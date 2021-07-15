<?php

namespace SunnyFlail\ObjectCreator;

use SunnyFlail\ObjectCreator\Exceptions\UninitialisedCreatorException;
use SunnyFlail\ObjectCreator\Exceptions\ClassNotFoundException;
use InvalidArgumentException;
use ReflectionException;
use ReflectionClass;

final class ObjectCreator implements IObjectCreator
{
    protected ?object $object = null;
    protected ReflectionClass $reflection;

    public function create(string $classFQCN): IObjectCreator
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

    public function withProperty(string $propertyName, mixed $value): IObjectCreator
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

    public function getObject(): object
    {
        if ($this->object === null) {
            throw new UninitialisedCreatorException();
        }
        
        return $this->object;
    }

}
