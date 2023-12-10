<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\BadRequestDTO;
use App\DTO\PriceDTO;
use App\DTO\PriceResponseDTO;
use App\DTO\SuccessDTO;
use App\Entity\Product;
use App\Exception\LoggedException;
use App\Exception\PaymentException;
use App\Interface\PurchaseInterface;
use App\Interface\ResponseDTOInterface;
use App\Repository\ProductRepository;
use App\Validator\PaymentValidator;
use Exception;
use LogicException;
use Psr\Log\LoggerInterface;

/**
 * Платежная система.
 * Возможности:
 *  - узнать цену;
 *  - провести платеж.
 */
readonly class PaymentSystem
{
    public const KEY_PRODUCT           = 'product';
    public const KEY_TAX_NUMBER        = 'taxNumber';
    public const KEY_COUPON_CODE       = 'couponCode';
    public const KEY_PAYMENT_PROCESSOR = 'paymentProcessor';

    /**
     * Узнать цену.
     */
    public const METHOD_PRICE = 'price';

    /**
     * Платеж.
     */
    public const METHOD_PAY = 'pay';

    public function __construct(
        private PaymentValidator $validator,
        private PurchaseInterface $paymentService,
        private PriceService $priceService,
        private LoggerInterface $logger,
        private ProductRepository $repository,
    ) {}

    /**
     * @param ?array $data
     * @param string $method
     *
     * @throws \App\Exception\LoggedException
     */
    public function process(?array $data, string $method): ResponseDTOInterface
    {
        if (!in_array($method, [self::METHOD_PAY, self::METHOD_PRICE])) {
            throw new LogicException('Method not allowed.');
        }

        try {
            $errors = $this->validator->validate($data, self::METHOD_PAY === $method);
            if (0 !== count($errors)) {
                return new BadRequestDTO($errors);
            }

            $product = $this->repository->find($data[self::KEY_PRODUCT]);
            if (!$product instanceof Product) {
                return new BadRequestDTO([sprintf('Product #%d not found.', $data[self::KEY_PRODUCT])]);
            }

            $price = $this->calculatePrice($product->getPrice(), $data);
            if (0 > $price) {
                return new BadRequestDTO([sprintf('Price (%d) cannot be less than zero.', $price)]);
            }

            // получить цену
            if (self::METHOD_PRICE === $method) {
                return new PriceResponseDTO($price);
            }

            // совершить платеж
            $this->paymentService->payment($data[self::KEY_PAYMENT_PROCESSOR], $price);

            return new SuccessDTO('Success.');
        } catch (Exception $e) {
            $this->logger->error($e);

            $message = $e instanceof PaymentException ? $e->getMessage() : 'Internal Server Error.';

            throw new LoggedException($message);
        }
    }

    /**
     * Получить цену продукта.
     *
     * @param int                       $price
     * @param array<string, int|string> $data
     *
     * @return float
     */
    private function calculatePrice(int $price, array $data): float
    {
        $countryCode = substr($data[self::KEY_TAX_NUMBER], 0, 2);

        if (array_key_exists(self::KEY_COUPON_CODE, $data)) {
            $discount  = (int) substr($data[self::KEY_COUPON_CODE], 1);
            $isPercent = str_starts_with($data[self::KEY_COUPON_CODE], 'P');
        } else {
            $discount  = null;
            $isPercent = false;
        }

        $dto = new PriceDTO($price, $countryCode, $discount, $isPercent);

        return $this->priceService->calculate($dto);
    }
}