<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequest;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;

class EncodingSeeder extends Seeder{
    use HasRequestData;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $workspace  = app(config('database.models.Workspace'))->uuid('9e7ff0f6-7679-46c8-ac3e-71da818160ff')->firstOrFail();        
        foreach (config('module-encoding.encodings') as $encoding) {
            app(config('app.contracts.Encoding'))->prepareStoreEncoding(
                $this->requestDTO(config('app.contracts.EncodingData'),[
                    'label' => $encoding['label'],
                    'name' => $encoding['name'],
                    'model_has_encoding' => [
                        'reference_id' => $workspace->getKey(),
                        'reference_type' => $workspace->getMorphClass()
                    ]
                ])
            );
        }
    }
}
