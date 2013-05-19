<?php

namespace Bytes\Docraptor\Exception;

class UnprocessableEntityException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Your document contains syntax errors and Docraptor cannot process it.', 433);
    }
}