<?php

namespace Blaspsoft\Doxswap;

use Illuminate\Support\Str;
use DateTime;

class FilenameGeneratorService
{
    /**
     * Generate a filename based on the specified strategy.
     *
     * @param string $originalFilename
     * @param string $extension
     * @param string $strategy
     * @param array $options Additional options for filename generation
     * @return string
     */
    public function generate(string $originalFilename, string $extension, string $strategy = 'original', array $options = []): string
    {
        return match ($strategy) {
            'original' => $originalFilename . '.' . $extension,
            
            'random' => Str::random($options['length'] ?? 16) . '.' . $extension,
            
            'uuid' => (string) Str::uuid() . '.' . $extension,
            
            'timestamp' => $this->generateTimestampFilename($originalFilename, $extension, $options),
            
            'date' => $this->generateDateFilename($originalFilename, $extension, $options),
            
            'prefix' => ($options['prefix'] ?? 'converted_') . $originalFilename . '.' . $extension,
            
            'suffix' => $originalFilename . ($options['suffix'] ?? '_converted') . '.' . $extension,
            
            'numbered' => $this->generateNumberedFilename($originalFilename, $extension, $options),
            
            'slug' => Str::slug($originalFilename) . '.' . $extension,
            
            'hash' => $this->generateHashFilename($originalFilename, $extension, $options),
            
            default => $originalFilename . '.' . $extension,
        };
    }

    /**
     * Generate a timestamp-based filename.
     */
    protected function generateTimestampFilename(string $originalFilename, string $extension, array $options): string
    {
        $format = $options['format'] ?? 'Y-m-d_H-i-s';
        $date = new DateTime();
        return $date->format($format) . '_' . $originalFilename . '.' . $extension;
    }

    /**
     * Generate a date-based filename.
     */
    protected function generateDateFilename(string $originalFilename, string $extension, array $options): string
    {
        $format = $options['format'] ?? 'Y-m-d';
        $date = new DateTime();
        return $date->format($format) . '_' . $originalFilename . '.' . $extension;
    }

    /**
     * Generate a numbered filename.
     */
    protected function generateNumberedFilename(string $originalFilename, string $extension, array $options): string
    {
        $number = $options['number'] ?? 1;
        $separator = $options['separator'] ?? '_';
        return $originalFilename . $separator . $number . '.' . $extension;
    }

    /**
     * Generate a hash-based filename.
     */
    protected function generateHashFilename(string $originalFilename, string $extension, array $options): string
    {
        $algorithm = $options['algorithm'] ?? 'md5';
        $length = $options['length'] ?? 32;
        $hash = hash($algorithm, $originalFilename . microtime());
        return substr($hash, 0, $length) . '.' . $extension;
    }
}