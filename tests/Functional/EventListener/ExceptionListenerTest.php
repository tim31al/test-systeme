<?php

namespace App\Tests\Functional\EventListener;

use App\Tests\Helper\ApiTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionListenerTest extends WebTestCase
{
    use ApiTrait;

    public function testHandleException(): void
    {
        $client = static::createClient();

        $client->catchExceptions(false);
        $this->expectException(NotFoundHttpException::class);
        $client->request('GET', '/');

        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(500);

        $data = static::getResponseData($client);
        $this->assertArrayHasKey('error', $data);
    }
}
