<?php

namespace App\Enums;

enum PositionLevel: int
{
    case OWNER = 100;
    case ADMIN = 80;
    case MANAGER = 60;
    case STAFF = 40;
    case NONE = 0;

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner User',
            self::ADMIN => 'Admin Level',
            self::MANAGER => 'Manager Level',
            self::STAFF => 'Staff Level',
            self::NONE => 'Guest Level',
        };
    }
}
