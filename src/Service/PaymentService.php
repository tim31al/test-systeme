<?php

declare(strict_types=1);

namespace App\Service;

use App\Interface\PaymentInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;


class PaymentService implements PaymentInterface
{
    /**
     * @var array<int, string> Допустимые типы процессоров оплаты
     */
    private array $processors;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->processors = (array) $this->container->getParameter('app.payment_processors');
    }

    /**
     * @throws \Exception
     */
    public function payment(string $processor, mixed $price): bool
    {
        if (!$this->support($processor)) {
            throw new \LogicException(sprintf('Processor "%s" not found.', $processor));
        }

        $runner = $this->container->get($processor);

        if ($runner instanceof PaypalPaymentProcessor) {
            if (!is_int($price)) {
                throw new \LogicException(sprintf('Processor "%s" require int price.', $processor));
            }

            $runner->pay($price);
            return true;
        }

        if ($runner instanceof StripePaymentProcessor) {
            if (!is_float($price)) {
                throw new \LogicException(sprintf('Processor "%s" require float price.', $processor));
            }
            return $runner->processPayment($price);
        }

        throw new \LogicException('Code is unreachable.');
    }

    /**
     * Поддерживаемые типы процессоров.
     */
    private function support(string $processor): bool
    {
        return in_array($processor, $this->processors);
    }
}