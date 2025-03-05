<?php

namespace Blaspsoft\Doxswap\Tests\Unit;

use Mockery;
use Blaspsoft\Doxswap\Tests\TestCase;
use Blaspsoft\Doxswap\ConversionService;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Blaspsoft\Doxswap\Exceptions\UnsupportedConversionException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedMimeTypeException;

class ConversionServiceTest extends TestCase
{
    private ConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('input');
        Storage::fake('output');
        
        $this->service = new ConversionService(
            config('doxswap.input_disk'),
            config('doxswap.output_disk')
        );
    }

    #[Test]
    public function it_handles_conversion_with_config()
    {
        $this->assertEquals(
            config('doxswap.input_disk'),
            'input'
        );
        
        Storage::disk('input')->put('test.docx', 'content');
        $result = $this->service->convertFile('test.docx', 'pdf');
        $this->assertStringEndsWith('.pdf', basename($result));
    }

    #[Test]
    public function it_validates_supported_conversions()
    {
        $this->assertTrue($this->service->isSupportedConversion('docx', 'pdf'));
        $this->assertFalse($this->service->isSupportedConversion('invalid', 'pdf'));
    }

    #[Test]
    public function it_throws_exception_for_unsupported_conversion()
    {
        $this->expectException(UnsupportedConversionException::class);
        Storage::disk('input')->put('test.docx', 'content');
        $this->service->convertFile('test.docx', 'svg');
    }

    #[Test]
    public function it_throws_exception_for_unsupported_mime_type()
    {
        $this->expectException(UnsupportedMimeTypeException::class);
        Storage::disk('input')->put('test.xyz', 'content');
        $this->service->convertFile('test.xyz', 'pdf');
    }

    #[Test]
    public function it_renames_converted_file()
    {
        Storage::disk('input')->put('test.docx', 'content');
        $result = $this->service->convertFile('test.docx', 'pdf');
        $this->assertStringEndsWith('.pdf', basename($result));
        $this->assertTrue(strlen(basename($result)) === 28);
    }

    protected function getPackageProviders($app): array
    {
        return [
            'Blaspsoft\Doxswap\DoxswapServiceProvider',
            'Blaspsoft\Onym\OnymServiceProvider',
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}