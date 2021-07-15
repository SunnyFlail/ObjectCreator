<?php

namespace SunnyFlail\ObjectCreatorTests;

class Entity
{

    private string $string;
    private array $array;

    public function get(string $property): mixed
    {
        return $this->$property;
    }

}