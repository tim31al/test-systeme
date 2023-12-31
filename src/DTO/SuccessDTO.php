<?php

declare(strict_types=1);

namespace App\DTO;

use App\Interface\ResponseDTOInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute\Groups;

readonly class SuccessDTO implements ResponseDTOInterface
{
    public function __construct(
        #[Groups('api')]
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