<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Admin\app\Models\Admin;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Dommage\Dommage;
use Ipsum\Reservation\app\Models\Reservation\Reservation;
use Str;


/**
 * Ipsum\Reservation\app\Models\Inspection\Inspection
 *
 * @property int $id
 * @property int $reservation_id
 * @property int $admin_id
 * @property int $type_id
 * @property int|null $kilometrage
 * @property int|null $carburant
 * @property string|null $locataire_signature
 * @property \Illuminate\Support\Carbon|null $locataire_signature_at
 * @property string|null $agent_signature
 * @property \Illuminate\Support\Carbon|null $agent_signature_at
 * @property string|null $observations
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Admin|null $admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Reservation\app\Models\Inspection\Checklist> $checklists
 * @property-read int|null $checklists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Dommage> $dommages
 * @property-read int|null $dommages_count
 * @property-read string $document_path
 * @property-read string $document_public_file_name
 * @property-read \Ipsum\Media\app\Models\Media|null $illustration
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Ipsum\Media\app\Models\Media> $medias
 * @property-read int|null $medias_count
 * @property-read Reservation|null $reservation
 * @property-read \Ipsum\Reservation\app\Models\Inspection\Type|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|Inspection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inspection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inspection query()
 * @mixin \Eloquent
 */
class Inspection extends BaseModel
{
    use Mediable;

    protected $casts = [
        'agent_signature_at' => 'datetime:Y-m-d\TH:i',
        'locataire_signature_at' => 'datetime:Y-m-d\TH:i',
    ];

    protected $guarded = ['id'];


    /*
     * Scopes
     */

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function dommages()
    {
        return $this->hasMany(Dommage::class);
    }

    public function checklists()
    {
        return $this->belongsToMany(Checklist::class, 'checklist-inspections')
            ->withPivot('value')
            ->withTimestamps();
    }


    /*
     * Functions
     */

    public function isSigned(): bool
    {
        return $this->isLocataireSigned() && $this->isAgentSigned();
    }

    public function isLocataireSigned(): bool
    {
        return !empty($this->locataire_signature);
    }

    public function isAgentSigned(): bool
    {
        return !empty($this->agent_signature);
    }



    /*
     * Accessors & Mutators
     */

    public function getDocumentPathAttribute(): string
    {
        return storage_path("app/inspections/etat_des_lieux-{$this->id}.pdf");
    }

    public function getDocumentPublicFileNameAttribute(): string
    {
        return 'etat-des-lieux-'.$this->reservation_id.'-'.Str::slug($this->type->nom).'.pdf';
    }

}
