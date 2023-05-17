<?php

namespace Bloodline7\Retro\Facades;

use Illuminate\Support\Facades\Facade;

class Retro extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'retro';
    }
}
