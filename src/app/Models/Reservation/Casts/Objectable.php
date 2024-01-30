<?php

namespace Ipsum\Reservation\app\Models\Reservation\Casts;

trait Objectable
{

    protected $attributes = [];

    public function __construct( ?array $attributes = [] ) {

        $this->attributes = $attributes;

        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }
    }

    public function __get( $name ) {
        if (method_exists($this, 'get' . lcfirst($name) . 'Attribute')) {
            return $this->{'get' . lcfirst($name) . 'Attribute'}();
        } elseif (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        if (method_exists($this, 'set' . lcfirst($name) . 'Attribute')) {
            $this->{'set' . lcfirst($name) . 'Attribute'}($value);
        } else {
            $this->attributes[$name] = $value;
        }
    }

    public function toArray()
    {
        return $this->attributes;
    }

}
