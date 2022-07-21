<?php

namespace Ipsum\Reservation\app\Models\Promotion;

use Ipsum\Core\app\Models\BaseModel;
use Config;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Prestation\Prestation;

/**
 * Ipsum\Reservation\app\Models\Promotion\Ligne
 *
 * @property-read Categorie|null $categorie
 * @property-read Categorie|null $categorieSurclassement
 * @property-read mixed $reduction
 * @property-read Prestation|null $prestation
 * @property-read \Ipsum\Reservation\app\Models\Promotion\Promotion|null $promotion
 * @method static \Illuminate\Database\Eloquent\Builder|Ligne newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ligne newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ligne query()
 * @mixin \Eloquent
 */
class Ligne extends BaseModel
{

    protected $table = 'promotion_ligne';

    protected $nullable = ['categorie_id', 'option_id', 'reduction_eur', 'reduction_usd', 'surclassement_categorie_nom', 'rachat_franchise'];

    protected $fillable = ['categorie_id', 'option_id', 'reduction_eur', 'reduction_usd', 'surclassement_categorie_nom', 'rachat_franchise'];

    public $timestamps=false;

    public static function getRules()
    {
        $rules = array(
            "categorie_id" => "integer|exists:categorie,id",
            "option_id" => "integer|exists:option,id",
            "reduction" => "numeric",
            "surclassement_categorie_id" => "integer|exists:categorie,id",
            "rachat_franchise" => "",
        );
        return $rules;
    }





    public static function boot()
    {
        parent::boot();

        self::deleting(function ($ligne) {
            $ligne->tarifs()->delete();
        });
    }



    /*
     * Relations
     */

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }

    public function prestation()
    {
        return $this->belongsTo(Prestation::class);
    }

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function categorieSurclassement()
    {
        return $this->belongsTo(Categorie::class);
    }



    /*
     * Scopes
     */





    /*
     * Accessors & Mutators
     */

    public function getReductionAttribute()
    {
        return $this->{'reduction_'.Config::get('app.devise')};
    }
}
