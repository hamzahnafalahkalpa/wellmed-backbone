<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ApiAccessSeeder extends Seeder{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        // $workspace_uuid = request()->workspace_uuid ?? '9e7ff0f6-7679-46c8-ac3e-71da818160ff';
        // $workspace  = app(config('database.models.Workspace'))->uuid($workspace_uuid)->firstOrFail();
        // $api_access = app(config('database.models.ApiAccess'))
                    // ->where('reference_type',$workspace->getMorphClass())
                    // ->where('reference_id',$workspace->getKey())
                    // ->first();
        $api_access = app(config('database.models.ApiAccess'))
                        ->where('app_code',1)
                        ->first();
        if (!isset($api_access)){
            $exitCode = Artisan::call('helper:generate', [
                '--app-code'       => 1,
                '--algorithm'      => 'HS256',
                // '--reference-id'   => $workspace->getKey(),
                // '--reference-type' => $workspace->getMorphClass(),
                '--secret'         => 'YXYlGIbJ65VGjQnETWXoOiCvqpXg7PJu'
            ]);
    
            if ($exitCode !== 0) {
                $this->command->error('Failed generating API access.');
                return;
            }
        }
        $this->command->info('API access generated.');
    }
}