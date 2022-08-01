<?php

namespace Ipsum\Reservation\app\Classes;



use Ipsum\Reservation\app\Models\Lieu\Ferie;
use Ipsum\Reservation\app\Models\Lieu\Lieu;

class Carbon extends \Carbon\Carbon
{

    protected $is_ferie = null;
    protected $_lieu = null;


    public function isFerie(Lieu $lieu): bool
    {
        $this->is_ferie = (is_null($this->is_ferie) or $this->_lieu !== $lieu) ? Ferie::isFerie($this->copy(), $lieu) : $this->is_ferie;
        $this->_lieu = $lieu;
        return $this->is_ferie;
    }


    public function dayOfWeekWithFerie(Lieu $lieu): int
    {
        return $this->isFerie($lieu) ? 7 : $this->dayOfWeek;
    }


}

