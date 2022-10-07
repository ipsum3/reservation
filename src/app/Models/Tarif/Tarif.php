<?php

namespace Ipsum\Reservation\app\Models\Tarif;

use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Reservation\Condition;

/**
 * Ipsum\Reservation\app\Models\Tarif\Tarif
 *
 * @property int $id
 * @property int $categorie_id
 * @property int $duree_id
 * @property int|null $saison_id
 * @property string|null $montant
 * @property string|null $condition_paiement_id
 * @property-read Categorie|null $categorie
 * @property-read Condition|null $condition
 * @property-read \Ipsum\Reservation\app\Models\Tarif\Duree|null $duree
 * @property-read \Ipsum\Reservation\app\Models\Tarif\Saison|null $saison
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif hasSaisonEnCoursOuFutur()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tarif query()
 * @mixin \Eloquent
 */
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

    public function condition()
    {
        return $this->belongsTo(Condition::class);
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
