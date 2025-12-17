<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Projects\WellmedBackbone\Imports\BaseImport;

class Patient extends BaseImport{
    use HasRequestData;

    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        try {
            $support = app(config('app.contracts.Support'))->prepareStoreSupport($this->requestDTO(config('app.contracts.SupportData'),$attributes));
            // dd($support->toViewApi()->resolve());
            // dispatch(new PatientImportJob($attributes))->onQueue('import')->onConnection('rabbitmq');
        } catch (\Throwable $th) {
            // dd($th->getMessage());
            throw $th;
        }
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