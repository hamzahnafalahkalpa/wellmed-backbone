<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Hanafalah\MicroTenant\Facades\MicroTenant;
use Projects\WellmedBackbone\Imports\BaseImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Patient extends BaseImport{
    use HasRequestData;

    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        // // optional
        // DB::setDefaultConnection('tenant');

        $support_attribute = $attributes['support'] ?? [];
        if (isset($support_attribute['files'])){
            $file = $support_attribute['files'][0];
            if (!$file->isValid()){
                throw new \Exception('Uploaded file is not valid: '.$file->getErrorMessage());
            }
        }
        // \Log::channel('import')->info('Using support id', ['id' => $attributes['id']]);
        // $support_model = app(config('database.models.Support'))->findOrFail($attributes['id']);
        // $support_resource = $support_model->toViewApi()->resolve();

        // Process data in chunks and dispatch to RabbitMQ
        dispatch(new \Projects\WellmedBackbone\Jobs\PatientImportJob($attributes))->onQueue('import')->onConnection('rabbitmq');
        return response()->json([
            'message' => 'Import sedang berjalan di latar belakang.',
        ]);


        // $file = $attributes['file'] ?? null;
        
        // if (!$file) {
        //     throw new \Exception('File is required');
        // }

        // $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray, \Maatwebsite\Excel\Concerns\WithStartRow {
        //     public function startRow(): int {
        //         return 2;
        //     }
            
        //     public function array(array $array) {}
        // }, $file)[0];

        // // Process data in chunks and dispatch to RabbitMQ
        // foreach (array_chunk($data, 100) as $chunk) {
        //     dispatch(new \Projects\WellmedBackbone\Jobs\ProcessPatientImport($chunk));
        // }

        // return [
        //     'success' => true,
        //     'total_rows' => count($data)
        // ];
    }
}