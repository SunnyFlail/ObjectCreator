<?php

namespace SunnyFlail\ObjectCreator\Exceptions;

use Exception;
use Throwable;

class UninitialisedCreatorException extends Exception {


    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            "Trying to access uninitalised object creator!", $code, $previous
        );
    }

}