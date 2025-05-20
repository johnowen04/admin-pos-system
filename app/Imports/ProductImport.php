<?php

namespace App\Imports;

use App\Services\Imports\ProductImportService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProductImport implements ToCollection, WithHeadingRow
{
    protected ProductImportService $importService;

    public function __construct(ProductImportService $importService)
    {
        $this->importService = $importService;
    }

    public function collection(Collection $rows)
    {
        // Use ->toArray() to pass a simple array of rows to your import service
        $this->importService->import($rows->toArray());
    }
}
