<?php

namespace Projects\WellmedBackbone\Commands;

class InstallMakeCommand extends EnvironmentCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wellmed-backbone:install {type}
                            {--drop : Drop database before installing}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for initial installation of this module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dev_mode = config('micro-tenant.dev_mode');
        $provider = 'Projects\WellmedBackbone\WellmedBackboneServiceProvider';

        if (config('app.env') !== 'production') config(['micro-tenant.dev_mode' => true]);
        $this->call('optimize:clear');

        if ($this->option('drop')) {
            $this->comment('Drop database...');
            try {
                $default = config('database.default','pgsql');
                DB::statement("DROP DATABASE IF EXISTS " . config('database.connections.'.$default.'.database'));
            } catch (\Exception $e) {
                $this->warn('Error when drop database: ' . $e->getMessage());
            }
        }

        $this->comment('Installing Module...');
        $this->callSilent('vendor:publish', [
            '--provider' => $provider,
            '--tag'      => 'config'
        ]);
        $this->info('✔️  Created config/wellmed-backbone.php');

        $this->callSilent('vendor:publish', [
            '--provider' => $provider,
            '--tag'      => 'migrations'
        ]);
        $this->info('✔️  Created migrations');

        
        $direct_access = config('micro-tenant.direct_provider_access');
        config(['micro-tenant.direct_provider_access' => false]);
        $this->call('migrate');
        $this->call('db:seed');
        config(['micro-tenant.direct_provider_access' => $direct_access]);

        $type = $this->argument('type');
        $this->info('Wellmed Backbone Starterpack Seeding');
        $this->call('wellmed-backbone:seed', [
            'class' => $type == 'LITE' ? 'LiteDatabaseSeeder' : 'DatabaseSeeder'
        ]);
        $this->info('✔️  Wellmed Backbone Starterpack Seeded');

        $this->call('impersonate:cache');
        if ($type == 'LITE'){
            $this->info('Wellmed LITE Migrating');
            $this->call('wellmed-lite:migrate');
            $this->info('✔️  Wellmed LITE Migrated');
        }else{
            $this->info('Wellmed Plus Migrating');
            $this->call('wellmed-plus:migrate');
            $this->info('✔️  Wellmed Plus Migrated');
        }
        
        // $this->call('wellmed-plus:impersonate-migrate');

        $this->comment('projects/wellmed-backbone installed successfully.');
        config(['micro-tenant.dev_mode' => $dev_mode]);
    }
}
