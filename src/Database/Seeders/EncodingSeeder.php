<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Database\Seeder;
use Projects\WellmedBackbone\Jobs\JobRequest;

class EncodingSeeder extends Seeder{
    use HasRequestData;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $data = JobRequest::all();
        $workspace_id = $data['workspace_id'];
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
