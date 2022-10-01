<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class PromotionCollection  extends Collection implements Castable
{


    public function getById(int $id): ?Promotion
    {
        return $this->firstWhere(function ($value) use ($id) {
            return $value->id == $id;
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
                    return new PromotionCollection();
                }

                $data = $attributes[$key] !== null ? json_decode($attributes[$key], true) : null;

                $datas = collect($data)->map(function ($item, $key) {
                    return new Promotion($item);
                })->all();
                return new PromotionCollection($datas);
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
