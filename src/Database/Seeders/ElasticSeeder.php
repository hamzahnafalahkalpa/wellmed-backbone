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

        $client = config('app.elasticsearch.client');
        $indexes = config('app.elasticsearch.indexes');
        $bulks = [
            'body' => []
        ];
        $this->syncData($indexes, 'Country', $bulks)
             ->syncData($indexes, 'Province', $bulks)
             ->syncData($indexes, 'District', $bulks)
             ->syncData($indexes, 'Subdistrict', $bulks)
             ->syncData($indexes, 'Village', $bulks);
        $results = $client->bulk($bulks);
    }

    private function syncData(array $indexes, string $model, array &$bulks): self{
        $datas = app(config('database.models.'.$model))->get();
        foreach ($datas as $data) {
            $resource = $data->toViewApi()->resolve();
            $bulks['body'][] = [
                'index' => [
                    '_index' => $indexes[Str::lower($model)]['full_name'],
                    '_id'    => $data->getKey(),
                ]
            ];

            $bulks['body'][] = $resource;
        }
        return $this;
    }
}
