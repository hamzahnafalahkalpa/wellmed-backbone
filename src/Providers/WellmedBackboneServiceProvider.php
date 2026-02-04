<?php

namespace Projects\WellmedBackbone\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Hanafalah\LaravelSupport\{
    Concerns\NowYouSeeMe,
    Supports\PathRegistry
};
use Hanafalah\ModuleWorkspace\Facades\Workspace;
use Projects\WellmedBackbone\{
    WellmedBackbone
};
use Projects\WellmedBackbone\Contracts\Supports\ConnectionManager;
use Projects\WellmedBackbone\Supports\ConnectionManager as SupportsConnectionManager;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Illuminate\Support\Facades\View;

class WellmedBackboneServiceProvider extends WellmedBackboneEnvironment
{
    use NowYouSeeMe;

    public function register()
    {
        $this->registerMainClass(WellmedBackbone::class,false)
            ->registerCommandService(CommandServiceProvider::class)
            ->registers([
                '*',
                'Services' => function(){
                    $this->binds([
                        ConnectionManager::class => SupportsConnectionManager::class
                    ]);   
                }
            ]);
    }

    public function boot(){        
        $this->registerModel();
        $this->app->booted(function(){
            try {
                $this->registers([
                    '*', 
                    'Provider' => function(){
                        $config = config('wellmed-backbone');
                        if (config('micro-tenant.direct_provider_access')) {
                            $migration_path = __DIR__.'/../'.$config['libs']['migration'] ?? 'Migrations';
                        }

                        $this->bootedRegisters($config['packages'], 'wellmed-backbone', $migration_path ?? null);
                        $this->registerOverideConfig('wellmed-backbone',__DIR__.'/../'.$config['libs']['config']);
                    },
                    'Model', 'Database'
                ]);
                $this->autoBinds();
                $this->registerRouteService(RouteServiceProvider::class);
                $this->app->singleton(PathRegistry::class, function() {
                    $registry = new PathRegistry();

                    $config = config("wellmed-backbone");
                    foreach ($config['libs'] as $key => $lib) $registry->set($key, 'projects'.$lib);
                    
                    return $registry;
                });

                View::addNamespace('wellmed', base_path('vendor/projects/wellmed-backbone/src/Resources/Views'));

                $this->initializeElasticsearch();

                $this->initializeRabbitMQQueues();
            } catch (\Throwable $th) {
                \Log::error('WellmedBackbone boot error: ' . $th->getMessage(), [
                    'exception' => $th,
                    'trace' => $th->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Initialize Elasticsearch client
     *
     * NOTE: This is a singleton - only created once per worker, not per request.
     * The client is reused across requests in Octane context.
     *
     * @return void
     */
    protected function initializeElasticsearch(): void
    {
        if (!config('elasticsearch.enabled', false)) {
            return;
        }

        // Skip if already initialized (Octane worker context)
        if ($this->app->bound('elasticsearch')) {
            return;
        }

        $hosts = config('app.elasticsearch.hosts', 'localhost:9200');
        if (!isset($hosts)) {
            return;
        }

        try {
            // Create client as singleton - reused across requests
            $this->app->singleton('elasticsearch', function () use ($hosts) {
                return ClientBuilder::create()
                    ->setHosts(is_array($hosts) ? $hosts : [$hosts])
                    ->setApiKey(
                        config('app.elasticsearch.username', 'elastic'),
                        config('app.elasticsearch.password', 'password')
                    )
                    ->setSSLVerification(env('ELASTICSEARCH_SSL_VERIFY', false) === 'true')
                    ->setRetries(2)
                    ->build();
            });

            $client = $this->app->make('elasticsearch');
            config(['app.elasticsearch.client' => $client]);

            // Index creation - only run once during initial boot (not in Octane worker)
            if (!app()->bound('octane.cacheTable')) {
                foreach (config('app.elasticsearch.indexes', []) as $index_key => $index_config) {
                    $full_index_name =
                        config('elasticsearch.prefix', 'development')
                        . config('elasticsearch.separator', '.')
                        . $index_config['name'];

                    config(['app.elasticsearch.indexes.' . $index_key . '.full_name' => $full_index_name]);

                    try {
                        if (!$client->indices()->exists(['index' => $full_index_name])->asBool()) {
                            $client->indices()->create(['index' => $full_index_name]);
                        }
                    } catch (\Throwable $e) {
                        \Log::warning("Failed to create ES index {$full_index_name}: {$e->getMessage()}");
                    }
                }
            }
        } catch (\Throwable $e) {
            dd($e->getMessage());
            \Log::error('Elasticsearch initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Initialize RabbitMQ queues with proper connection handling
     *
     * NOTE: This only runs once during initial boot, not on every Octane request.
     * The laravel-queue-rabbitmq package also auto-declares queues when workers start.
     *
     * @return void
     */
    protected function initializeRabbitMQQueues(): void
    {
        // Skip if disabled via config
        if (!env('RABBITMQ_DECLARE_QUEUES_ON_BOOT', false)) {
            return;
        }

        // Skip if running in Octane worker (queues already declared on initial boot)
        if (app()->bound('octane.cacheTable')) {
            return;
        }

        // Skip if running in queue worker context
        if (app()->runningInConsole() && str_contains(implode(' ', $_SERVER['argv'] ?? []), 'queue:work')) {
            return;
        }

        $maxRetries = 3;
        $retryDelay = 2; // seconds

        // Heartbeat value - read/write timeout must be at least 2x this
        $heartbeat = (int) env('RABBITMQ_HEARTBEAT', 60);
        $connectionTimeout = (float) env('RABBITMQ_CONNECTION_TIMEOUT', 10.0);
        // Read/write timeout must be > 2 * heartbeat to avoid channel closed errors
        $readWriteTimeout = (float) env('RABBITMQ_READ_WRITE_TIMEOUT', max(130.0, $heartbeat * 2 + 10));

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $connection = null;
            $channel = null;

            try {
                // Create connection with proper timeout configuration
                $connection = new AMQPStreamConnection(
                    env('RABBITMQ_HOST', '127.0.0.1'),
                    (int) env('RABBITMQ_PORT', 5672),
                    env('RABBITMQ_USER', 'guest'),
                    env('RABBITMQ_PASSWORD', 'guest'),
                    env('RABBITMQ_VHOST', '/'),
                    false, // insist
                    'AMQPLAIN', // login method
                    null, // login response
                    'en_US', // locale
                    $connectionTimeout,
                    $readWriteTimeout,
                    null, // context
                    (bool) env('RABBITMQ_KEEPALIVE', false),
                    $heartbeat
                );

                $channel = $connection->channel();

                // Declare queues (idempotent operation)
                $queues = ['default', 'installation', 'elasticsearch', 'billing', 'satusehat', 'xendit', 'import', 'export'];
                foreach ($queues as $queue) {
                    $channel->queue_declare($queue, false, true, false, false);
                }

                // Success - close connections and return
                if ($channel && $channel->is_open()) {
                    $channel->close();
                }
                if ($connection && $connection->isConnected()) {
                    $connection->close();
                }

                if ($attempt > 1) {
                    \Log::info("RabbitMQ queue initialization succeeded on attempt {$attempt}");
                }

                return;

            } catch (\PhpAmqpLib\Exception\AMQPHeartbeatMissedException $e) {
                \Log::warning("RabbitMQ heartbeat missed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");
                $this->cleanupRabbitMQConnection($channel, $connection);

            } catch (\PhpAmqpLib\Exception\AMQPChannelClosedException $e) {
                \Log::warning("RabbitMQ channel closed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");
                $this->cleanupRabbitMQConnection($channel, $connection);

            } catch (\PhpAmqpLib\Exception\AMQPConnectionClosedException $e) {
                \Log::warning("RabbitMQ connection closed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");
                $this->cleanupRabbitMQConnection($channel, $connection);

            } catch (\Throwable $e) {
                \Log::warning("RabbitMQ initialization error on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");
                $this->cleanupRabbitMQConnection($channel, $connection);
            }

            // Retry with exponential backoff (but don't throw on final failure - just log)
            if ($attempt < $maxRetries) {
                sleep($retryDelay * $attempt);
            } else {
                \Log::error("RabbitMQ queue initialization failed after {$maxRetries} attempts. Queues will be declared by workers.");
            }
        }
    }

    /**
     * Safely cleanup RabbitMQ channel and connection
     *
     * @param mixed $channel
     * @param mixed $connection
     * @return void
     */
    protected function cleanupRabbitMQConnection($channel, $connection): void
    {
        try {
            if ($channel && $channel->is_open()) {
                $channel->close();
            }
        } catch (\Throwable $e) {
            \Log::debug("Error closing RabbitMQ channel: {$e->getMessage()}");
        }

        try {
            if ($connection && $connection->isConnected()) {
                $connection->close();
            }
        } catch (\Throwable $e) {
            \Log::debug("Error closing RabbitMQ connection: {$e->getMessage()}");
        }
    }
}
