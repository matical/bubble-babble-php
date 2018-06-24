<?php

namespace ksmz\BubbleBabble;

class InvalidByteOffsetException extends BubbleBabbleException
{
    public function __construct(int $offset = 0, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Corrupt string found at offset $offset", $code, $previous);
    }
}
