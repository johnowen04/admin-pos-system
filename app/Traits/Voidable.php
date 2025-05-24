<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\StockMovementService;

trait Voidable
{
    public function void(string $reason, int $voidedById, StockMovementService $stockService): void
    {
        if ($this->is_voided) {
            throw new Exception('This record is already voided.');
        }

        DB::transaction(function () use ($reason, $voidedById, $stockService) {
            $this->update([
                'is_voided'   => true,
                'void_reason' => $reason,
                'voided_by'   => $voidedById,
                'voided_at'   => now(),
            ]);

            $stockService->reverseInvoice($this, $voidedById, $reason);
        });
    }

    public function isVoidable(): bool
    {
        return !$this->is_voided;
    }
}
