<?php

namespace SunnyFlail\ObjectCreator;

use SunnyFlail\ObjectCreator\Exceptions\UninitialisedCreatorException;
use SunnyFlail\ObjectCreator\Exceptions\ClassNotFoundException;
use InvalidArgumentException;
use ReflectionException;
use ReflectionClass;

final class ObjectCreator
{
    protected ?object $object = null;
    protected ReflectionClass $reflection;

    /**
     * Returns a copy of ObjectCreator with an object of provided class
     * 
     * @param string $classFQCN Fully qualified class name of the class
     * 
     * @return ObjectCreator copy of this
     * 
     * @throws ClassNotFoundException if class doesn't exist
     */
    public function create(string $classFQCN): ObjectCreator
    {
        $creator = clone $this;
        $classFQCN = '\\' . $classFQCN;

        try {
            $creator->reflection = new ReflectionClass($classFQCN);
        } catch (ReflectionException) {
            throw new ClassNotFoundException($classFQCN);
        }

        $creator->object = $creator->reflection->newInstanceWithoutConstructor();

        return $creator;
    }

    /**
     * Sets object property to provided value
     * 
     * @param string $propertyName Name of the property
     * @param mixed $value Value to set
     * 
     * @return ObjectCreator this
     * 
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function withProperty(string $propertyName, mixed $value): ObjectCreator
    {
        if ($this->object === null) {
            throw new UninitialisedCreatorException();
        }

        if (!$this->reflection->hasProperty($propertyName)) {
            throw new InvalidArgumentException(sprintf(
                "Trying to set non-existing property %s of class %s",
                $propertyName, $this->reflection->getName()
            ));
        }

        $property = $this->reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->object, $value);

        return $this;
    }

    /**
     * Returns an object with provided values
     * 
     * @return object
     * 
     * @throws Exception if object wasn't initialised beforehand
     */
    public function getObject(): object
    {
        if ($this->object === null) {
            throw new UninitialisedCreatorException();
        }
        
        return $this->object;
    }

}
