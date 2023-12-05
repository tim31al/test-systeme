<?php

declare(strict_types=1);

namespace App\Interface;

interface PaymentInterface
{
    /**
     * @param string $processor имя процессора для платежа
     * @param mixed $price
     * @return bool
     */
    public function payment(string $processor, mixed $price): bool;
}