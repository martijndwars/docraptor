<?php

namespace Bytes\Docraptor;

use Bytes\Docraptor\Document\DocumentInterface;

interface ClientInterface
{
    /**
     * Convert document using docraptor API
     *
     * @param DocumentInterface $document The document to be converted
     * @return string The binary contents of the result after conversion
     */
    function convert(DocumentInterface $document);
}
