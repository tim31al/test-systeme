<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseActionTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('POST', '/purchase');

        $this->assertResponseIsSuccessful();

        $content = $client->getResponse()->getContent();
        $data    = json_decode($content, true);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Purchase!', $data['message']);
    }
}
