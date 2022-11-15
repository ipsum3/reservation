<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Carbon\CarbonInterface;
use Ipsum\Core\app\Models\BaseModel;

/**
 * Ipsum\Reservation\app\Models\Lieu\Horaire
 *
 * @property int $id
 * @property int $lieu_id
 * @property int $jour
 * @property string $debut
 * @property string $fin
 * @property-read mixed $creneau_to_string
 * @property-read \Ipsum\Reservation\app\Models\Lieu\Lieu|null $lieu
 * @method static \Illuminate\Database\Eloquent\Builder|Horaire creneaux(\Carbon\CarbonInterface $date, $is_ferie = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Horaire date(\Carbon\CarbonInterface $date, $is_ferie = false)
 * @method static \Illuminate\Database\Eloquent\Builder|Horaire newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Horaire newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Horaire query()
 * @mixin \Eloquent
 */
class Horaire extends BaseModel
{


    public $timestamps = false;

    const JOURS = [0 => 'dimanche', 1 => 'lundi', 2 => 'mardi', 3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi', 6 => 'samedi', 7 => 'ferié'];
    const JOUR_FERIE = 7;

    protected $guarded = ['id'];




    /*
     * Relations
     */

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }



    /*
     * Scopes
     */

    public function scopeDate($query, CarbonInterface $date, $is_ferie = false)
    {
        return $query->where('jour', $is_ferie ? self::JOUR_FERIE : $date->dayOfWeek)
            ->where('debut', '<=', $date->format('H:i:s'))
            ->where('fin', '>=', $date->format('H:i:s'));
    }

    public function scopeCreneaux($query, CarbonInterface $date, $is_ferie = false)
    {
        return $query->where('jour', $is_ferie ? self::JOUR_FERIE : $date->dayOfWeek);

    }



    /*
     * Accessors & Mutators
     */

    public function getCreneauToStringAttribute()
    {
        return 'de '. str_replace(':', 'h', $this->debut) . ' à ' . str_replace(':', 'h', $this->fin);
    }

    public function getDebutAttribute()
    {
        return substr($this->attributes['debut'], 0, -3);
    }

    public function getFinAttribute()
    {
        return substr($this->attributes['fin'], 0, -3);
    }

}
