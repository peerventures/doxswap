<?php

namespace Blaspsoft\Doxswap;

use Blaspsoft\Doxswap\ConversionService;
use Blaspsoft\Doxswap\Exceptions\ConversionFailedException;

/**
 * @method static \Blaspsoft\Doxswap\Doxswap convert(string $file, string $toFormat)
 * @method static \Blaspsoft\Doxswap\Doxswap configure(string $disk, string $outputDisk)
 */
class Doxswap
{
    /**
     * The input file.
     *
     * @var string
     */
    public $inputFile;

    /**
     * The output file.
     *
     * @var string
     */
    public $outputFile;

    /**
     * The format to convert the file to.
     *
     * @var string
     */
    public $toFormat;

    /**
     * The conversion service.
     *
     * @var \Blaspsoft\Doxswap\ConversionService
     */
    protected $conversionService;

    public function __construct()
    {
        $this->conversionService = new ConversionService();
    }

    /**
     * Convert a file to a different format
     *
     * @param string $file The absolute path to the file to convert
     * @param string $format The format to convert the file to
     * @return self
     */
    public function convert($file, $toFormat)
    {
        $this->inputFile = $file;

        $this->toFormat = $toFormat;

        $this->outputFile = $this->conversionService->convertFile($this->inputFile, $this->toFormat);

        return $this;
    }
}
