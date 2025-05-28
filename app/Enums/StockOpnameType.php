<?php

namespace App\Enums;

enum StockOpnameType: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case ADJUSTED = 'adjusted';
}