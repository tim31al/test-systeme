<?php

declare(strict_types=1);

namespace App\Interface;

interface PaymentInterface
{
    /**
     * Оплата товара.
     *
     * @param string $processor имя процессора для платежа
     * @param float  $price     цена продукта
     *
     * @throws \App\Exception\PaymentException
     *
     * @return bool
     */
    public function payment(string $processor, float $price): bool;
}