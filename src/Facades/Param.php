<?php

namespace Pneves001\Apidocs\Facades;

use Illuminate\Support\Facades\Facade;

class Param extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new \Pneves001\Apidocs\Params\Param;
    }
}
