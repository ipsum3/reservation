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
 * @property string|null $code
 * @property string|null $civilite
 * @property string $nom
 * @property string $prenom
 * @property string $email
 * @property int $has_login
 * @property string|null $telephone
 * @property string|null $adresse
 * @property string|null $cp
 * @property string|null $ville
 * @property int|null $pays_id
 * @property \Illuminate\Support\Carbon|null $naissance_at
 * @property string|null $naissance_lieu
 * @property string|null $permis_numero
 * @property \Illuminate\Support\Carbon|null $permis_at
 * @property string|null $permis_delivre
 * @property AsCustomFieldsObject|null $custom_fields
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Pays|null $pays
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Ipsum\Reservation\database\factories\ClientFactory factory($count = null, $state = [])
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

    protected static function booted()
    {
        self::created(function (self $client) {
            // Génération du code client
            $client->code = $client->generateClientCode($client->id);
            $client->save();
        });
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

    public function generateClientCode($id)
    {
        return str_pad($id, config('ipsum.reservation.numero_longueur'), "0", STR_PAD_LEFT);
    }
}
