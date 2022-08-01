<?php

namespace Ipsum\Reservation\app\Models\Promotion;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

/**
 * Ipsum\Reservation\app\Models\Promotion\Promotion
 *
 * @property-read mixed $active
 * @property-read mixed $en_cours
 * @property-read Lieu|null $lieu
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Promotion\Ligne[] $lignes
 * @property-read int|null $lignes_count
 * @property-write mixed $activation_at
 * @property-write mixed $desactivation_at
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion active()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion affichable()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion enCours()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion valide($debut_at, $fin_at, $lieu_id, $code)
 * @method static \Illuminate\Database\Eloquent\Builder|Promotion visible()
 * @mixin \Eloquent
 */
class Promotion extends BaseModel
{

    protected $table = 'promotion';

    protected $fillable = array('lieu_id', 'type', 'reference', 'nom', 'code', 'extrait', 'debut_at', 'fin_at', 'activation_at', 'desactivation_at', 'duree_minimum', 'duree_maximum');

    protected $nullable = ['lieu_id', 'code', 'activation_at', 'desactivation_at', 'duree_minimum', 'duree_maximum'];

    static public $TYPES = ['reduction', 'compte'];

    public static function getRulesWithoutLocal()
    {
        $rules = array(
            "lieu_id"          => "integer|exists:lieu,id",
            "type"             => "required|in:reduction,compte",
            "reference"        => "required|max:255",
            "nom"              => "required|max:255",
            "code"             => "max:255",
            "debut_at"         => "required|date_format:d/m/Y",
            "fin_at"           => "required|date_format:d/m/Y|date_greater_than:debut_at,d/m/Y",
            "activation_at"    => "date_format:d/m/Y",
            "desactivation_at" => "date_format:d/m/Y",
            "duree_minimum"    => "integer",
            "duree_maximum"    => "integer",
        );
        return $rules;
    }



    /*
     * Relations
     */

    public function lignes()
    {
        return $this->hasMany(Ligne::class);
    }

    public function lieu()
    {
        return $this->belongsTo(Lieu::class);
    }


    /*
     * Scopes
     */

    public function scopeValide($query, $debut_at, $fin_at, $lieu_id, $code)
    {
        $debut_at->copy()->startOfDay();
        $fin_at->copy()->startOfDay();

        return $query->active()
            ->where('debut_at', '<=', $debut_at)
            ->where('fin_at', '>=', $fin_at)
            ->where(function ($query) use ($debut_at, $fin_at) {
                $query->where('duree_minimum', '<=', $debut_at->diffInDays($fin_at))->orWhereNull('duree_minimum');
            })
            ->where(function ($query) use ($debut_at, $fin_at) {
                $query->where('duree_maximum', '>=', $debut_at->diffInDays($fin_at))->orWhereNull('duree_maximum');
            })
            ->where(function ($query) use ($lieu_id) {
                $query->where('lieu_id', $lieu_id)->orWhereNull('lieu_id');
            })
            ->where('code', $code);
    }

    public function scopeActive($query)
    {
        $date = Carbon::now();

        return $query->where(function ($query) use ($date) {
            $query->where('activation_at', '<=', $date->startOfDay())->orWhereNull('activation_at');
        })
            ->where(function ($query) use ($date) {
                $query->where('desactivation_at', '>=', $date->startOfDay())->orWhereNull('desactivation_at');
            });
    }

    public function scopeVisible($query)
    {
        return $query;
    }

    public function scopeEnCours($query)
    {
        return $query->active()->where('fin_at', '>=', Carbon::now()->startOfDay());
    }

    public function scopeAffichable($query)
    {
        return $query->enCours()->visible();
    }


    /*
     * Accessors & Mutators
     */

    /*public function setDebutAtAttribute($value)
    {
        $this->attributes['debut_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }

    public function setFinAtAttribute($value)
    {
        $this->attributes['fin_at'] = Carbon::createFromFormat('d/m/Y', $value);
    }*/

    public function setActivationAtAttribute($value)
    {
        $this->attributes['activation_at'] = $value  ? Carbon::createFromFormat('d/m/Y', $value) : null;
    }

    public function setDesactivationAtAttribute($value)
    {
        $this->attributes['desactivation_at'] = $value ? Carbon::createFromFormat('d/m/Y', $value) : null;
    }

    public function getDates()
    {
        return array('debut_at', 'fin_at', 'activation_at', 'desactivation_at');
    }


    public function getActiveAttribute()
    {
        $date = Carbon::now();
        return ($this->activation_at <= $date->startOfDay() or $this->activation_at == null) and ($this->desactivation_at >= $date->startOfDay() or $this->desactivation_at == null);
    }

    public function getEnCoursAttribute()
    {
        return $this->active and $this->fin_at >= Carbon::now()->startOfDay();
    }

}
