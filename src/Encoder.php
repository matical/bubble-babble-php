<?php

namespace ksmz\BubbleBabble;

class Encoder
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
     * @param string $input
     * @return string
     */
    public function encode(string $input)
    {
        $buffer = 'x';
        $length = strlen($input);

        $i = 0;
        $checksum = 1;
        $tupleMark = 1;

        while ($i < $length - 1) {
            $firstByte = ord($input[$i]);
            $secondByte = ord($input[$i + 1]);

            [$a, $b, $c] = $this->encodeFirstSegment($firstByte, $checksum);
            [$d, $e] = $this->encodeSecondSegment($secondByte);

            // Build tuple T
            // V[a] C[b] V[c] C[d] `-' C[e]
            $buffer[$tupleMark] = $this->vowels[$a];
            $buffer[$tupleMark + 1] = $this->consonants[$b];
            $buffer[$tupleMark + 2] = $this->vowels[$c];
            $buffer[$tupleMark + 3] = $this->consonants[$d];
            $buffer[$tupleMark + 4] = '-';
            $buffer[$tupleMark + 5] = $this->consonants[$e];

            $i += 2;
            $tupleMark += 6; // V[a] C[b] V[c] C[d] `-' C[e] = 6 position marks
            $checksum = $this->checksum($checksum, $firstByte, $secondByte);
        }

        // Last segment
        if ($length % 2 === 0) {
            $a = $checksum % 6;
            $b = 16;
            $c = $checksum / 6;
        } else {
            [$a, $b, $c] = $this->encodeFirstSegment(ord($input[$i]), $checksum);
        }

        $buffer[$tupleMark] = $this->vowels[$a];
        $buffer[$tupleMark + 1] = $this->consonants[$b];
        $buffer[$tupleMark + 2] = $this->vowels[(int) $c];
        $buffer[$tupleMark + 3] = $this->consonants[16];

        return $buffer;
    }

    /**
     * @param $firstByte
     * @param $checksum
     * @return array
     */
    protected function encodeFirstSegment($firstByte, $checksum): array
    {
        $a = ((($firstByte >> 6) & 3) + $checksum) % 6;
        $b = ($firstByte >> 2) & 15;
        $c = (($firstByte & 3) + ($checksum / 6)) % 6;

        return [$a, $b, $c];
    }

    /**
     * @param $secondByte
     * @return array
     */
    protected function encodeSecondSegment($secondByte): array
    {
        $d = ($secondByte >> 4) & 15;
        $e = $secondByte & 15;

        return [$d, $e];
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
