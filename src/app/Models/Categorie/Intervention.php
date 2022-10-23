<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;

class Intervention extends BaseModel
{

    protected $guarded = ['id'];


    protected $casts = [
        'custom_fields' => AsCustomFieldsObject::class,
        'debut_at' => 'datetime:Y-m-d\TH:i',
        'fin_at' => 'datetime:Y-m-d\TH:i',
    ];



    /*
     * Relations
     */

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }

    public function type()
    {
        return $this->belongsTo(InterventionType::class);
    }



    /*
     * Scopes
     */

    public function scopeBetweenDates($query, CarbonInterface $debut_at, CarbonInterface $fin_at)
    {
        $debut_at->copy()->startOfDay();
        $fin_at->copy()->endOfDay();

        return $query->where(function ($query) use ($debut_at, $fin_at) {
            return $query->where(function ($query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '>=', $debut_at)->where('debut_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('fin_at', '>=', $debut_at)->where('fin_at', '<=', $fin_at);
            })->orWhere(function ($query) use ($debut_at, $fin_at) {
                $query->where('debut_at', '<=', $debut_at)->where('fin_at', '>=', $fin_at);
            });
        });
    }



    /*
     * Accessors & Mutators
     */




}
