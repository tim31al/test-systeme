<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Response;

readonly class SuccessDTO
{
    public function __construct(
        private string $message,
        private int $code = Response::HTTP_OK
    ) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}