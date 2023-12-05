<?php

namespace App\Tests\Functional\Service;

use Exception;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentServiceTest extends KernelTestCase
{
    public function testThrowLogicException(): void
    {
        self::bootKernel();

        $service = static::getContainer()->get('test.PaymentService');

        $method = 'test';

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Processor "test" not found.');
        $service->payment($method, 12);
    }

    public function testPaypal(): void
    {
        self::bootKernel();

        $service = static::getContainer()->get('test.PaymentService');

        $method = 'paypal';

        $result = $service->payment($method, 12);
        $this->assertTrue($result);
    }

    public function testPaypalExpectException(): void
    {
        self::bootKernel();

        $service = static::getContainer()->get('test.PaymentService');

        $method = 'paypal';

        $this->expectException(Exception::class);
        $result = $service->payment($method, 10000001);
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
        self::bootKernel();

        $service = static::getContainer()->get('test.PaymentService');

        $method = 'stripe';

        $result = $service->payment($method, $price);
        $this->assertSame($expected, $result);
    }
}
