<?php

declare(strict_types=1);

namespace App\DTO;

use App\Interface\ResponseDTOInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class PriceResponseDTO implements ResponseDTOInterface
{
    public function __construct(
        #[Groups('api')]
        private float $price,
        private int $code = Response::HTTP_OK
    ) {}

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}