<?php

namespace Ipsum\Reservation\app\Models\Lieu;


use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;

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

    public function scopeDate($query, Carbon $date, $ferie = false)
    {
        return $query->where('jour', $ferie ? self::JOUR_FERIE : $date->dayOfWeek)
            ->where('debut', '<=', $date->format('H:i:s'))
            ->where('fin', '>=', $date->format('H:i:s'));
    }

    public function scopeCreneaux($query, Carbon $date, $ferie = false)
    {
        return $query->where('jour', $ferie ? self::JOUR_FERIE : $date->dayOfWeek);

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
