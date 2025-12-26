<?php

namespace Projects\WellmedBackbone\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Hanafalah\LaravelSupport\{
    Concerns\NowYouSeeMe,
    Supports\PathRegistry
};
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
        $databaseName = config('database.connections.central.database');
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

                if (config('app.elasticsearch.enabled', false)) {
                    $hosts = config('app.elasticsearch.hosts','localhost:9200');
                    if (isset($hosts)){
                        $client = ClientBuilder::create()->setHosts($hosts)
                            ->setApiKey(
                                config('app.elasticsearch.username','elastic'),
                                config('app.elasticsearch.password','password')
                            )
                            ->setSSLVerification(env('ELASTICSEARCH_SSL_VERIFY') === 'true')
                            ->build();
                        config(['app.elasticsearch.client' => $client]);
                        foreach (config('app.elasticsearch.indexes',[]) as $index_key => $index_config){
                            $full_index_name = 
                                config('app.elasticsearch.index_prefix', 'development')
                                .config('app.elasticsearch.index_separator', '.')
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

                $connection = new AMQPStreamConnection(
                    env('RABBITMQ_HOST'),
                    env('RABBITMQ_PORT'),
                    env('RABBITMQ_USER'),
                    env('RABBITMQ_PASSWORD'),
                    '/'
                );

                $channel = $connection->channel();

                foreach (['default', 'installation', 'elasticsearch', 'import', 'export'] as $queue) {
                    $channel->queue_declare($queue, false, true, false, false);
                }

                $channel->close();
                $connection->close();
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }
        });
    }    
}
