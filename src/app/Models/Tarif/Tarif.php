<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;
use Carbon\Carbon;
use Config;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Modalite;

class Tarif extends BaseModel
{

    public $timestamps = false;

    protected $guarded = ['id'];

    /*
     * Relations
     */

    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    public function saison()
    {
        return $this->belongsTo(Saison::class);
    }

    public function duree()
    {
        return $this->belongsTo(Duree::class);
    }

    public function modalite()
    {
        return $this->belongsTo(Modalite::class);
    }




    /*
     * Scopes
     */


    public function scopeHasSaisonEnCoursOuFutur($query)
    {
        return $query->whereHas('saison', function ($q) {
            $q->where('fin_at', '>=', Carbon::now()->format('Y-m-d'));
        });
    }




    /*
     * Functions
     */

    public static function check()
    {
        $messages = null;

        $saisons_count = Saison::count();
        $durees_count = Duree::count();
        $categories_count = Categorie::count();
        $tarifs_count = Tarif::count();

        if ($saisons_count * $durees_count * $categories_count * 2 != $tarifs_count) {
            throw new TarifException("Des grilles de tarifs ne sont pas renseignées correctement");
        }

    }



    /*
     * Accessors & Mutators
     */


}
