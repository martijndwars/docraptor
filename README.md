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
		"bytes/docraptor": "dev-master"
	}
}
````

}

## Usage

```php
<?php

use Bytes\Docraptor\Document\PdfDocument;

$document = new PdfDocument('Document 1');
$document
	->setContent($html)
	->setStrictMode(false)
;

// ----------------------------------------------------------------------------

use Bytes\Docraptor\Http\Client as HttpClient;
use Bytes\Docraptor\Client;

$client = new Client(new HttpClient(), '4pik3y');

try {
	$pdf = $client->convert($document);
} catch (DocraptorException $e) {

}

// ----------------------------------------------------------------------------

try {
	$pdf = $this->get('docraptor')->convert($document);
} catch (DocraptorException $e) {

}
```

## License

This project is licensed under the MIT license.