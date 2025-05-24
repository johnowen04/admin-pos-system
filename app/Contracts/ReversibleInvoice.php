<?php

namespace App\Contracts;

interface ReversibleInvoice
{
    public function reversedQuantityFor($item): int;
}
