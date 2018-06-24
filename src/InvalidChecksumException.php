<?php

namespace ksmz\BubbleBabble;

class InvalidChecksumException extends BubbleBabbleException
{
    public function __construct(int $offset = 0, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Corrupt string: Invalid checksum at offset $offset", $code, $previous);
    }
}
