<?php

namespace Projects\WellmedBackbone\Providers;

use Hanafalah\LaravelSupport\{
    Concerns\NowYouSeeMe,
    Supports\PathRegistry
};
use Projects\WellmedBackbone\{
    WellmedBackbone,
    Contracts,
    Facades
};
use Projects\WellmedBackbone\Contracts\Supports\ConnectionManager;
use Projects\WellmedBackbone\Supports\ConnectionManager as SupportsConnectionManager;

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
                // 'Provider' => function(){
                //     $config = config('wellmed-backbone');
                //     $this->bootedRegisters($config['packages'], 'wellmed-backbone', __DIR__.'/../'.$config['libs']['migration'] ?? 'Migrations');
                //     $this->registerOverideConfig('wellmed-backbone',__DIR__.'/../'.$config['libs']['config']);
                // }
                // 'Config' => function() {
                //     $this->__config_wellmed_backbone = config('wellmed-backbone');
                // },
                // 'Provider' => function(){
                //     $this->registerOverideConfig('wellmed-backbone',__DIR__.'/../'.$this->__config_wellmed_backbone['libs']['config']);
                // }
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
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }
        });
    }    
}
