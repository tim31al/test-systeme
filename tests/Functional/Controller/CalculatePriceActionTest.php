<?php

namespace App\Tests\Functional\Controller;

use App\Tests\helper\ApiTrait;
use App\Tests\helper\FunctionalTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalculatePriceActionTest extends WebTestCase
{
    use ApiTrait;
    use FunctionalTrait;

    public function testSuccess(): void
    {
        $client = static::createClient();

        $product = $this->getProductRepository()->findOneBy(['name' => 'Iphone']);

        $client->request('POST', '/calculate-price', [
            'product'    => $product->getId(),
            'taxNumber'  => 'DE123123123',
            'couponCode' => 'D15',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = static::getResponseData($client);

        $this->assertIsArray($data);

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('price', $data);
    }

    public function testSuccessWithoutCoupon(): void
    {
        $client = static::createClient();

        $product = $this->getProductRepository()->findOneBy(['price' => 10]);

        $client->request('POST', '/calculate-price', [
            'product'   => $product->getId(),
            'taxNumber' => 'GR333555777',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = static::getResponseData($client);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('price', $data);
    }

    public function testBadRequest(): void
    {
        $client = static::createClient();

        $product = $this->getProductRepository()->findOneBy(['name' => 'Iphone']);

        $client->request('POST', '/calculate-price', [
            'product'   => $product->getId(),
            'taxNumber' => 'GR123',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = static::getResponseData($client);

        $this->assertArrayHasKey('errors', $data);
        $this->assertArrayHasKey('taxNumber', $data['errors']);
    }
}
