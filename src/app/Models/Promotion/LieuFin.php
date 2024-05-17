<?php

namespace Ipsum\Reservation\app\Models\Promotion;

use Ipsum\Reservation\app\Models\Lieu\Lieu;


/**
 * Ipsum\Reservation\app\Models\Promotion\LieuFin
 *
 * @property int $id
 * @property string $slug
 * @property int $type_id
 * @property int $is_actif
 * @property string $nom
 * @property string $telephone
 * @property string $adresse
 * @property string|null $instruction
 * @property string $horaires_txt
 * @property string|null $gps
 * @property array $emails
 * @property array $emails_reservation
 * @property \Ipsum\Admin\app\Casts\AsCustomFieldsObject|null $custom_fields
 * @property int $order
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $email_first
 * @property-read mixed $email_reservation_first
 * @property-read mixed $is_aeroport
 * @property-read mixed $lat
 * @property-read mixed $lng
 * @property-read mixed $tag_meta_description
 * @property-read mixed $tag_title
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Core\app\Models\Translate> $translates
 * @property-read int|null $translates_count
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu agences()
 * @method static \Illuminate\Database\Eloquent\Builder|Lieu filtreSortable($objet)
 * @method static \Illuminate\Database\Eloquent\Builder|LieuFin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LieuFin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LieuFin query()
 * @mixin \Eloquent
 */
class LieuFin extends Lieu
{

}
