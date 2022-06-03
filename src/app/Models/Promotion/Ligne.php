<?php

namespace Ipsum\Reservation\app\Models\Promotion;

use Ipsum\Core\app\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Config;

class Ligne extends BaseModel
{
    use SoftDeletingTrait;

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
        return $this->belongsTo('App\Promotion\Promotion');
    }

    public function option()
    {
        return $this->belongsTo('App\Option\Option');
    }

    public function categorie()
    {
        return $this->belongsTo('App\Categorie\Categorie');
    }

    public function categorieSurclassement()
    {
        return $this->belongsTo('App\Categorie\Categorie');
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
