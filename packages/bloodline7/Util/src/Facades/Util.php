<?php

namespace Bloodline7\Util\Facades;

use Illuminate\Support\Facades\Facade;

class Util extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'util';
    }
}
