<?php

declare(strict_types=1);

namespace App\Tests\helper;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait ApiTrait
{
    /**
     * @param \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
     *
     * @return array<string, mixed>
     */
    protected function getResponseData(KernelBrowser $client): array
    {
        $content = $client->getResponse()->getContent();
        if (false === $content) {
            throw new RuntimeException('Client content is not string.');
        }

        return json_decode($content, true);
    }
}