<?php

namespace Projects\WellmedBackbone\Imports;

use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Projects\WellmedBackbone\Imports\BaseImport;

class Item extends BaseImport{
    use HasRequestData;

    public function handle(?array $attributes = []){
        $attributes ??= request()->all();
        $support_attribute = $attributes['support'] ?? [];
        if (isset($support_attribute['files'])){
            $file = $support_attribute['files'][0];
            if (!$file->isValid()){
                throw new \Exception('Uploaded file is not valid: '.$file->getErrorMessage());
            }
        }
        dispatch(new \Projects\WellmedBackbone\Jobs\ItemImportJob($attributes))->onQueue('import')->onConnection('rabbitmq');
        return response()->json([
            'message' => 'Import sedang berjalan di latar belakang.',
        ]);
    }
}