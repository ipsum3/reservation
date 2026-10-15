<?php

namespace Ipsum\Reservation\app\Enum;

enum FactureType: string
{
    case LOCATION = 'location';
    case ADDITIONNELLE = 'additionnelle';

    public function label(): string
    {
        return match($this) {
            self::LOCATION => 'location',
            self::ADDITIONNELLE => 'additionnelle',
        };
    }

    static function pluck(): array
    {
        $types = [];
        foreach (self::cases() as $case) {
            $types[$case->value] = $case->label();
        }
        return $types;
    }

}