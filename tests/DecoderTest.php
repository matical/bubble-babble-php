<?php

namespace ksmz\BubbleBabble\Tests;

use ksmz\BubbleBabble\Decoder;
use PHPUnit\Framework\TestCase;

class DecoderTest extends TestCase
{
    public function testDecode()
    {
        $expectedOutputs = [
            'xexax'                                                 => '',
            'xesef-disof-gytuf-katof-movif-baxux'                   => '1234567890',
            'xigak-nyryk-humil-bosek-sonax'                         => 'Pineapple',
            'xigad-typad-torud-tunud-tamyd-tasad-tosud-tarod-tynox' => 'P-i-n-e-a-p-p-l-e',
            'xipol-farel-puxex'                                     => 'ksmz',
        ];

        foreach ($expectedOutputs as $input => $output) {
            $result = (new Decoder())->decode($input);
            $this->assertSame($result, $output);
        }
    }
}
