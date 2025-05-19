<?php

namespace App\Enums;

enum RoleLevel: int
{
    case SUPER = 100;
    case HIGH = 80;
    case MID = 60;
    case LOW = 40;
    case NONE = 0;

    public function label(): string
    {
        return match ($this) {
            self::SUPER => 'Super User',
            self::HIGH => 'Admin Level',
            self::MID => 'Manager Level',
            self::LOW => 'Staff Level',
            self::NONE => 'Guest Level',
        };
    }
}
