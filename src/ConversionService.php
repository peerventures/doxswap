<?php

namespace Blaspsoft\Doxswap;

use Exception;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Blaspsoft\Doxswap\Exceptions\ConversionFailedException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedMimeTypeException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedConversionException;

class ConversionService
{
    /**
     * The mime types.
     *
     * @var array
     */
    protected $mimeTypes;

    /**
     * The disk.
     *
     * @var string
     */
    protected $storageDisk;

    /**
     * The output disk.
     *
     * @var string
     */
    protected $outputDisk;

    /**
     * The supported conversions.
     *
     * @var array
     */
    protected $supportedConversions;

    /**
     * The libre office path.
     *
     * @var string
     */
    protected $libreOfficePath;

    /**
     * The perform cleanup.
     *
     * @var bool
     */
    protected $performCleanup;

    public function __construct(?string $storageDisk = null, ?string $outputDisk = null)
    {
        $this->storageDisk = $storageDisk ?? config('doxswap.input_disk');
        $this->outputDisk = $outputDisk ?? config('doxswap.output_disk');
        $this->supportedConversions = config('doxswap.supported_conversions');
        $this->mimeTypes = config('doxswap.mime_types');
        $this->performCleanup = config('doxswap.perform_cleanup');
        $this->libreOfficePath = config('doxswap.libre_office_path');
    }
   
    /**
     * Process the file conversion.
     *
     * @param string $filename
     * @param string $toExtension
     * @return string
     * @throws Exception
     */
    public function convertFile(string $filename, string $toExtension): string
    {
        $filePath = Storage::disk($this->storageDisk)->path($filename);

        $fromExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        $mimeType = $this->getMimeType($fromExtension);

        if (!$this->isSupportedConversion($fromExtension, $toExtension)) {
            throw new UnsupportedConversionException($fromExtension, $toExtension);
        }

        if (!$mimeType) {
            throw new UnsupportedMimeTypeException($fromExtension);
        }

        try {
           
            $this->process($filePath, $toExtension);

            $outputPath = $this->renameFile($filePath, $toExtension);


        } catch (ProcessFailedException $e) {

            throw new ConversionFailedException($e->getMessage());

        } finally {

            $this->cleanup($filename);
        }

        return $outputPath;
    }

    /**
     * Process the file conversion
     *
     * @param string $format
     * @return self
     */
    public function process(string $filePath, string $format): void
    {
        $command = [
            $this->libreOfficePath,
            '--headless',
            '--convert-to', $format ,
            '--outdir', Storage::disk($this->outputDisk)->path(''),
            $filePath,
        ];

        $process = new Process($command);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

     /**
     * Check if the conversion is supported.
     *
     * @param string $fromExtension
     * @param string $toExtension
     * @return bool
     */
    public function isSupportedConversion(string $fromExtension, string $toExtension): bool
    {
        return isset($this->supportedConversions[$fromExtension]) &&
               in_array($toExtension, $this->supportedConversions[$fromExtension]);
    }

    /**
     * Get the MIME type for the given extension.
     *
     * @param string $fromExtension
     * @return string|null
     */
    public function getMimeType(string $fromExtension): ?string
    {
        return $this->mimeTypes[$fromExtension] ?? null;
    }

    /**
     * Cleanup the input file.
     *
     * @param string $filename
     * @return void
     */
    protected function cleanup(string $filename): void
    {
        if ($this->performCleanup) {
            Storage::disk($this->storageDisk)->delete($filename);
        }
    }

    /**
     * Rename the file.
     *
     * @param string $filePath
     * @param string $toExtension
     * @return string
     */
    protected function renameFile(string $filePath, string $toExtension): string
    {
        $strategy = config('doxswap.filename.strategy');
        $options = config('doxswap.filename.options');

        $originalOutputFilePath = Storage::disk($this->outputDisk)->path('') . File::name($filePath) . '.' . $toExtension;

        $filenameGenerator = new FilenameGeneratorService();

        $newOutputFilePath = Storage::disk($this->outputDisk)->path($filenameGenerator->generate($originalOutputFilePath, $toExtension, $strategy, $options));

        File::move($originalOutputFilePath, $newOutputFilePath);

        return $newOutputFilePath;
    }
}
