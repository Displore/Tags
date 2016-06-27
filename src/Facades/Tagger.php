<?php

namespace Displore\Tags\Facades;

use Illuminate\Support\Facades\Facade;

class Tagger extends Facade
{
    /**
     * Get the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'tagger';
    }
}
