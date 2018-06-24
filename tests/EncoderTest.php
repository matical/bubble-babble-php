<?php

namespace ksmz\BubbleBabble\Tests;

use ksmz\BubbleBabble\Encoder;
use PHPUnit\Framework\TestCase;

class EncoderTest extends TestCase
{
    public function testEncode()
    {
        $expectedOutputs = [
            ''                  => 'xexax',
            '1234567890'        => 'xesef-disof-gytuf-katof-movif-baxux',
            'Pineapple'         => 'xigak-nyryk-humil-bosek-sonax',
            'P-i-n-e-a-p-p-l-e' => 'xigad-typad-torud-tunud-tamyd-tasad-tosud-tarod-tynox',
            'ksmz'              => 'xipol-farel-puxex',
        ];

        foreach ($expectedOutputs as $input => $output) {
            $result = (new Encoder())->encode($input);
            $this->assertSame($result, $output);
        }
    }
}
