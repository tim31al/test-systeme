<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalculatePriceActionTest extends WebTestCase
{
    public function testSuccess(): void
    {
        $client = static::createClient();
        $client->request('POST', '/calculate-price', ['product' => 1]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $client->getResponse()->getContent();
        $data    = json_decode($content, true);

        $this->assertIsArray($data);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame($data['message'], 'Success');
    }
}
