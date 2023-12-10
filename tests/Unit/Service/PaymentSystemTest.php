<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\DTO\BadRequestDTO;
use App\Entity\Product;
use App\Exception\LoggedException;
use App\Interface\PurchaseInterface;
use App\Repository\ProductRepository;
use App\Service\PaymentSystem;
use App\Service\PriceService;
use App\Validator\PaymentValidator;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentSystemTest extends TestCase
{
    public function testMethodNotAllowed(): void
    {
        $validator         = $this->createMock(PaymentValidator::class);
        $paymentService    = $this->createMock(PurchaseInterface::class);
        $priceService      = $this->createMock(PriceService::class);
        $logger            = $this->createMock(LoggerInterface::class);
        $productRepository = $this->createMock(ProductRepository::class);

        $service = new PaymentSystem($validator, $paymentService, $priceService, $logger, $productRepository);

        $this->expectException(LogicException::class);
        $dto = $service->process([], 'bad-method');
    }

    public function testProductNotFound(): void
    {
        $validator = $this->createMock(PaymentValidator::class);
        $validator
            ->method('validate')
            ->willReturn([]);

        $paymentService = $this->createMock(PurchaseInterface::class);

        $priceService = $this->createMock(PriceService::class);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error')
        ;

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository
            ->method('find')
            ->willReturn(null);

        $service = new PaymentSystem($validator, $paymentService, $priceService, $logger, $productRepository);

        $dto = $service->process(['product' => 1], PaymentSystem::METHOD_PRICE);
    }

    public function testGetPriceNegative(): void
    {
        $validator = $this->createMock(PaymentValidator::class);
        $validator
            ->method('validate')
            ->willReturn([])
        ;

        $paymentService = $this->createMock(PurchaseInterface::class);

        $priceService = $this->createMock(PriceService::class);
        $priceService
            ->expects(self::once())
            ->method('calculate')
            ->willReturn(-1.2);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error')
        ;

        $product = new Product();
        $product
            ->setName('product')
            ->setPrice(50);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository
            ->method('find')
            ->willReturn($product);

        $service = new PaymentSystem($validator, $paymentService, $priceService, $logger, $productRepository);

        $data = [
            'product'   => 1,
            'taxNumber' => 'DE123123123',
        ];

        $dto = $service->process($data, PaymentSystem::METHOD_PRICE);
        $this->assertInstanceOf(BadRequestDTO::class, $dto);
    }

    public function testGetPriceHandlePaymentException(): void
    {
        $validator = $this->createMock(PaymentValidator::class);
        $validator
            ->method('validate')
            ->willReturn([])
        ;

        $paymentService = $this->createMock(PurchaseInterface::class);

        $exception = new Exception('Calculate exception.');

        $priceService = $this->createMock(PriceService::class);
        $priceService
            ->expects(self::once())
            ->method('calculate')
            ->willThrowException($exception);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with($exception)
        ;

        $product = new Product();
        $product
            ->setName('test')
            ->setPrice(100);

        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository
            ->method('find')
            ->willReturn($product);

        $service = new PaymentSystem($validator, $paymentService, $priceService, $logger, $productRepository);

        $data = [
            'product'    => 1,
            'taxNumber'  => 'DE123123123',
            'couponCode' => 'D1',
        ];

        $this->expectException(LoggedException::class);
        $this->expectExceptionMessage('Internal Server Error.');
        $dto = $service->process($data, PaymentSystem::METHOD_PRICE);
    }
}
