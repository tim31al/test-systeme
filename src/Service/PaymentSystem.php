<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BadRequestDTO;
use App\DTO\PriceDTO;
use App\DTO\PriceResponseDTO;
use App\DTO\SuccessDTO;
use App\Exception\LoggedException;
use App\Exception\PaymentException;
use App\Interface\PaymentInterface;
use App\Interface\ResponseDTOInterface;
use App\Validator\PaymentValidator;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

readonly class PaymentSystem
{
    public const KEY_PRODUCT           = 'product';
    public const KEY_TAX_NUMBER        = 'taxNumber';
    public const KEY_COUPON_CODE       = 'couponCode';
    public const KEY_PAYMENT_PROCESSOR = 'paymentProcessor';

    private const DEFAULT_ERROR_MESSAGE = 'Internal Server Error.';

    public function __construct(
        private PaymentValidator $validator,
        private PaymentInterface $paymentService,
        private PriceService $priceService,
        private LoggerInterface $logger
    ) {}

    /**
     * Получить цену продукта.
     *
     * @param array<string, mixed>|null $data
     *
     * @throws Throwable
     *
     * @return \App\Interface\ResponseDTOInterface
     */
    public function getPrice(?array $data): ResponseDTOInterface
    {
        try {
            $errors = $this->validator->validate($data);
            if (0 !== count($errors)) {
                return new BadRequestDTO($errors);
            }

            $price = $this->calculatePrice($data);

            return new PriceResponseDTO($price);
        } catch (Throwable $e) {
            $this->logError($e);

            throw new LoggedException(self::DEFAULT_ERROR_MESSAGE, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Покупка товара.
     *
     * @param array<string, mixed>|null $data
     *
     * @throws \App\Exception\LoggedException
     *
     * @return \App\Interface\ResponseDTOInterface
     */
    public function pay(?array $data): ResponseDTOInterface
    {
        try {
            $errors = $this->validator->validate($data, true);
            if (0 !== count($errors)) {
                return new BadRequestDTO($errors);
            }

            $price = $this->calculatePrice($data);

            $isPay = $this->paymentService->payment($data[self::KEY_PAYMENT_PROCESSOR], $price);

            if ($isPay) {
                return new SuccessDTO('Success.');
            }

            throw new PaymentException('Operation not success.');
        } catch (Exception $e) {
            $this->logError($e);
            $message = $e instanceof PaymentException ? $e->getMessage() : self::DEFAULT_ERROR_MESSAGE;

            throw new LoggedException($message);
        }
    }

    /**
     * @param array<string, int|string> $data
     *
     * @return float
     */
    private function calculatePrice(array $data): float
    {
        $countryCode = substr($data[self::KEY_TAX_NUMBER], 0, 2);

        $dto = new PriceDTO(100, $countryCode);

        return $this->priceService->calculate($dto);
    }

    /**
     * Лог ошибок.
     *
     * @param Throwable $e
     *
     * @return void
     */
    private function logError(Throwable $e): void
    {
        $this->logger->error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
    }
}