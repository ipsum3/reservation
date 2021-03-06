<?php

namespace Ipsum\Reservation\app\Models\Prestation;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Ipsum\Admin\Concerns\Sortable;
use Ipsum\Core\app\Models\BaseModel;
use Exception;
use Config;
use Ipsum\Reservation\app\Models\Categorie\Categorie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

/**
 * Ipsum\Reservation\app\Models\Prestation\Prestation
 *
 * @property int $id
 * @property int $type_id
 * @property string $tarification
 * @property string|null $class
 * @property string $nom
 * @property string|null $description
 * @property string|null $montant
 * @property int $quantite_max
 * @property int|null $gratuit_apres
 * @property int|null $jour_fact_max
 * @property int|null $age_max
 * @property string|null $heure_max
 * @property string|null $heure_min
 * @property int|null $jour
 * @property int $order
 * @property string|null $condition
 * @property-read \Illuminate\Database\Eloquent\Collection|\Ipsum\Reservation\app\Models\Prestation\Blocage[] $blocages
 * @property-read int|null $blocages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Categorie[] $categories
 * @property-read int|null $categories_count
 * @property-read bool $is_obligatoire
 * @property-read bool $is_optionnelle
 * @property-read bool $is_tarification_agence
 * @property-read \Illuminate\Database\Eloquent\Collection|Lieu[] $lieux
 * @property-read int|null $lieux_count
 * @property-read \Ipsum\Reservation\app\Models\Prestation\Type|null $type
 * @method static Builder|Prestation condition(\Ipsum\Reservation\app\Models\Categorie\Categorie $categorie, \Ipsum\Reservation\app\Models\Lieu\Lieu $lieu_debut, \Ipsum\Reservation\app\Models\Lieu\Lieu $lieu_fin, \Carbon\Carbon $debut_at, \Carbon\Carbon $fin_at, ?int $age = null)
 * @method static Builder|Prestation filtreSortable($objet)
 * @method static Builder|Prestation newModelQuery()
 * @method static Builder|Prestation newQuery()
 * @method static Builder|Prestation obligatoire()
 * @method static Builder|Prestation optionnelle()
 * @method static Builder|Prestation query()
 * @method static Builder|Prestation withoutBlocage($debut_at, $fin_at)
 * @mixin \Eloquent
 */
class Prestation extends BaseModel
{
    use Sortable;

    public $timestamps = false;

    public static $LISTE_TARIFICATION = array('jour', 'forfait', 'agence');
    public static $LISTE_CONDITION = array('depart' => 'Uniquement sur le d??part', 'retour' => 'Uniquement sur le retour');


    protected $guarded = ['id'];


    protected static function booted()
    {
        static::deleting(function (self $prestation) {
            $prestation->blocages()->delete();
            $prestation->categories()->detach();
            $prestation->lieux()->detach();
        });
    }


    /*
     * Relations
     */

    public function blocages()
    {
        return $this->hasMany(Blocage::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function categories()
    {
        return $this->morphedByMany(Categorie::class, 'prestable')->withPivot('montant');
    }

    public function lieux()
    {
        return $this->morphedByMany(Lieu::class, 'prestable')->withPivot('montant');
    }


    /*
     * Scopes
     */

    public function scopeWithoutBlocage(Builder $query, $debut_at, $fin_at)
    {
        $query->whereDoesntHave('blocages', function ($query) use ($debut_at, $fin_at) {
            $query->betweenDates($debut_at, $fin_at);
        });
    }

    public function scopeOptionnelle(Builder $query)
    {
        $query->where('type_id', '!=', Type::FRAIS_ID);
    }

    public function scopeObligatoire(Builder $query)
    {
        $query->where('type_id', Type::FRAIS_ID);
    }

    public function scopeCondition(Builder $query, Categorie $categorie, Lieu $lieu_debut, Lieu $lieu_fin, Carbon $debut_at, Carbon $fin_at, ?int $age = null)
    {
        // Cat??gories
        $query->where(function (Builder $query) use ($categorie) {
            $query->whereHas('categories', function (Builder $query) use ($categorie) {
                $query->where('id', $categorie->id);
            })->orWhereDoesntHave('categories');
        })

            // Lieux
            ->where(function (Builder $query) use ($lieu_debut, $lieu_fin) {
                $query->whereHas('lieux', function (Builder $query) use ($lieu_debut, $lieu_fin) {
                    $query->where(function (Builder $query) use ($lieu_debut) {
                        $query->where('id', $lieu_debut->id)->where(function (Builder $query) {
                            $query->where('condition', '!=', 'retour')->orWhereNull('condition');
                        });
                    })->orWhere(function (Builder $query) use ($lieu_fin) {
                        $query->where('id', $lieu_fin->id)->where(function (Builder $query) {
                            $query->where('condition', '!=', 'depart')->orWhereNull('condition');
                        });
                    });
                })->orWhereDoesntHave('lieux');
            })

            // Horaires
            ->where(function (Builder $query) use ($debut_at, $fin_at) {
                $query->where('heure_max', '>', $debut_at->toTimeString())->orWhere('heure_max', '>', $fin_at->toTimeString())
                    ->orWhere('heure_min', '<', $debut_at->toTimeString())->orWhere('heure_min', '<', $fin_at->toTimeString())
                    ->orWhere(function (Builder $query) {
                        $query->whereNull('heure_max')->whereNull('heure_min');
                    });
            })

            // Jour de la semaine
            ->where(function (Builder $query) use ($debut_at, $fin_at) {
                $query->where('jour', $debut_at->dayOfWeek)->orWhere('jour', $fin_at->dayOfWeek)->orWhereNull('jour');
            });

        // Age
        if ($age !== null) {
            $query->where(function (Builder $query) use ($age) {
                $query->where('age_max', '>', $age)->orWhereNull('age_max');
            });
        } else {
            $query->whereNull('age_max');
        }
    }





    /*
     * Functions
     */



    /*
     * Accessors & Mutators
     */

    public function getIsTarificationAgenceAttribute(): bool
    {
        return $this->tarification === 'agence';
    }

    public function getIsOptionnelleAttribute(): bool
    {
        return !in_array($this->type_id, [Type::FRAIS_ID]);
    }

    public function getIsObligatoireAttribute(): bool
    {
        return !$this->is_optionnelle;
    }

    public function getHeureMaxAttribute()
    {
        return substr($this->attributes['heure_max'], 0, -3);
    }

    public function getHeureMinAttribute()
    {
        return substr($this->attributes['heure_min'], 0, -3);
    }


}
