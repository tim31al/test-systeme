<?php

declare(strict_types=1);

namespace App\Tests\Functional\Validator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentValidatorTest extends KernelTestCase
{
    public function testValidPrice(): void
    {
        self::bootKernel();

        /** @var \App\Validator\PaymentValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'    => 1,
            'couponCode' => 'P123',
            'taxNumber'  => 'DE123123123',
        ];

        $errors = $validator->validate($data);
        $this->assertCount(0, $errors);
    }

    public function testNotValidPrice(): void
    {
        self::bootKernel();

        /** @var \App\Validator\PaymentValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'    => -1,
            'couponCode' => 'D123',
            'taxNumber'  => 'DE123123123',
        ];

        $errors = $validator->validate($data);
        $this->assertCount(1, $errors);

        $this->assertArrayHasKey('product', $errors);
        $this->assertCount(1, $errors['product']);
    }

    public function testNotValidWithNullData(): void
    {
        self::bootKernel();

        /** @var \App\Validator\PaymentValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = null;

        $errors = $validator->validate($data);
        $this->assertCount(2, $errors);
    }

    public function testValidPurchase(): void
    {
        self::bootKernel();

        /** @var \App\Validator\PaymentValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'          => 1,
            'couponCode'       => 'D12',
            'taxNumber'        => 'FRAZ123123123',
            'paymentProcessor' => 'paypal',
        ];

        $errors = $validator->validate($data, true);
        $this->assertCount(0, $errors);
    }

    public function testInvalidPurchase(): void
    {
        self::bootKernel();

        /** @var \App\Validator\PaymentValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'          => 1,
            'couponCode'       => '12',
            'taxNumber'        => 'FRAZ123123123',
            'paymentProcessor' => 'paypal',
        ];

        $errors = $validator->validate($data, true);
        $this->assertCount(1, $errors);
    }
}
