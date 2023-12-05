<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class TaxNumberRegexTest extends TestCase
{
    /**
     * @return array<int, mixed>
     */
    public function dataProvider(): array
    {
        return [
            ['DE123', 0],
            ['DE123123123', 1],
            ['IT012345678', 1],
            ['it012345678', 0],
            ['GR012345678', 1],
            ['GR0123456789', 0],
            ['GRa', 0],
            ['FR012345678', 0],
            ['FRAT012345678', 1],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $subject
     * @param int    $expected
     */
    public function testRegex(string $subject, int $expected): void
    {
        $pattern = '/^(DE|IT|GR|FR[A-Z]{2})\d{9}$/';

        $result = preg_match($pattern, $subject);
        $this->assertSame($expected, $result);
    }
}
