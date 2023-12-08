<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Helper\ApiTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalculatePriceActionTest extends WebTestCase
{
    use ApiTrait;

    public function testSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/calculate-price', [
            'product'   => 1,
            'taxNumber' => 'DE123123123',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $data = static::getResponseData($client);

        $this->assertIsArray($data);

        $this->assertCount(1, $data);
        $this->assertArrayHasKey('price', $data);
    }

    public function testBadRequest(): void
    {
        $client = static::createClient();
        $client->request('POST', '/calculate-price', [
            'product'   => 1,
            'taxNumber' => 'GR123',
        ]);

        $this->assertResponseStatusCodeSame(400);

        $data = static::getResponseData($client);

        $this->assertArrayHasKey('errors', $data);
        $this->assertEquals(['taxNumber' => ['Tax number is not valid.']], $data['errors']);
    }
}
