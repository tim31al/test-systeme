<?php

declare(strict_types=1);

namespace App\Tests\Functional\Service;

use App\Service\PaymentService;
use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentServiceTest extends KernelTestCase
{
    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->service = static::getContainer()->get('test.PaymentService');
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

    /**
     * @throws \App\Exception\PaymentException
     */
    public function testPaypal(): void
    {
        $method = 'paypal';
        $price  = 12;

        $result = $this->service->payment($method, $price);
        $this->assertTrue($result);
    }

    /**
     * @throws \App\Exception\PaymentException
     */
    public function testPaypalExpectException(): void
    {
        $method = 'paypal';

        $this->expectException(Exception::class);
        $result = $this->service->payment($method, 10000001);
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
     *
     * @throws \App\Exception\PaymentException
     */
    public function testStripe(float $price, bool $expected): void
    {
        $method = 'stripe';

        $result = $this->service->payment($method, $price);
        $this->assertSame($expected, $result);
    }
}
