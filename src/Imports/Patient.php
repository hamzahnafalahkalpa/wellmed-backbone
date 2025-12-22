<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Projects\WellmedBackbone\Imports\BaseImport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Patient extends BaseImport{
    use HasRequestData;

    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        $file = $attributes['files'][0];
        if (!$file->isValid()){
            throw new \Exception('Uploaded file is not valid: '.$file->getErrorMessage());
        }
        $support_model = app(config('database.models.Support'));
        if (isset($attributes['chunk_index'])){
            $support_model = $support_model->findOrFail($attributes['id']);
            if (isset($attributes['id'])){
                if ($attributes['chunk_index'] > $support_model->total_chunks) throw new \Exception('Invalid chunk index');
                
                // Validate filename matches
                $clientFilename = $file->getClientOriginalName();
                $filenameWithoutExt = pathinfo($clientFilename, PATHINFO_FILENAME);
                $expectedFilename = $attributes['filename'] ?? null;
                
                if ($expectedFilename && $filenameWithoutExt !== $expectedFilename) {
                    throw new \Exception('Filename mismatch. Expected: ' . $expectedFilename . ', Got: ' . $filenameWithoutExt);
                }
                
                if ($attributes['chunk_index'] == $support_model->total_chunks - 1) {
                    $expectedPattern = 'part' . ($attributes['chunk_index'] + 1);
                    if (!preg_match('/^part\d+$/', $filenameWithoutExt)) {
                        throw new \Exception('Invalid file extension pattern. Expected: ' . $expectedPattern);
                    }
                    if ($support_model->progress + $file->getSize() > $support_model->total_size) {
                        throw new \Exception('File size exceeds total size');
                    }
                    $attributes['status'] = 'COMPLETED';
                }
            }else{
                $attributes['status'] = 'PROCESSING';
            }
            $attributes['progress'] = ($support_model->progress ?? 0) + $file->getSize();
        }
        $attributes['name'] ??= $support_model->name ?? 'Upload pasien import '.date('Y-m-d H:i:s');
        $attributes['upload_id'] ??= $support_model->upload_id ?? Str::orderedUuid()->toString();
        $attributes['target_path'] ??= $support_model->target_path ?? '/support/'.$attributes['upload_id'];
        $support_model = app(config('app.contracts.Support'))->storeSupport($this->requestDTO(config('app.contracts.SupportData'),$attributes));
        // $url = config('wellmed-backbone.listener.url').'patient';
        // // $response = Http::withHeaders(array_merge(request()->headers->all(),[
        // $headers = request()->headers->all();
        // unset($headers['content-type']);
        // $response = Http::withHeaders(array_merge($headers,[
        //     'Accept' => '*/*'
        // ]))
        // ->timeout(10)
        // ->post($url, $support_resource);
        // // Kalau status bukan 2xx, lempar exception
        // if ($response->failed()) {
        //     throw new \RuntimeException(
        //         "Listener API call failed with status {$response->status()}: {$response->body()}"
        //     );
        // }
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