<?php

namespace Bytes\Docraptor\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements DocraptorException
{
    public function __construct($code = 0)
    {
        parent::__construct('Docraptor returned an unexpected response ('.$code.')', $code);
    }
}