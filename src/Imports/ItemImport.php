<?php

namespace Projects\WellmedBackbone\Imports;

use App\Middlewares\EncodingWrapper;
use Hanafalah\LaravelSupport\Concerns\Support\HasRequestData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\{
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow
};
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ItemImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    WithStartRow
{
    use HasRequestData;

    protected array $seenMr  = [];
    protected array $seenNik = [];

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function collection(Collection $rows)
    {
        app(EncodingWrapper::class)->installationSetup();
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // biar sesuai excel

            /**
             * ======================
             * 1. VALIDASI WAJIB
             * ======================
             */
            if (!isset($row['nama'])) {
                $this->logSkip($rowNumber, 'nama kosong');
                continue;
            }

            /**
             * ======================
             * 2. DEDUP DALAM FILE
             * ======================
             */
            if (isset($row['nama'])) {
                if (isset($this->seenNik[$row['nama']])) {
                    $this->logSkip($rowNumber, 'duplicate Name dalam file', $row['nama']);
                    continue;
                }
                $this->seenNik[$row['nama']] = true;
            }

            /**
             * ======================
             * 3. STORE
             * ======================
             */
            try {
                app(config('app.contracts.Item'))->prepareStoreItem(
                    $this->requestDTO(config('app.contracts.ItemData'), [
                        'id' => null,
                        'barcode' => null,
                        'name' => $row['nama'],
                        'net_unit_id' => null,
                        'net_qty' => null,
                        'cogs' => (int) ($row['cogs'] ?? 0),
                        'margin' => (int) ($row['margin'] ?? 0),
                        'unit' => [
                            'id' => null,
                            'name' => $row['satuan']
                        ],
                        'reference_type' => (string) $row['type'],
                        'min_stock' => 1000,
                        'tax' => 0,
                        'netto' => null,
                        'item_stock' => [
                            'stock' => null
                        ],
                        'reference' => [
                            'nama'  => $row['nama'],
                        ],
                        'compositions' => []
                    ])
                );

                Log::channel('import')->info('Item imported', [
                    'row' => $rowNumber,
                    'name' => $row['nama'],
                ]);
            } catch (\Throwable $e) {
                Log::channel('import')->error('Failed import item', [
                    'row' => $rowNumber,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    protected function logSkip(int $row, string $reason, $value = null): void
    {
        Log::channel('import')->warning('Skipping item row', [
            'row'    => $row,
            'reason' => $reason,
            'value'  => $value,
        ]);
    }
}
