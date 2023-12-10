<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST)]
final class RequestListener
{
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('POST' !== $request->getMethod()) {
            return;
        }

        if ('application/json' === $request->headers->get('content-type')) {
            $content = $request->getContent();
            $data    = json_decode($content, true);

            $request->request->replace($data);
        }
    }
}