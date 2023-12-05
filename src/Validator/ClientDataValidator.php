<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ClientDataValidator
{
    public function __construct(
        private ValidatorInterface $validator,
        private ContainerInterface $container
    ) {}

    /**
     * Post данные.
     *
     * @param array<string, mixed>|null $data
     * @param bool                      $isPurchase
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
            'product'   => new Assert\Positive(),
            'taxNumber' => new Assert\Regex(
                pattern: '/^(DE|IT|GR|FR[A-Z]{2})\d{9}$/',
                message: 'Tax number is not valid.'
            ),
            // TODO: выяснить формат
            'couponCode' => new Assert\Optional([
                new Assert\NotBlank(),
            ]),
        ];

        if ($isPurchase) {
            $collection['paymentProcessor'] = [
                new Assert\NotBlank(),
                new Assert\Choice($this->container->getParameter('app.payment_processors')),
            ];
        }

        return new Assert\Collection($collection);
    }
}