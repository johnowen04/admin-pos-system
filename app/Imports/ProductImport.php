<?php

namespace App\Imports;

use App\Services\Imports\ProductImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToArray, WithHeadingRow, WithChunkReading, ShouldQueue
{
    public function array(array $rows)
    {
        $importService = app(ProductImportService::class);
        $importService->import($rows);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
