<?php

namespace Tests\Integration;

use Blaspsoft\Doxswap\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Blaspsoft\Doxswap\Doxswap;
use Blaspsoft\Doxswap\Exceptions\ConversionFailedException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedMimeTypeException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedConversionException;
use PHPUnit\Framework\Attributes\Test;

class FileConversionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('input');
        Storage::fake('output');
    }

    #[Test]
    public function it_successfully_converts_a_real_file(): void
    {
        Storage::disk('input')->put('test.docx', file_get_contents(__DIR__ . '/../Stubs/sample.docx'));

        $doxswap = new Doxswap();
        $result = $doxswap->convert('test.docx', 'pdf');

        $this->assertFileExists($result->outputFile);
        $this->assertStringEndsWith('.pdf', $result->outputFile);
    }

    #[Test]
    public function it_throws_exception_for_invalid_format(): void
    {
        Storage::disk('input')->put('test.txt', 'This is a test text file.');

        $this->expectException(UnsupportedConversionException::class);

        $doxswap = new Doxswap();
        $doxswap->convert('test.txt', 'txt');
    }

    #[Test]
    public function it_throws_exception_when_input_file_is_missing(): void
    {
        $this->expectException(ConversionFailedException::class);

        $doxswap = new Doxswap();
        $doxswap->convert('missing.docx', 'pdf');
    }

    #[Test]
    public function it_deletes_input_file_after_conversion_when_cleanup_is_enabled(): void
    {
        config(['doxswap.perform_cleanup' => true]);

        Storage::disk('input')->put('test.docx', file_get_contents(__DIR__ . '/../Stubs/sample.docx'));

        $doxswap = new Doxswap();
        $doxswap->convert('test.docx', 'pdf');

        $this->assertFalse(Storage::disk('input')->exists('test.docx'));
    }

    #[Test]
    public function it_does_not_delete_input_file_after_conversion_when_cleanup_is_disabled(): void
    {
        config(['doxswap.perform_cleanup' => false]);

        Storage::disk('input')->put('test.docx', file_get_contents(__DIR__ . '/../Stubs/sample.docx'));

        $doxswap = new Doxswap();
        $doxswap->convert('test.docx', 'pdf');

        $this->assertTrue(Storage::disk('input')->exists('test.docx'));
    }

    #[Test]
    public function it_throws_exception_when_file_is_empty(): void
    {
        $this->expectException(ConversionFailedException::class);

        $doxswap = new Doxswap();
        $doxswap->convert('empty.docx', 'pdf');
    }

    #[Test]
    public function it_throws_exception_for_unsupported_mime_type(): void
    {
        Storage::disk('input')->put('unsupported.epub', file_get_contents(__DIR__ . '/../Stubs/sample.docx'));
        $this->expectException(UnsupportedMimeTypeException::class);

        $doxswap = new Doxswap();
        $doxswap->convert('unsupported.epub', 'pdf');
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Blaspsoft\Doxswap\DoxswapServiceProvider',
            'Blaspsoft\Onym\OnymServiceProvider',
        ];
    }
}
