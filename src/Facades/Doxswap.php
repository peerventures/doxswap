<?php

namespace Blaspsoft\Doxswap\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Blaspsoft\Doxswap\Doxswap convert(string $file, string $toFormat)
 * @method static \Blaspsoft\Doxswap\Doxswap configure(string $disk, string $outputDisk)
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
