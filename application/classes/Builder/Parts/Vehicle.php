<?php

namespace Builder\Parts;

/**
 * VehicleInterface is a contract for a vehicle
 */
abstract class Vehicle
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setPart($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function getParts() {
        $result = array();
        foreach( (array) $this->data as $key => $row ) :
            $result[] = $key;
        endforeach;

        return $result;
    }
}