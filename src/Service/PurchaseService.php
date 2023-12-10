<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\PaymentException;
use App\Interface\PurchaseInterface;
use Exception;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

/**
 * Платежный сервис.
 * Поддерживаемые типы процессоров в app.payment_processors.
 */
class PurchaseService implements PurchaseInterface
{
    /**
     * @var array<int, string> Допустимые типы процессоров оплаты
     */
    private array $processors;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->processors = (array) $this->container->getParameter('app.payment_processors');
    }

    public function payment(string $processor, float $price): void
    {
        if (!$this->support($processor)) {
            throw new LogicException(sprintf('Processor "%s" not found.', $processor));
        }

        $runner = $this->container->get($processor);

        if ($runner instanceof PaypalPaymentProcessor) {
            $price = (int) ($price * 100);

            try {
                $runner->pay($price);
            } catch (Exception $e) {
                throw new PaymentException($e->getMessage());
            }

            return;
        }

        if ($runner instanceof StripePaymentProcessor) {
            $isSuccess = $runner->processPayment($price);
            if (!$isSuccess) {
                throw new PaymentException('Operation not success.');
            }

            return;
        }

        throw new LogicException('Code is unreachable.');
    }

    /**
     * Поддерживаемые типы процессоров.
     *
     * @param string $processor
     *
     * @return bool
     */
    private function support(string $processor): bool
    {
        return in_array($processor, $this->processors);
    }
}