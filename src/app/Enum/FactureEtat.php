<?php

namespace Ipsum\Reservation\app\Enum;

enum FactureEtat: string
{
    case BROUILLON = 'brouillon';
    case ENCOURS = 'en_cours';
    case PAYEE = 'payee';

    public function label(): string
    {
        return match($this) {
            self::BROUILLON => 'Brouillon',
            self::ENCOURS => 'En cours',
            self::PAYEE => 'Payée',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::BROUILLON => 'badge-light',
            self::ENCOURS => 'badge-info',
            self::PAYEE => 'badge-success',
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