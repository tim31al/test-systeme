<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\LoggedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Обработчик ошибок.
 */
#[AsEventListener(event: ExceptionEvent::class)]
readonly class ExceptionListener
{
    public function __construct(private LoggerInterface $logger) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof LoggedException) {
            $this->logger->error($exception);

            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        } else {
            $status = Response::HTTP_BAD_REQUEST;
        }

        $response = new JsonResponse(['errors' => [$exception->getMessage()]], $status);

        $event->setResponse($response);
    }
}