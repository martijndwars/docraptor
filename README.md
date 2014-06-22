# PHP Docrapter API

A simple client for the Docraptor API, written in PHP5.

## Features

* Convert PDF documents through the Docraptor API.

## Requirements

* PHP >= 5.3.2 with [cURL](http://php.net/manual/en/book.curl.php) extension

## Installation

Using composer, add the following to composer.json:

```yaml
{
	"require": {
		"bytes/docraptor": "dev-master@dev"
	}
}
````

## Usage

```php
<?php

use Bytes\Docraptor\Document\PdfDocument;
use Bytes\Docraptor\Http\Client as HttpClient;
use Bytes\Docraptor\Client;

$document = new PdfDocument('<Document name>');
$document->setContent($html);

$httpClient = new HttpClient();
$client = new Client($httpClient, '<4pik3y>');

try {
	$pdf = $client->convert($document);
} catch (DocraptorException $e) {

}
```