<?php

namespace SunnyFlail\ObjectCreatorTests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SunnyFlail\ObjectCreator\Exceptions\ClassNotFoundException;
use SunnyFlail\ObjectCreator\IObjectCreator;
use SunnyFlail\ObjectCreator\ObjectCreator;

final class ObjectCreatorTest extends TestCase
{

    protected static IObjectCreator $creator;

    public static function setUpBeforeClass(): void
    {
        self::$creator = new ObjectCreator();
    }

    public function creatorDataProvider()
    {
        return [
            [Entity::class, new Entity()]
        ];
    }

    public function exceptionCreatorProvider()
    {
        return [
            ["SimpleObject", ClassNotFoundException::class]
        ];
    }

    public function setterDataProvider()
    {
        return [
            "string" => [Entity::class, "string", "Yolla yehu!"],
            "array" => [Entity::class, "array", [1, 2, 3]]
        ];
    }

    public function exceptionSetterProvider()
    {
        return [
            "int" => [Entity::class, "int", 123, InvalidArgumentException::class],
        ];
    }

    /**
     * @dataProvider creatorDataProvider
     */
    public function testCreation(string $className, object $expected)
    {
        $obj = self::$creator->create($className)->getObject();

        $this->assertEquals(
            $expected, $obj
        );
    }

    /**
     * @dataProvider exceptionCreatorProvider
     */
    public function testCreationException(string $className, string $expected)
    {
        $this->expectException($expected);
        self::$creator->create($className);
    }

    /**
     * @dataProvider setterDataProvider
     */
    public function testSettingValue(string $className, string $propertyName, mixed $value)
    {
        $obj = self::$creator->create($className)->withProperty($propertyName, $value)->getObject();
        $actual = $obj->get($propertyName);

        $this->assertEquals($value, $actual);
    }

    /**
     * @dataProvider exceptionSetterProvider
     */
    public function testSettingException(string $className, string $propertyName, mixed $value, string $expected)
    {
        $this->expectException($expected);
        $obj = self::$creator->create($className)->withProperty($propertyName, $value)->getObject();
    }

}