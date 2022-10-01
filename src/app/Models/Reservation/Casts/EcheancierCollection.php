<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class EcheancierCollection  extends Collection implements Castable
{



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
                    return new EcheancierCollection();
                }

                $data = $attributes[$key] !== null ? json_decode($attributes[$key], true) : null;

                $datas = collect($data)->map(function ($item, $key) {
                    return new Echeancier($item);
                })->all();
                return new EcheancierCollection($datas);
            }

            public function set($model, $key, $value, $attributes)
            {
                if (is_object($value)) {
                    // Pour contourner un problème lié à la méthode get de la collection
                    return null;
                }
                return [$key => json_encode($value)];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return $value->getArrayCopy();
            }
        };
    }
}
