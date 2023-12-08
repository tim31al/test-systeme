<?php

declare(strict_types=1);

namespace App\DTO;

use App\Interface\ResponseDTOInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute\Groups;

class BadRequestDTO implements ResponseDTOInterface
{
    /**
     * @var array <string, mixed>
     */
    #[Groups('api')]
    private array $errors;
    private int $code;

    /**
     * @param array<string, mixed> $errors
     * @param int                  $code
     */
    public function __construct(array $errors, int $code = Response::HTTP_BAD_REQUEST)
    {
        $this->errors = $errors;
        $this->code   = $code;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}