# ObjectCreator
A simple class providing object oriented interface over class invoking

# Usage
First you need to import it by requiring composer autoloader AND then importing it in class that will use it
```php
use SunnyFlail\ObjectCreator\ObjectCreator;
```
## Creating object
First you need a common object creator instance and then invoke `ObjectCreator::create` passing fully qualified class name as an argument (the namespaced CANNOT contain leading backslashes).
This returns a mutable copy of ObjectCreator

```php
$creator = new ObjectCreator();
$concreteCreator = $creator->create(Entity::class);
```

## Setting properties
To set properties just invoke `ObjectCreator::withProperty` on initialised copy, passing name of the property as first value, and value as second.
This returns the copy so you can chain it.
```php
$concreteCreator->withProperty("propertyName", "value");
```
## Getting object
To get the object invoke `ObjectCreator::getObject` on initalised copy.
```php
$object = $concreteCreator->getObject();
```

## Extending

You can make your custom implementation of this by implementing interface `IObjectCreator`.