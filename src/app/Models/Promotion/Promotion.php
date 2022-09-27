<?php

namespace Ipsum\Reservation\app\Models\Promotion;

use Illuminate\Database\Eloquent\Builder;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Core\Concerns\Slug;
use Ipsum\Reservation\app\Classes\Carbon;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;
use Ipsum\Reservation\app\Models\Prestation\Prestation;
use Ipsum\Reservation\app\Models\Reservation\Modalite;

/**
 * Ipsum\Reservation\app\Models\Promotion\Promotion
 *
 * @property int $id
 * @property string $slug
 * @property int|null $client_id
 * @property string $type
 * @property string|null $reference
 * @property string $nom
 * @property string|null $extrait
 * @property string|null $texte
 * @property int|null $modalite_paiement_id
 * @property string|null $code
 * @property \Illuminate\Support\Carbon $debut_at
 * @property \Illuminate\Support\Carbon $fin_at
 * @property \Illuminate\Support\Carbon|null $activation_at
 * @property \Illuminate\Support\Carbon|null $desactivation_at
 * @property int|null $duree_min
 * @property int|null $duree_max
 * @property string $reduction_type
 * @property string|null $reduction_valeur
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Categorie[] $categories
 * @property-read int|null $categories_count
 * @property-read bool $is_active
 * @property-read bool $is_en_cours
 * @property-read \Illuminate\Database\Eloquent\Collection|Lieu[] $lieux
 * @property-read int|null $lieux_count
 * @property-read Modalite|null $modalite
 * @property-read \Illuminate\Database\Eloquent\Collection|Prestation[] $prestations
 * @property-read int|null $prestations_count
 * @method static Builder|Promotion active()
 * @method static Builder|Promotion enCours()
 * @method static Builder|Promotion newModelQuery()
 * @method static Builder|Promotion newQuery()
 * @method static Builder|Promotion query()
 * @mixin \Eloquent
 */
class Promotion extends BaseModel
{
    use Slug;

    protected $guarded = ['id'];

    protected $slugBase = 'nom';


    const REDUCTION_TYPES = ['pourcentage' => 'Pourcentage', 'montant' => 'Montant'];


    /*
     * Relations
     */

    public function categories()
    {
        return $this->morphedByMany(Categorie::class, 'promotionable')->withPivot(['reduction']);
    }

    public function prestations()
    {
        return $this->morphedByMany(Prestation::class, 'promotionable')->withPivot(['reduction']);
    }

    public function lieux()
    {
        return $this->morphedByMany(Lieu::class, 'promotionable')->withPivot(['reduction']);
    }

    public function modalite()
    {
        return $this->belongsTo(Modalite::class, 'modalite_paiement_id');
    }



    /*
     * Scopes
     */

    public function scopeActive(Builder|self $query): void
    {
        $now = Carbon::now();

        // Activation
        $query->where(function (Builder $query) use ($now) {
            $query->where('activation_at', '<=', $now->startOfDay())->orWhereNull('activation_at');
        })
        ->where(function (Builder $query) use ($now) {
            $query->where('desactivation_at', '>=', $now->startOfDay())->orWhereNull('desactivation_at');
        });
    }

    public function scopeEnCours(Builder|self $query): void
    {
        $query->active()->where('fin_at', '>=', Carbon::now()->startOfDay());
    }




    /*
     * Accessors & Mutators
     */


    public function getDates()
    {
        return array('debut_at', 'fin_at', 'activation_at', 'desactivation_at');
    }


    public function getIsActiveAttribute(): bool
    {
        $date = Carbon::now();
        return ($this->activation_at <= $date->startOfDay() or $this->activation_at == null) and ($this->desactivation_at >= $date->startOfDay() or $this->desactivation_at == null);
    }

    public function getIsEnCoursAttribute(): bool
    {
        return $this->is_active and $this->fin_at >= Carbon::now()->startOfDay();
    }

}
