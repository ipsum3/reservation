<?php

namespace Ipsum\Reservation\app\Enum;

enum FactureEtat: string
{
    case BROUILLON = 'brouillon';
    case VALIDEE = 'validee';

    public function label(): string
    {
        return match($this) {
            self::BROUILLON => 'brouillon',
            self::VALIDEE => 'validée',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::BROUILLON => 'badge-warning',
            self::VALIDEE => 'badge-success',
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