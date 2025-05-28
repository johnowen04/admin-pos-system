<?php

namespace App\Enums;

enum UserRoleType: string
{
    case SUPERUSER = 'superuser';
    case EMPLOYEE = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::SUPERUSER => 'Super User',
            self::EMPLOYEE => 'employee',
        };
    }
}