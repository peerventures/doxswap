<?php

namespace Blaspsoft\Doxswap\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Blaspsoft\Doxswap\Skeleton\SkeletonClass
 */
class Doxswap extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'doxswap';
    }
}
