<?php

declare(strict_types=1);

namespace App\DTO;

use App\Service\PriceService;
use LogicException;

readonly class PriceDTO
{
    public function __construct(
        private int $price,
        private string $countryCode,
        private ?int $discount = null,
        private ?int $discountPercent = null
    ) {
        if (!array_key_exists($this->countryCode, PriceService::COUNTRY_TAX)) {
            throw new LogicException(sprintf('Country code "%s" not support.', $this->countryCode));
        }
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    // TODO: выяснить применяется ли?
    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function getDiscountPercent(): ?int
    {
        return $this->discountPercent;
    }
}