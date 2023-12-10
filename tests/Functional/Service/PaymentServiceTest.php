<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Exception\PaymentException;
use App\Service\PurchaseService;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentServiceTest extends KernelTestCase
{
    private PurchaseService $service;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->service = static::getContainer()->get('test.PurchaseService');
    }

    /**
     * @throws \App\Exception\PaymentException
     */
    public function testThrowLogicException(): void
    {
        $method = 'test';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Processor "test" not found.');
        $this->service->payment($method, 12);
    }

    public function testPaypal(): void
    {
        $method = 'paypal';
        $price  = 12;

        $isSuccess = true;

        try {
            $this->service->payment($method, $price);
        } catch (PaymentException $e) {
            $isSuccess = false;
        }

        $this->assertTrue($isSuccess);
    }

    public function testPaypalExpectException(): void
    {
        $method = 'paypal';

        $this->expectException(PaymentException::class);
        $this->service->payment($method, 10000001);
    }

    /**
     * @return array<string, array<int, float|bool>>
     */
    public function priceProvider(): array
    {
        return [
            '120.0' => [120.0, true],
            '80.1'  => [80.1, false],
        ];
    }

    /**
     * @dataProvider priceProvider
     *
     * @param float $price
     * @param bool  $expected
     */
    public function testStripe(float $price, bool $expected): void
    {
        $method = 'stripe';

        $isSuccess = true;

        try {
            $this->service->payment($method, $price);
        } catch (PaymentException $exception) {
            $isSuccess = false;
        }

        $this->assertSame($expected, $isSuccess);
    }
}
