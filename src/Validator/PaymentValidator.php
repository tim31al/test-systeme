<?php

declare(strict_types=1);

namespace App\Validator;

use App\Service\PaymentSystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PaymentValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ContainerInterface $container
    ) {}

    /**
     * Post данные.
     *
     * @param array<string, mixed>|null $data
     * @param bool                      $isPurchase если покупка
     *
     * @return array<string, array<int, mixed>> ошибки
     */
    public function validate(?array $data, bool $isPurchase = false): array
    {
        if (null === $data) {
            $data = [];
        }

        $violations = $this->validator->validate($data, $this->getConstraints($isPurchase));

        $errors = [];
        foreach ($violations as $violation) {
            $key            = str_replace(['[', ']'], '', $violation->getPropertyPath());
            $errors[$key][] = $violation->getMessage();
        }

        return $errors;
    }

    private function getConstraints(bool $isPurchase = false): Assert\Collection
    {
        $collection = [
            PaymentSystem::KEY_PRODUCT    => new Assert\Positive(),
            PaymentSystem::KEY_TAX_NUMBER => new Assert\Regex(
                pattern: '/^(DE|IT|GR|FR[A-Z]{2})\d{9}$/',
                message: 'Tax number is not valid.'
            ),
            PaymentSystem::KEY_COUPON_CODE => new Assert\Optional([
                new Assert\Regex(
                    pattern: '/^(D|P)\d+$/',
                    message: 'Coupon code is not valid.'
                ),
            ]),
        ];

        if ($isPurchase) {
            $collection[PaymentSystem::KEY_PAYMENT_PROCESSOR] = [
                new Assert\NotBlank(),
                new Assert\Choice($this->container->getParameter('app.payment_processors')),
            ];
        }

        return new Assert\Collection($collection);
    }
}