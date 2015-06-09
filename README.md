# PHP DocRapter API

A simple client for the [DocRaptor](https://docraptor.com) API, written in PHP5.

## Features

* Convert HTML to PDF and HTML to Excel through the DocRaptor API.

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
$html = '<some html goes here>';
$document->setContent($html);

$httpClient = new HttpClient();
$client = new Client($httpClient, 'YOUR_API_KEY_HERE');

try {
	$pdf = $client->setTestMode(true)->convert($document);
	// write to file, stream to user, etc.
} catch (DocraptorException $e) {
        echo($e);
}
```
