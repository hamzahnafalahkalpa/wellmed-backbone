<?php

namespace Projects\WellmedBackbone\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ElasticSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "[DEBUG] Booting ".class_basename($this)."\n";

        $client = app('elasticsearch');
        $indices = config('elasticsearch.indices');
        $prefix = config('elasticsearch.prefix', config('app.env', 'development'));
        $separator = config('elasticsearch.separator', '.');

        $bulks = [
            'body' => []
        ];
        $this->syncData($indices, $prefix, $separator, 'Country', $bulks)
             ->syncData($indices, $prefix, $separator, 'Province', $bulks)
             ->syncData($indices, $prefix, $separator, 'District', $bulks)
             ->syncData($indices, $prefix, $separator, 'Subdistrict', $bulks)
             ->syncData($indices, $prefix, $separator, 'Village', $bulks);
        $results = $client->bulk($bulks);
    }

    private function syncData(array $indices, string $prefix, string $separator, string $model, array &$bulks): self{
        $datas = app(config('database.models.'.$model))->get();
        $indexKey = Str::lower($model);
        $indexName = $indices[$indexKey]['name'] ?? $indexKey;
        $fullIndexName = $prefix . $separator . $indexName;

        foreach ($datas as $data) {
            $resource = $data->toViewApi()->resolve();
            try {
                $bulks['body'][] = [
                    'index' => [
                        '_index' => $fullIndexName,
                        '_id'    => $data->getKey(),
                    ]
                ];
            } catch (\Throwable $th) {
                // dd($indexKey, $fullIndexName);
                //throw $th;
            }

            $bulks['body'][] = $resource;
        }
        return $this;
    }
}
