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
        $workspace_id = request()->workspace_id;
        foreach (config('module-encoding.encodings') as $encoding) {
            app(config('app.contracts.Encoding'))->prepareStoreEncoding(
                $this->requestDTO(config('app.contracts.EncodingData'),[
                    'label' => $encoding['label'],
                    'name' => $encoding['name'],
                    'model_has_encoding' => [
                        'reference_id' => $workspace_id,
                        'reference_type' => 'Workspace'
                    ]
                ])
            );
        }
    }
}
