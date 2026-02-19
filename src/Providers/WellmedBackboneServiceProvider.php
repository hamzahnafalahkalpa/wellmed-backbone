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

    /**
     * Static flag to track if Elasticsearch has been initialized in this worker
     * This prevents re-initialization in Octane context
     */
    protected static bool $elasticsearchInitialized = false;

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
                $bootStart = microtime(true);
                $timings = [];

                $projectName = 'wellmed-backbone';
                $config = config($projectName);

                // Check if valid cache exists first
                $t = microtime(true);
                $cacheExists = $this->hasValidSetupCache($projectName);
                $timings['cache_check'] = round((microtime(true) - $t) * 1000, 2);

                if (false && $cacheExists) {
                    // Register providers first (they will set their morphMaps)
                    $t = microtime(true);
                    $this->registerProvidersFromCache($config['packages'], $projectName);
                    $timings['register_providers'] = round((microtime(true) - $t) * 1000, 2);

                    $t = microtime(true);
                    $this->registerOverideConfig($projectName, __DIR__.'/../'.$config['libs']['config']);
                    $timings['override_config'] = round((microtime(true) - $t) * 1000, 2);

                    // Then apply cached setup LAST to override with project-level models
                    $t = microtime(true);

                    $this->loadFromSetupCache($projectName, $config);
                    $timings['load_cache'] = round((microtime(true) - $t) * 1000, 2);
                } else {
                    // Traditional filesystem scanning
                    $t = microtime(true);
                    $this->registers([
                        '*',
                        'Provider' => function() use ($config, $projectName) {
                            if (config('micro-tenant.direct_provider_access')) {
                                $migration_path = __DIR__.'/../'.$config['libs']['migration'] ?? 'Migrations';
                            }

                            $this->bootedRegisters($config['packages'], $projectName, $migration_path ?? null);
                            $this->registerOverideConfig($projectName, __DIR__.'/../'.$config['libs']['config']);
                        },
                        'Model', 'Database'
                    ]);
                    $timings['registers'] = round((microtime(true) - $t) * 1000, 2);

                    $t = microtime(true);
                    $this->autoBinds();
                    $timings['auto_binds'] = round((microtime(true) - $t) * 1000, 2);

                    // Generate cache for next request (if auto-generate is enabled)
                    if (config('laravel-support.setup_cache.auto_generate', true)) {
                        $t = microtime(true);
                        $this->generateSetupCacheIfEnabled($projectName, $config);
                        $timings['generate_cache'] = round((microtime(true) - $t) * 1000, 2);
                    }
                }

                $t = microtime(true);
                $this->registerRouteService(RouteServiceProvider::class);
                $timings['route_service'] = round((microtime(true) - $t) * 1000, 2);

                $this->app->singleton(PathRegistry::class, function() {
                    $registry = new PathRegistry();

                    $config = config("wellmed-backbone");
                    foreach ($config['libs'] as $key => $lib) $registry->set($key, 'projects'.$lib);
                    return $registry;
                });

                View::addNamespace('wellmed', base_path('vendor/projects/wellmed-backbone/src/Resources/Views'));

                $t = microtime(true);
                $this->initializeElasticsearch();
                $timings['elasticsearch'] = round((microtime(true) - $t) * 1000, 2);

                $t = microtime(true);
                $this->initializeRabbitMQQueues();
                $timings['rabbitmq'] = round((microtime(true) - $t) * 1000, 2);

                $timings['total'] = round((microtime(true) - $bootStart) * 1000, 2);
                
                // Log timing if enabled
                if (env('SETUP_PROFILE_BOOT', false)) {
                    \Log::info('[BootProfile] WellmedBackbone timing', $timings);
                }
            } catch (\Throwable $th) {
                dd($th->getMessage());
                \Log::error('WellmedBackbone boot error: ' . $th->getMessage(), [
                    'exception' => $th,
                    'trace' => $th->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Load models and contracts from Redis setup cache
     *
     * @param string $projectName
     * @param array $config
     * @return void
     */
    protected function loadFromSetupCache(string $projectName, array $config): void
    {
        try {
            $cache = $this->initSetupCache($projectName);
            $setup = $cache->getOrGenerate(function () {
                // This shouldn't be called since we checked cache exists
                return ['models' => [], 'contracts' => [], 'providers' => []];
            });
            $cache->apply($setup);

            \Log::debug("[SetupCache] Loaded from cache: {$projectName}");
        } catch (\Throwable $e) {
            \Log::warning("Failed to load from setup cache: {$e->getMessage()}");
        }
    }

    /**
     * Generate setup cache after traditional setup is complete
     *
     * @param string $projectName
     * @param array $config
     * @return void
     */
    protected function generateSetupCacheIfEnabled(string $projectName, array $config): void
    {
        if (!$this->shouldUseSetupCache()) {
            return;
        }

        try {
            $cache = $this->initSetupCache($projectName);
            $builder = $this->initSetupBuilder();
            $packages = $config['packages'] ?? [];

            // Build and cache the setup data from current state
            $setup = $builder->buildSetupData($projectName, $packages, static::class);
            $setup['version'] = $cache->getVersion();
            $setup['generated_at'] = now()->toIso8601String();

            // Apply immediately to current request (creates short name mappings)
            $cache->apply($setup);

            // Save to Redis for next request
            $cache->regenerate(function () use ($setup) {
                return $setup;
            });

            \Log::info("[SetupCache] Generated and applied cache for: {$projectName}");
        } catch (\Throwable $e) {
            \Log::warning("Failed to generate setup cache: {$e->getMessage()}");
        }
    }

    /**
     * Register service providers when using cache
     * (Models and contracts are already loaded from cache, but providers still need registration)
     *
     * @param array $packages
     * @param string $configName
     * @return void
     */
    protected function registerProvidersFromCache(array $packages, string $configName): void
    {
        foreach ($packages as $package) {
            if (!isset($package['provider'])) {
                continue;
            }

            $provider = preg_replace('/\\\\+/', '\\', $package['provider']);

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
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

        // Skip if already initialized in this worker (static flag persists across Octane requests)
        if (static::$elasticsearchInitialized) {
            return;
        }

        // Also skip if singleton already exists and resolved
        if ($this->app->bound('elasticsearch') && $this->app->resolved('elasticsearch')) {
            static::$elasticsearchInitialized = true;
            return;
        }

        $hosts = config('elasticsearch.hosts', ['localhost:9200']);
        if (!isset($hosts)) {
            return;
        }

        try {
            // Register client as singleton (LAZY - only creates connection when actually used)
            $this->app->singleton('elasticsearch', function () use ($hosts) {
                return ClientBuilder::create()
                    ->setHosts(is_array($hosts) ? $hosts : [$hosts])
                    ->setApiKey(
                        config('elasticsearch.username', 'elastic'),
                        config('elasticsearch.password', 'password')
                    )
                    ->setSSLVerification(env('ELASTICSEARCH_SSL_VERIFY', false) === 'true')
                    ->setRetries(2)
                    ->build();
            });

            // Skip eager initialization if disabled (RECOMMENDED for performance)
            // Set ELASTICSEARCH_EAGER_INIT=false to skip index checking at boot
            if (!env('ELASTICSEARCH_EAGER_INIT', true)) {
                static::$elasticsearchInitialized = true;
                return;
            }

            // Eager initialization: create client and check indexes NOW
            // This adds ~300-400ms to boot time but ensures indexes exist
            $client = $this->app->make('elasticsearch');

            // Index creation - only run once during initial boot
            $prefix = config('elasticsearch.prefix', config('app.env', 'development'));
            $separator = config('elasticsearch.separator', '.');

            foreach (config('elasticsearch.indices', []) as $index_key => $index_config) {
                $full_index_name = $prefix . $separator . $index_config['name'];

                try {
                    if (!$client->indices()->exists(['index' => $full_index_name])->asBool()) {
                        $client->indices()->create(['index' => $full_index_name]);
                    }
                } catch (\Throwable $e) {
                    \Log::warning("Failed to create ES index {$full_index_name}: {$e->getMessage()}");
                }
            }

            // Mark as initialized for this worker
            static::$elasticsearchInitialized = true;

        } catch (\Throwable $e) {
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
