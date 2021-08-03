<?php

namespace SunnyFlail\ObjectCreator;

use SunnyFlail\ObjectCreator\Exceptions\UninitialisedCreatorException;
use SunnyFlail\ObjectCreator\Exceptions\ClassNotFoundException;
use InvalidArgumentException;
use ReflectionException;
use ReflectionClass;
use ReflectionProperty;
use SunnyFlail\ObjectCreator\Exceptions\InvalidTypeException;
use SunnyFlail\Traits\GetTypesTrait;

final class ObjectCreator implements IObjectCreator
{

    use GetTypesTrait;

    private ?object $object = null;
    private ReflectionClass $reflection;

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

        try { 
            $value = $this->updatePropertyType($property, $value);
        } catch (InvalidTypeException) {
            return $this;
        }

        $property->setValue($this->object, $value);

        return $this;
    }

    /**
     * Updates property type
     * 
     * @param ReflectionProperty $property
     * @param mixed $value
     * 
     * @return mixed
     * 
     * @throws InvalidTypeException
     */
    private function updatePropertyType(ReflectionProperty $property, mixed $value): mixed
    {
        $types = $this->getTypeStrings($property);

        if (!$types) {
            return $value;
        }

        foreach ($types as $type) {
            if ($type === 'mixed') {
                return $value;
            }
            if ($type === 'int' && is_numeric($value)) {
                return (int) $value;
            }
            if ($type === 'array' && is_array($value)) {
                return $value;
            }
            if ($type === 'string' && is_string($value)) {
                return $value;
            }
            if ($type === 'boolean') {
                return boolval($value);
            }
            if (class_exists('\\' . $type)) {
                if ($obj = $this->scrapeObject($type, $value)) {
                    return $obj;
                }
            }
        }

        throw new InvalidTypeException();
    }

    private function scrapeObject(string $classFQCN, mixed $value): ?object
    {
        if ($value instanceof ('\\' . $classFQCN)) {
            return $value;
        }
        if (!is_array($value)) {
            return null;
        }

        $creator = $this->create($classFQCN);

        foreach ($value as $property => $propValue) {
            $creator->withProperty($property, $propValue);
        }

        return $creator->getObject();
    }

    public function getObject(): object
    {
        if ($this->object === null) {
            throw new UninitialisedCreatorException();
        }
        
        return $this->object;
    }

}
