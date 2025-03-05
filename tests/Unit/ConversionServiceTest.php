<?php

namespace Blaspsoft\Doxswap\Tests\Unit;

use ReflectionClass;
use Blaspsoft\Doxswap\Doxswap;
use Blaspsoft\Doxswap\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Storage;
use Blaspsoft\Doxswap\ConversionService;
use PHPUnit\Framework\MockObject\MockObject;
use Blaspsoft\Doxswap\Exceptions\ConversionFailedException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedMimeTypeException;
use Blaspsoft\Doxswap\Exceptions\UnsupportedConversionException;

class ConversionServiceTest extends TestCase
{
    private Doxswap $doxswap;
    private ConversionService&MockObject $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('input');
        Storage::fake('output');
        
        $this->service = $this->createMock(ConversionService::class);

        $this->doxswap = new Doxswap($this->service);
        
        $reflection = new ReflectionClass($this->doxswap);
        $property = $reflection->getProperty('conversionService');
        $property->setAccessible(true);
        $property->setValue($this->doxswap, $this->service);
    }

    #[Test]
    public function it_converts_files_using_service()
    {
        Storage::disk('input')->put('test.docx', 'content');

        $expectedOutputPath = '/path/to/converted/test.pdf';

        $this->service
            ->expects($this->once())
            ->method('convertFile')
            ->with('test.docx', 'pdf')
            ->willReturn($expectedOutputPath);

        $result = $this->doxswap->convert('test.docx', 'pdf');

        $this->assertInstanceOf(Doxswap::class, $result);
        $this->assertEquals('test.docx', $result->inputFile);
        $this->assertEquals('pdf', $result->toFormat);
        $this->assertEquals($expectedOutputPath, $result->outputFile);
    }

    #[Test]
    public function it_throws_an_exception_when_conversion_fails(): void
    {
        // Setup test file in fake storage
        Storage::disk('input')->put('test.docx', 'content');

        // Mock the convertFile method to throw an exception
        $this->service
            ->expects($this->once())
            ->method('convertFile')
            ->willThrowException(new ConversionFailedException('Conversion failed'));

        $this->expectException(ConversionFailedException::class);
        $this->expectExceptionMessage('Conversion failed');

        // Call the method (should throw an exception)
        $this->doxswap->convert('test.docx', 'pdf');
    }

    #[Test]
    public function it_throws_an_exception_for_unsupported_format(): void
    {
        Storage::disk('input')->put('test.txt', 'content');

        $this->service
            ->expects($this->once())
            ->method('convertFile')
            ->willThrowException(new UnsupportedConversionException('txt', 'pdf'));

        $this->expectException(UnsupportedConversionException::class);

        $this->doxswap->convert('test.txt', 'pdf');
    }

    #[Test]
    public function it_returns_the_same_instance_for_chaining(): void
    {
        Storage::disk('input')->put('test.docx', 'content');

        $this->service
            ->method('convertFile')
            ->willReturn('/path/to/converted/test.pdf');

        $result = $this->doxswap->convert('test.docx', 'pdf');

        $this->assertSame($this->doxswap, $result);
    }

    #[Test]
    public function it_throws_an_exception_when_file_is_empty(): void
    {
        $this->service
            ->method('convertFile')
            ->willThrowException(new ConversionFailedException('File is empty'));

        $this->expectException(ConversionFailedException::class);
        $this->expectExceptionMessage('File is empty');

        $this->doxswap->convert('', 'pdf');
    }

    #[Test]
    public function it_throws_an_exception_for_unknown_format(): void
    {
        Storage::disk('input')->put('test.unknown', 'content');

        $this->service
            ->method('convertFile')
            ->willThrowException(new UnsupportedMimeTypeException('unknown'));

        $this->expectException(UnsupportedMimeTypeException::class);

        $this->doxswap->convert('test.unknown', 'pdf');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}