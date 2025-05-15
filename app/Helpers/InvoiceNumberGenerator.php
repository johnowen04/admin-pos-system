<?php
namespace App\Helpers;

class InvoiceNumberGenerator
{
    public static function generate(string $prefix, string $model)
    {
        $currentDate = now()->format('Ymd');
        $lastInvoice = $model::where('invoice_number', 'like', "$prefix/$currentDate/%")
            ->orderBy('created_at', 'desc')
            ->first();

        $lastSequence = $lastInvoice ? intval(substr($lastInvoice->invoice_number, -3)) : 0;
        $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);

        return "$prefix/$currentDate/$newSequence";
    }
}