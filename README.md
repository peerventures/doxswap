<p align="center">
    <img src="./.github/assets/icon.png" alt="Onym Icon" width="150" height="150"/>
    <p align="center">
        <a href="https://github.com/Blaspsoft/doxswap/actions/workflows/main.yml"><img alt="GitHub Workflow Status (main)" src="https://github.com/Blaspsoft/doxswap/actions/workflows/main.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/blaspsoft/doxswap"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/blaspsoft/doxswap"></a>
        <a href="https://packagist.org/packages/blaspsoft/doxswap"><img alt="Latest Version" src="https://img.shields.io/packagist/v/blaspsoft/doxswap"></a>
        <a href="https://packagist.org/packages/blaspsoft/doxswap"><img alt="License" src="https://img.shields.io/packagist/l/blaspsoft/doxswap"></a>
    </p>
</p>

# Doxswap

A Laravel package for seamless document conversion using LibreOffice. Convert between various document formats like DOCX, PDF, ODT and more with a simple, elegant API.

## 🚀 Features

- 📄 **Multiple Format Support** – Convert between DOCX, PDF, ODT, and other document formats.
- 🚀 **Simple API** – Easy-to-use interface for document conversion operations.
- 💾 **Laravel Storage Integration** – Works seamlessly with Laravel's filesystem drivers.
- ⚡ **Efficient Processing** – Optimized conversion using LibreOffice's powerful engine.
- 🔒 **Secure File Handling** – Safe and secure document processing with proper cleanup.
- ⚙️ **Configurable Settings** – Customize paths, storage disks, and conversion options.
- 🛡️ **Error Handling** – Robust exception handling for unsupported formats and conversions.

## Installation

You can install the package via composer:

```bash
composer require blaspsoft/doxswap
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="doxswap-config"
```

### Overview

The `config/doxswap.php` file includes:

#### 💾 Storage

- `input_disk`: Where to read files from (default: 'public')
- `output_disk`: Where to save converted files (default: 'public')
- `perform_cleanup`: Delete input files after conversion (default: false)

#### 🛠️ LibreOffice Path

```php
'libre_office_path' => env('LIBRE_OFFICE_PATH', '/usr/bin/soffice')
```

Default paths by OS:

- 🐧 Linux: `/usr/bin/soffice`
- 🍎 macOS: `/Applications/LibreOffice.app/Contents/MacOS/soffice`
- 🪟 Windows: `C:\Program Files\LibreOffice\program\soffice.exe`

#### 📄 File Types

Supports various document formats including:

- Documents: DOC, DOCX, ODT, RTF, TXT
- Spreadsheets: XLS, XLSX, ODS, CSV
- Presentations: PPT, PPTX, ODP
- Images: JPG, PNG, SVG, BMP, TIFF
- Web: HTML, XML
- Other: PDF, EPUB

### Usage

```php
$convertedFile = Doxswap::convert('sample.docx', 'pdf');

/**
 * Returns a Doxswap object with the following properties:
 *
 * @property string $inputFile      The original input filename
 * @property string $outputFile     The full path to the converted output file
 * @property string $toFormat      The format the file was converted to (e.g. 'pdf')
 * @property ConversionService $conversionService  The service used for conversion
 */

```

## Requirements

### LibreOffice

This package requires LibreOffice to be installed on your system. Here's how to install it:

#### Ubuntu/Debian

```bash
sudo apt update
sudo apt install libreoffice
```

#### macOS

```bash
brew install libreoffice
```

# or download from https://www.libreoffice.org/download/download-libreoffice/

#### Windows

```bash
choco install libreoffice
```

# or download from https://www.libreoffice.org/download/download-libreoffice/

#### Docker

If you're using Docker, you can add LibreOffice to your container:

```dockerfile
# Ubuntu/Debian based
RUN apt-get update && apt-get install -y libreoffice

# Alpine based
RUN apk add --no-cache libreoffice
```

### PHP Requirements

