<?php

declare(strict_types=1);

namespace App\Interface;

interface PurchaseInterface
{
    /**
     * Оплата товара.
     *
     * @param string $processor имя процессора для платежа
     * @param float  $price     цена продукта
     *
     * @throws \App\Exception\PaymentException
     *
     * @return void
     */
    public function payment(string $processor, float $price): void;
}