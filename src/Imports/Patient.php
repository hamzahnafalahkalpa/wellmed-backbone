<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Projects\WellmedBackbone\Imports\BaseImport;
use Illuminate\Support\Facades\Http;

class Patient extends BaseImport{
    use HasRequestData;

    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        try {
            $support = app(config(
                'app.contracts.Support'))->prepareStoreSupport($this->requestDTO(config('app.contracts.SupportData'),$attributes));
            $support_resource = $support->toViewApi()->resolve();
            $url = config('wellmed-backbone.listener.url').'patient';
            // $response = Http::withHeaders(array_merge(request()->headers->all(),[
            $headers = request()->headers->all();
            unset($headers['content-type']);
            $response = Http::withHeaders(array_merge($headers,[
                'Accept' => '*/*'
            ]))
            ->timeout(10)
            ->post($url, $support_resource);
            dd($response->body());
            // Kalau status bukan 2xx, lempar exception
            if ($response->failed()) {
                throw new \RuntimeException(
                    "Listener API call failed with status {$response->status()}: {$response->body()}"
                );
            }
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