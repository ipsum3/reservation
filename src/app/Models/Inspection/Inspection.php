<?php

namespace Ipsum\Reservation\app\Models\Inspection;

use Ipsum\Admin\app\Models\Admin;
use Ipsum\Core\app\Models\BaseModel;
use Ipsum\Media\Concerns\Mediable;
use Ipsum\Reservation\app\Models\Dommage\Dommage;
use Ipsum\Reservation\app\Models\Reservation\Reservation;


class Inspection extends BaseModel
{
    use Mediable;

    protected $casts = [
        'agent_signature_at' => 'datetime:Y-m-d\TH:i',
        'locataire_signature_at' => 'datetime:Y-m-d\TH:i',
    ];

    protected $guarded = ['id'];

    protected $htmlable = ['observations'];

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

    public function isSigned(): bool
    {
        return (bool) ($this->locataire_signature && $this->agent_signature);
    }

    public function isLocataireSigned(): bool
    {
        return !empty($this->locataire_signature);
    }

    public function isAgentSigned(): bool
    {
        return !empty($this->agent_signature);
    }

}
