<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class PrestationCollection  extends Collection implements Castable
{


    public function getById(int $id): ?Prestation
    {
        return $this->firstWhere(function ($value) use ($id) {
            return $value->id == $id;
        });
    }

    public function totalMontants(): ?float
    {
        return $this->sum(function (Prestation $prestation) {
            return $prestation->tarif;
        });
    }




    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return object|string
     */
    public static function castUsing(array $arguments)
    {
        return new class implements CastsAttributes
        {
            public function get($model, $key, $value, $attributes)
            {
                if (! array_key_exists($key, $attributes)) {
                    return new PrestationCollection();
                }

                $data = $attributes[$key] !== null ? json_decode($attributes[$key], true) : null;

                $datas = collect($data)->map(function ($item, $key) {
                    return new Prestation($item);
                })->all();
                return new PrestationCollection($datas);
            }

            public function set($model, $key, $value, $attributes)
            {
                if (is_object($value)) {
                    // Pour contourner un problème lié à la méthode get de la collection
                    return null;
                }


                $value = collect($value)->whereNotIn('quantite', [0])->all();

                return [$key => json_encode($value)];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return $value->getArrayCopy();
            }
        };
    }
}
