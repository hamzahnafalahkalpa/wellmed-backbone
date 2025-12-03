<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class InfrastructureSeeder extends Seeder
{
    use HasRequestData;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $building = $this->schema('Building',[
            'name' => 'Gedung Baru'
        ]);
    }

    private function model($entity){
        return app(config('database.models.'.$entity));
    }

    private function schema($entity,$attributes): Model{
        return app(config('app.contracts.'.$entity))->{'prepareStore'.$entity}($this->requestDto(config('app.contracts.'.$entity.'Data'),$attributes));
    }
}