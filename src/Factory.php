<?php

namespace ksmz\BubbleBabble;

class Factory
{
    /**
     * @param string $input
     * @return string
     */
    public static function encode(string $input)
    {
        return (new Encoder())->encode($input);
    }

    /**
     * @param string $input
     * @return string
     * @throws \ksmz\BubbleBabble\InvalidByteOffsetException
     * @throws \ksmz\BubbleBabble\InvalidChecksumException
     * @throws \ksmz\BubbleBabble\InvalidEncodingException
     */
    public static function decode(string $input)
    {
        return (new Decoder())->decode($input);
    }

    /**
     * @param string $input
     * @return bool
     */
    public static function validate(string $input)
    {
        try {
            (new Decoder())->validateIntegrity($input);
        } catch (InvalidEncodingException $exception) {
            return false;
        }

        return true;
    }
}
