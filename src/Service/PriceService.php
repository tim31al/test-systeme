<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PriceDTO;

class PriceService
{
    public const COUNTRY_TAX = [
        'DE' => 19,
        'IT' => 22,
        'FR' => 20,
        'GR' => 24,
    ];

    /**
     * Расчет стоимости товара.
     *
     * @param \App\DTO\PriceDTO $dto
     *
     * @return float
     */
    public function calculate(PriceDTO $dto): float
    {
        $price = (float) $dto->getPrice();

        if (null !== $dto->getDiscount()) {
            $price -= $dto->isPercent() ? $this->getPercent($price, $dto->getDiscount()) : $dto->getDiscount();
        }

        // цена со скидкой + налог
        return $price + $this->getPercent($price, self::COUNTRY_TAX[$dto->getCountryCode()]);
    }

    /**
     * Процент от суммы.
     *
     * @param float $sum
     * @param int   $percent
     *
     * @return float
     */
    private function getPercent(float $sum, int $percent): float
    {
        return $sum * ($percent / 100);
    }
}