- PHP >= 8.1
- ext-fileinfo
- Laravel >= 9.0

## Supported Conversions 📄 ↔️ 📑

| From/To | PDF | DOCX | ODT | RTF | TXT | HTML | EPUB | XML | XLSX | ODS | CSV | PPT | PPTX | ODP | PNG | JPG | SVG | TIFF |
| ------- | :-: | :--: | :-: | :-: | :-: | :--: | :--: | :-: | :--: | :-: | :-: | :-: | :--: | :-: | :-: | :-: | :-: | :--: |
| DOC     | ✅  |  ✅  | ✅  | ✅  | ✅  |  ✅  |  ✅  | ✅  |      |     |     |     |      |     |     |     |     |      |
| DOCX    | ✅  |      | ✅  | ✅  | ✅  |  ✅  |  ✅  | ✅  |      |     |     |     |      |     |     |     |     |      |
| ODT     | ✅  |  ✅  | ✅  | ✅  | ✅  |  ✅  |      | ✅  |      |     |     |     |      |     |     |     |     |      |
| RTF     | ✅  |  ✅  | ✅  |     | ✅  |  ✅  |      | ✅  |      |     |     |     |      |     |     |     |     |      |
| TXT     | ✅  |  ✅  | ✅  |     |     |  ✅  |      | ✅  |      |     |     |     |      |     |     |     |     |      |
| HTML    | ✅  |      | ✅  |     | ✅  |      |      |     |      |     |     |     |      |     |     |     |     |      |
| XML     | ✅  |  ✅  | ✅  |     | ✅  |  ✅  |      |     |      |     |     |     |      |     |     |     |     |      |
| CSV     | ✅  |      |     |     |     |  ✅  |      |     |  ✅  | ✅  |     |     |      |     |     |     |     |      |
| XLSX    | ✅  |      |     |     |     |  ✅  |      |     |      | ✅  | ✅  |     |      |     |     |     |     |      |
| XLS     | ✅  |      |     |     |     |  ✅  |      |     |      | ✅  | ✅  |     |      |     |     |     |     |      |
| ODS     | ✅  |      |     |     |     |  ✅  |      |     |  ✅  |     | ✅  |     |      |     |     |     |     |      |
| PPTX    | ✅  |      |     |     |     |      |      |     |      |     |     |     |      | ✅  |     |     |     |      |
| PPT     | ✅  |      |     |     |     |      |      |     |      |     |     |     |      | ✅  |     |     |     |      |
| ODP     | ✅  |      |     |     |     |      |      |     |      |     |     |     |  ✅  |     |     |     |     |      |
| SVG     | ✅  |      |     |     |     |      |      |     |      |     |     |     |      |     | ✅  | ✅  |     |  ✅  |
| JPG     | ✅  |      |     |     |     |      |      |     |      |     |     |     |      |     | ✅  |     | ✅  |      |
| PNG     | ✅  |      |     |     |     |      |      |     |      |     |     |     |      |     |     | ✅  | ✅  |      |
| BMP     | ✅  |      |     |     |     |      |      |     |      |     |     |     |      |     | ✅  | ✅  |     |      |
| TIFF    | ✅  |      |     |     |     |      |      |     |      |     |     |     |      |     | ✅  | ✅  |     |      |

### Legend 🔍

- ✅ : Supported conversion
- Empty cell: Conversion not supported

### File Type Categories 📁

- 📝 Documents: DOC, DOCX, ODT, RTF, TXT
- 🌐 Web: HTML, XML
- 📊 Spreadsheets: XLSX, XLS, ODS, CSV
- 🎯 Presentations: PPT, PPTX, ODP
- 🖼️ Images: SVG, JPG, PNG, BMP, TIFF
- 📚 eBooks: EPUB
- 📄 Universal: PDF

> **Note**: All conversions are performed using LibreOffice in headless mode 🚀

## License

Blasp is open-sourced software licensed under the [MIT license](LICENSE).
