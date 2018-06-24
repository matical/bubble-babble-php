<?php

namespace ksmz\BubbleBabble;

class Decoder
{
    /**
     * @var string
     */
    protected $vowels = 'aeiouy';

    /**
     * @var string
     */
    protected $consonants = 'bcdfghklmnprstvzx';

    /**
     * @param $input
     * @return string
     *
     * @throws \ksmz\BubbleBabble\InvalidByteOffsetException
     * @throws \ksmz\BubbleBabble\InvalidChecksumException
     * @throws \ksmz\BubbleBabble\InvalidEncodingException
     */
    public function decode($input)
    {
        $this->validateIntegrity($input);

        $checksum = 1;
        $split = $this->splitToTuple($input);
        $lastTuple = count($split) - 1;
        $buffer = '';

        foreach ($split as $index => $tuple) {
            $offset = $index * 6;
            $morphed = $this->morphToCharPositions($tuple);

            if ($index !== $lastTuple) {
                $firstSegment = $this->decodeFirstSegment($morphed[0], $morphed[1], $morphed[2], $offset, $checksum);
                $secondSegment = $this->decodeSecondSegment($morphed[3], $morphed[5], $offset);
                $checksum = $this->checksum($checksum, $firstSegment, $secondSegment);

                $buffer .= chr($firstSegment) . chr($secondSegment);
            } elseif ($morphed[1] === 16) {
                // When 'X' is encountered outside of start and end
                $this->validateMorphedChecksum($morphed, $checksum, $offset);
            } else {
                // Decode last segment
                $byte = $this->decodeFirstSegment($morphed[0], $morphed[1], $morphed[2], $offset, $checksum);

                $buffer .= chr($byte);
            }
        }

        return $buffer;
    }

    /**
     * @param string $input
     * @throws \ksmz\BubbleBabble\InvalidEncodingException
     */
    public function validateIntegrity(string $input): void
    {
        if ($input[0] !== 'x' || substr($input, -1) !== 'x') {
            throw new InvalidEncodingException("BubbleBabble strings must begin and end with an 'x'");
        }

        if (! preg_match('/^([' . $this->consonants . $this->vowels . ']{5})(-(?1))*$/', $input)) {
            throw new InvalidEncodingException('Invalid BubbleBabble format');
        }

        if (strlen($input) !== 5 && strlen($input) % 6 !== 5) {
            throw new InvalidEncodingException('Corrupted BubbleBabble pattern: Invalid Length');
        }
    }

    /**
     * @param $firstByte
     * @param $secondByte
     * @param $thirdByte
     * @param $offset
     * @param $checksum
     * @return int
     *
     * @throws \ksmz\BubbleBabble\InvalidByteOffsetException
     */
    protected function decodeFirstSegment($firstByte, $secondByte, $thirdByte, $offset, $checksum)
    {
        $aMark = ($firstByte - ($checksum % 6) + 6) % 6;

        if ($aMark >= 4) {
            throw new InvalidByteOffsetException($offset);
        }

        // C[b] / $bMark
        if ($secondByte > 16) {
            throw new InvalidByteOffsetException($offset + 1);
        }

        $cMark = ($thirdByte - ($checksum / 6 % 6) + 6) % 6;

        if ($cMark >= 4) {
            throw new InvalidByteOffsetException($offset + 2);
        }

        return $aMark << 6 | $secondByte << 2 | $cMark;
    }

    /**
     * @param $dMark
     * @param $eMark
     * @param $offset
     * @return int
     *
     * @throws \ksmz\BubbleBabble\InvalidByteOffsetException
     */
    protected function decodeSecondSegment($dMark, $eMark, $offset)
    {
        if ($dMark > 16) {
            throw new InvalidByteOffsetException($offset);
        }

        if ($eMark > 16) {
            throw new InvalidByteOffsetException($offset + 2);
        }

        return ($dMark << 4) | $eMark;
    }

    /**
     * @param $input
     * @return array
     */
    protected function morphToCharPositions($input)
    {
        $tuple = [
            strpos($this->vowels, $input[0]), // V[a]
            strpos($this->consonants, $input[1]), // C[b]
            strpos($this->vowels, $input[2]), // V[c]
        ];

        if (isset($input[3])) {
            $tuple[] = strpos($this->consonants, $input[3]); // C[d]
            $tuple[] = '-';
            $tuple[] = strpos($this->consonants, $input[5]); // C[e]
        }

        return $tuple;
    }

    /**
     * @param $input
     * @return array
     */
    protected function splitToTuple($input): array
    {
        return str_split(substr($input, 1, -1), 6);
    }

    /**
     * @param $morphed
     * @param $checksum
     * @param $offset
     *
     * @throws \ksmz\BubbleBabble\InvalidChecksumException
     */
    protected function validateMorphedChecksum($morphed, $checksum, $offset): void
    {
        if ($morphed[0] !== $checksum % 6) {
            throw new InvalidChecksumException($offset);
        }

        if ($morphed[2] !== (int) ($checksum / 6)) {
            throw new InvalidChecksumException($offset + 2);
        }
    }

    /**
     * @param $checksum
     * @param $firstByte
     * @param $secondByte
     * @return int
     */
    protected function checksum($checksum, $firstByte, $secondByte): int
    {
        return ($checksum * 5 + $firstByte * 7 + $secondByte) % 36;
    }
}
