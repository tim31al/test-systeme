<?php

namespace App\Tests\Functional\Validator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ClientDataValidator extends KernelTestCase
{
    public function testValidPrice(): void
    {
        self::bootKernel();

        /** @var \App\Validator\ClientDataValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'    => 1,
            'couponCode' => '123',
            'taxNumber'  => 'DE123123123',
        ];

        $errors = $validator->validate($data);
        $this->assertCount(0, $errors);
    }

    public function testNotValidPrice(): void
    {
        self::bootKernel();

        /** @var \App\Validator\ClientDataValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'    => -1,
            'couponCode' => '123',
            'taxNumber'  => 'DE123123123',
        ];

        $errors = $validator->validate($data);
        $this->assertCount(1, $errors);

        $this->assertArrayHasKey('product', $errors);
        $this->assertCount(1, $errors['product']);
    }

    public function testValidPurchase(): void
    {
        self::bootKernel();

        /** @var \App\Validator\ClientDataValidator $validator */
        $validator = static::getContainer()->get('test.Validator');

        $data = [
            'product'          => 1,
            'couponCode'       => '123',
            'taxNumber'        => 'FRAZ123123123',
            'paymentProcessor' => 'paypal',
        ];

        $errors = $validator->validate($data, true);
        $this->assertCount(0, $errors);
    }
}
