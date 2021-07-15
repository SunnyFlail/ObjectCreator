<?php

namespace SunnyFlail\ObjectCreator;

use SunnyFlail\ObjectCreator\Exceptions\UninitialisedCreatorException;
use SunnyFlail\ObjectCreator\Exceptions\ClassNotFoundException;
use InvalidArgumentException;

/**
 * Interface for object creators
 */
interface IObjectCreator
{

    /**
     * Returns a copy of ObjectCreator with an object of provided class
     * 
     * @param string $classFQCN Fully qualified class name of the class
     * 
     * @return IObjectCreator copy of this
     * 
     * @throws ClassNotFoundException if class doesn't exist
     */
    public function create(string $classFQCN): IObjectCreator;

    /**
     * Sets object property to provided value
     * 
     * @param string $propertyName Name of the property
     * @param mixed $value Value to set
     * 
     * @return IObjectCreator this
     * 
     * @throws UninitialisedCreatorException
     * @throws InvalidArgumentException
     */
    public function withProperty(string $propertyName, mixed $value): IObjectCreator;

    /**
     * Returns an object with provided values
     * 
     * @return object
     * 
     * @throws UninitialisedCreatorException if object wasn't initialised beforehand
     */
    public function getObject(): object;

}