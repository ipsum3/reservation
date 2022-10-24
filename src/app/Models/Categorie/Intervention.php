<?php

namespace Ipsum\Reservation\app\Models\Categorie;

use Carbon\CarbonInterface;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;

/**
 * Ipsum\Reservation\app\Models\Categorie\Intervention
 *
 * @property int $id
 * @property int $vehicule_id
 * @property int $type_id
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property string|null $intervenant
 * @property string|null $information
 * @property string|null $cout
 * @property mixed|null $custom_fields
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Ipsum\Reservation\app\Models\Categorie\InterventionType|null $type
 * @property-read \Ipsum\Reservation\app\Models\Categorie\Vehicule|null $vehicule
 * @method static \Illuminate\Database\Eloquent\Builder|Intervention betweenDates(\Carbon\CarbonInterface $debut_at, \Carbon\CarbonInterface $fin_at)
 * @method static \Illuminate\Database\Eloquent\Builder|Intervention newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Intervention newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Intervention query()
 * @mixin \Eloquent
 */
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
