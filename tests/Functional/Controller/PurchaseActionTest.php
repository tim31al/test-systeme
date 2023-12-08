<?php

namespace App\Tests\Functional\Controller;

use App\Tests\Helper\ApiTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PurchaseActionTest extends WebTestCase
{
    use ApiTrait;

    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('POST', '/purchase', [
            'product'          => 1,
            'taxNumber'        => 'GR123456789',
            'paymentProcessor' => 'paypal',
        ]);

        $this->assertResponseIsSuccessful();

        $data = $this->getResponseData($client);

        $this->assertArrayHasKey('message', $data);
        $this->assertSame('Success.', $data['message']);
    }
}
