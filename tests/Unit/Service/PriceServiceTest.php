<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\PriceDTO;
use App\Service\PriceService;
use PHPUnit\Framework\TestCase;

class PriceServiceTest extends TestCase
{
    /**
     * @return array<string, array<int, string|float>>
     */
    public static function nalogProvider(): array
    {
        return [
            'DE' => ['DE', 119.0],
            'IT' => ['IT', 122.0],
            'FR' => ['FR', 120.0],
            'GR' => ['GR', 124.0],
        ];
    }

    /**
     * @dataProvider nalogProvider
     *
     * @param string $countryCode
     * @param float  $expected
     */
    public function testNalog(string $countryCode, float $expected): void
    {
        $dto = new PriceDTO(100, $countryCode);

        $service = new PriceService();
        $sum     = $service->process($dto);

        $this->assertSame($expected, $sum);
    }

    public function testPriceWithDiscount(): void
    {
        $dto     = new PriceDTO(100, 'GR', null, 6);
        $service = new PriceService();
        $sum     = $service->process($dto);

        $this->assertSame(116.56, $sum);
    }
}
