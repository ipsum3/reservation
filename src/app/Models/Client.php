<?php

namespace Ipsum\Reservation\app\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ipsum\Admin\app\Casts\AsCustomFieldsObject;
use Ipsum\Reservation\app\Models\Reservation\Pays;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Ipsum\Reservation\database\factories\ClientFactory;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string|null $civilite
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property string $telephone
 * @property string $adresse
 * @property string $cp
 * @property string $ville
 * @property int $pays_id
 * @property \Illuminate\Support\Carbon $naissance_at
 * @property string $naissance_lieu
 * @property string $permis_numero
 * @property \Illuminate\Support\Carbon $permis_at
 * @property string $permis_delivre
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Pays|null $pays
 * @property-read \Illuminate\Database\Eloquent\Collection|Reservation[] $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @mixin \Eloquent
 */
class Client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'naissance_at' => 'datetime',
        'permis_at' => 'datetime',
        'custom_fields' => AsCustomFieldsObject::class,
    ];



    protected static function newFactory()
    {
        return ClientFactory::new();
    }




    /*
     * Relations
     */

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }



    /*
     * Scopes
     */




    /*
     * Accessors & Mutators
     */
}
