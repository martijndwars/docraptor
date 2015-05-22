<?php

namespace Bytes\Docraptor\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements DocraptorException
{
    public function __construct($code = 0)
    {
        parent::__construct('DocRaptor returned an unexpected response ('.$code.')', $code);
    }
}