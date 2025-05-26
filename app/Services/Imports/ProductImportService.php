<?php

namespace App\Services\Imports;

use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductImportService
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function import(array $rows): void
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $row['outlets'] = [1, 2]; // Assuming outlets are hardcoded for the import
                $this->productService->createProduct($row);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product import failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
