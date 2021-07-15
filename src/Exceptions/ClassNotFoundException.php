<?php

namespace SunnyFlail\ObjectCreator\Exceptions;

use InvalidArgumentException;
use Throwable;

final class ClassNotFoundException extends InvalidArgumentException
{

    public function __construct(string $className, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Class %s not found!', $className), $code, $previous
        );
    }

}