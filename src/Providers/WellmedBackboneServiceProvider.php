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

                if (config('elasticsearch.enabled', false)) {
                    $hosts = config('app.elasticsearch.hosts','localhost:9200');
                    if (isset($hosts)){
                        $client = ClientBuilder::create()->setHosts($hosts)
                            ->setApiKey(
                                config('app.elasticsearch.username','elastic'),
                                config('app.elasticsearch.password','password')
                            )
                            ->setSSLVerification(env('ELASTICSEARCH_SSL_VERIFY',false) === 'true')
                            ->build();
                        config(['app.elasticsearch.client' => $client]);
                        $this->app->singleton('elasticsearch', function () use ($client) {
                            return $client;
                        });
                        foreach (config('app.elasticsearch.indexes',[]) as $index_key => $index_config){
                            $full_index_name =
                                config('elasticsearch.prefix', 'development')
                                .config('elasticsearch.separator', '.')
                                .$index_config['name'];
                            config(['app.elasticsearch.indexes.'.$index_key.'.full_name' => $full_index_name]);
                            if ($client->indices()->exists(['index' => $full_index_name])->asBool()) {
                                continue;
                            }
                            $client->indices()->create([
                                'index' => $full_index_name
                            ]);
                        }
                    }
                }

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
     * Initialize RabbitMQ queues with proper connection handling
     *
     * @return void
     */
    protected function initializeRabbitMQQueues(): void
    {
        $maxRetries = 3;
        $retryDelay = 1; // seconds

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $connection = null;
            $channel = null;

            try {
                // Create connection with heartbeat and timeout configuration
                $connection = new AMQPStreamConnection(
                    env('RABBITMQ_HOST'),
                    env('RABBITMQ_PORT'),
                    env('RABBITMQ_USER'),
                    env('RABBITMQ_PASSWORD'),
                    '/',
                    false, // insist
                    'AMQPLAIN', // login method
                    null, // login response
                    'en_US', // locale
                    env('RABBITMQ_CONNECTION_TIMEOUT', 3.0), // connection timeout
                    env('RABBITMQ_READ_WRITE_TIMEOUT', 3.0), // read/write timeout
                    null, // context
                    env('RABBITMQ_KEEPALIVE', false), // keepalive
                    env('RABBITMQ_HEARTBEAT', 60) // heartbeat interval in seconds
                );

                $channel = $connection->channel();

                // Declare queues
                foreach (['default', 'installation', 'elasticsearch', 'import', 'export'] as $queue) {
                    $channel->queue_declare($queue, false, true, false, false);
                }

                // Success - close connections and return
                if ($channel) {
                    $channel->close();
                }
                if ($connection) {
                    $connection->close();
                }

                // Log success if this was a retry
                if ($attempt > 1) {
                    \Log::info("RabbitMQ queue initialization succeeded on attempt {$attempt}");
                }

                return;

            } catch (\PhpAmqpLib\Exception\AMQPHeartbeatMissedException $e) {
                \Log::warning("RabbitMQ heartbeat missed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");

                // Cleanup on error
                $this->cleanupRabbitMQConnection($channel, $connection);

                if ($attempt < $maxRetries) {
                    sleep($retryDelay * $attempt); // Exponential backoff
                    continue;
                }

                throw $e;

            } catch (\PhpAmqpLib\Exception\AMQPChannelClosedException $e) {
                \Log::warning("RabbitMQ channel closed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");

                // Cleanup on error
                $this->cleanupRabbitMQConnection($channel, $connection);

                if ($attempt < $maxRetries) {
                    sleep($retryDelay * $attempt);
                    continue;
                }

                throw $e;

            } catch (\PhpAmqpLib\Exception\AMQPConnectionClosedException $e) {
                \Log::warning("RabbitMQ connection closed on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}");

                // Cleanup on error
                $this->cleanupRabbitMQConnection($channel, $connection);

                if ($attempt < $maxRetries) {
                    sleep($retryDelay * $attempt);
                    continue;
                }

                throw $e;

            } catch (\Throwable $e) {
                \Log::error("RabbitMQ initialization error on attempt {$attempt}/{$maxRetries}: {$e->getMessage()}", [
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);

                // Cleanup on error
                $this->cleanupRabbitMQConnection($channel, $connection);

                if ($attempt < $maxRetries) {
                    sleep($retryDelay * $attempt);
                    continue;
                }

                throw $e;
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
