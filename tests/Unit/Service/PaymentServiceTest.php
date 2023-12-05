<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Service\PaymentService;
use Exception;
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaymentServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testCodeUnreachable(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('getParameter')
            ->with('app.payment_processors')
            ->willReturn(['paypal', 'stripe']);

        $container
            ->method('get')
            ->with('paypal')
            ->willReturn(new stdClass());

        $service = new PaymentService($container);

        $this->expectException(LogicException::class);
        $service->payment('paypal', 100.1);
    }

    /**
     * @return array<string, array<int, float|int>>
     */
    public function priceProvider(): array
    {
        return [
            '12.5'  => [12.5, 1250],
            '.1'    => [.1, 10],
            '.01'   => [.01, 1],
            '300.5' => [300.5, 30050],
        ];
    }

    /**
     * @dataProvider priceProvider
     *
     * @param float $price
     * @param int   $expected
     *
     * @throws Exception
     */
    public function testPaypalWithFloatValue(float $price, int $expected): void
    {
        $paypal = $this->createMock(PaypalPaymentProcessor::class);
        $paypal
            ->expects(self::once())
            ->method('pay')
            ->with($expected);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('getParameter')
            ->with('app.payment_processors')
            ->willReturn(['paypal', 'stripe']);

        $container
            ->method('get')
            ->with('paypal')
            ->willReturn($paypal);

        $service = new PaymentService($container);

        $service->payment('paypal', $price);
    }
}
