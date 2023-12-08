<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\PaymentException;
use App\Interface\PaymentInterface;
use Exception;
use LogicException;
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

    public function payment(string $processor, float $price): bool
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

            return true;
        }

        if ($runner instanceof StripePaymentProcessor) {
            return $runner->processPayment($price);
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