<?php

namespace Blaspsoft\Doxswap;

use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Converter
{
    protected $filename;
    protected $newFilename;
    protected $contents;
    protected $inputFilePath;
    protected $outputFilePath;
    protected $disk;
    protected $dirName;
    protected $fromExtension;
    protected $toExtension;

    /**
     * Inject Filesystem for flexibility (dependency injection)
     * @param Filesystem $disk
     */
    public function __construct
    (
        Filesystem $file,
        string $fromExtension,
        string $toExtension,
        Filesystem $disk = null
    )
    {
        $this->disk = $disk ?? Storage::disk('conversions');
        $this->filename = $file->getClientOriginalName();
        $this->fromExtension = $fromExtension;
        $this->toExtension = $toExtension;

        $this->validateExtensions();
        $this->dirName = $this->createDirName();
        $this->newFilename = $this->generateNewFilename();
        $this->contents = $file->get();

        $this->store($this->filename, $this->contents);
        $this->inputFilePath = $this->path($this->filename);
        $this->outputFilePath = $this->generateOutputPath();
    }

    /**
     * Process the file conversion
     *
     * @param string $format
     * @return self
     */
    public function process(string $format): self
    {
        // Validate the format create a config with the mappings
        if (!in_array($format, config("convert.supportedConversions.$this->fromExtension"))) {
            throw new \InvalidArgumentException("Unsupported format: $format. Supported formats are 'pdf', 'ods', 'csv', and 'html'."); // Need to update this message
        }

        $command = [
            config('doxswap.libre_office_path'),
            '--headless',
            '--convert-to', $format,
            '--outdir', dirname($this->outputFilePath),
            $this->inputFilePath
        ];

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $this;
    }

    /**
     * Validates the file extensions
     *
     * @throws \InvalidArgumentException if the extensions are invalid
     */
    protected function validateExtensions()
    {
        if (!$this->filename || !str_ends_with($this->filename, $this->fromExtension)) {
            throw new \InvalidArgumentException("File extension does not match expected input format ({$this->fromExtension}).");
        }

        if (!$this->toExtension) {
            throw new \InvalidArgumentException("Target file extension cannot be empty.");
        }
    }

    /**
     * Generates the output file path based on the target extension
     *
     * @return string
     */
    protected function generateNewFilename()
    {
        return str_replace($this->fromExtension, $this->toExtension, $this->filename);
    }

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Get the new filename
     *
     * @return string
     */
    public function getNewFilename()
    {
        return $this->newFilename;
    }

    /**
     * Generates the full output file path
     *
     * @return string
     */
    protected function generateOutputPath()
    {
        return $this->path($this->newFilename);
    }

    /**
     * Generates a random directory name
     *
     * @return string
     */
    protected function createDirName()
    {
        return Str::random(24);
    }

    /**
     * Store the file in the filesystem
     *
     * @param string $filename
     * @param string $contents
     * @return bool
     */
    public function store(string $filename, $contents)
    {
        return $this->disk->put($this->dirName . '/' . $filename, $contents);
    }

    /**
     * Get the full path for the given file
     *
     * @param string $filename
     * @return string
     */
    public function path(string $filename)
    {
        return $this->disk->path($this->dirName . '/' . $filename);
    }

    /**
     * Get file contents
     *
     * @param string $filename
     * @return string
     */
    public function get(string $filename)
    {
        return $this->disk->get($this->dirName . '/' . $filename);
    }

    /**
     * Delete the file from the filesystem
     *
     * @param string $filename
     * @return bool
     */
    public function delete(string $filename)
    {
        return $this->disk->delete($this->dirName . '/' . $filename);
    }

    /**
     * Get the file size
     *
     * @param string $filename
     * @return int
     */
    public function getFileSize(string $filename)
    {
        return $this->disk->size($this->dirName . '/' . $filename);
    }

    /**
     * Get the download URL for the given file
     *
     * @param string $filename
     * @return string
     */
    public function getDownloadUrl(string $filename)
    {
        return str_replace(' ', '%20', $this->disk->url($this->dirName . '/' . $filename));
    }

    /**
     * Get the base64-encoded file data
     *
     * @param string $filename
     * @return string
     */
    public function getFileData(string $filename): string
    {
        return base64_encode($this->get($filename));
    }

    /**
     * Get the directory name
     *
     * @return string
     */
    public function getDirName()
    {
        return $this->dirName;
    }
}
