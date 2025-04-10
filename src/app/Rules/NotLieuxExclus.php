<?php

namespace Ipsum\Reservation\app\Rules;

use Illuminate\Contracts\Validation\Rule;
use Ipsum\Reservation\app\Models\Categorie\Categorie;

class NotLieuxExclus implements Rule
{

    protected Categorie $categorie;

    public function __construct(Categorie $categorie)
    {
        $this->categorie = $categorie;
    }

    public function passes($attribute, $value): bool
    {
        return !$this->categorie->lieuxExclus->pluck('id')->contains((int) $value);
    }

    public function message(): string
    {
        return 'Ce lieu n’est pas autorisé pour la catégorie sélectionnée.';
    }
}
