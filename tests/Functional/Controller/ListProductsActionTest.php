<?php

namespace App\Tests\Functional\Controller;

use App\Tests\helper\ApiTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListProductsActionTest extends WebTestCase
{
    use ApiTrait;

    public function testList(): void
    {
        $client  = static::createClient();
        $client->request('GET', '/products');

        $this->assertResponseIsSuccessful();

        $data = $this->getResponseData($client);

        $this->assertCount(3, $data);
    }
}
