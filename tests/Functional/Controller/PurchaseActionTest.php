<?php

namespace App\Tests\Functional\Controller;

use App\Tests\helper\ApiTrait;
use App\Tests\helper\FunctionalTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseActionTest extends WebTestCase
{
    use ApiTrait;
    use FunctionalTrait;

    public function testSuccess(): void
    {
        $client = static::createClient();

        $product = $this->getProductRepository()->findOneBy(['price' => 100]);

        $client->request('POST', '/purchase', [
            'product'          => $product->getId(),
            'taxNumber'        => 'GR123456789',
            'couponCode'       => 'P10',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertResponseIsSuccessful();

        $data = $this->getResponseData($client);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Success.', $data['message']);
    }

    public function testBadRequest(): void
    {
        $client = static::createClient();

        $client->request('POST', '/purchase', [
            'product'          => 'bad-id',
            'taxNumber'        => 'GR123456789',
            'couponCode'       => 'P10',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = $this->getResponseData($client);

        $this->assertArrayHasKey('errors', $data);
    }
}
