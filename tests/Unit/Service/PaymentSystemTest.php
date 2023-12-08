<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Exception\LoggedException;
use App\Interface\PaymentInterface;
use App\Service\PaymentSystem;
use App\Service\PriceService;
use App\Validator\PaymentValidator;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PaymentSystemTest extends TestCase
{
    public function testGetPriceHandleException(): void
    {
        $exception = new Exception('Price exception.');
        $validator = $this->createMock(PaymentValidator::class);
        $validator
            ->method('validate')
            ->willThrowException($exception)
        ;

        $paymentService = $this->createMock(PaymentInterface::class);

        $priceService = $this->createMock(PriceService::class);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with('Price exception.')
        ;

        $service = new PaymentSystem($validator, $paymentService, $priceService, $logger);

        $this->expectException(LoggedException::class);
        $price = $service->getPrice([]);
    }
}
