<?php

namespace Projects\WellmedBackbone\Imports;

use Projects\WellmedBackbone\Imports\BaseImport;
use Maatwebsite\Excel\Facades\Excel;

class Patient extends BaseImport{
    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        try {
            $support = app(config('app.contracts.Support'))->prepareStoreSupport(config('app.contracts.SupportData'),[
                'name'           => $attributes['name'],
                'reference_type' => $attributes['reference_type'],
                'reference_id' => $attributes['reference_id'],
                'paths' => [
                    $attributes['file']
                ]
            ]);
            dd($support);

            dispatch(new PatientImportJob($attributes))->onQueue('import')->onConnection('rabbitmq');
        } catch (\Throwable $th) {
            dd($th->getMessage());
            throw $th;
        }
        return response()->json([
            'message' => 'Seeder sedang dijalankan di background'
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