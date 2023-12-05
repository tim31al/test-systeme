<?php

declare(strict_types=1);

namespace App\Tests\Unit\DTO;

use App\DTO\PriceDTO;
use LogicException;
use PHPUnit\Framework\TestCase;

class PriceDTOTest extends TestCase
{
    public function testBadCountryCode(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Country code "de" not support.');

        $dto = new PriceDTO(100, 'de');
    }
}
