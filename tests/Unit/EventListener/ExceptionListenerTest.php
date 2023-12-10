<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\ExceptionListener;
use App\Exception\LoggedException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class ExceptionListenerTest extends TestCase
{
    public function testHandleNotLoggedException(): void
    {
        $exception = new Exception('Test');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with($exception);

        $kernel  = $this->createMock(KernelInterface::class);
        $request = $this->createMock(Request::class);

        $event = new ExceptionEvent($kernel, $request, 1, $exception);

        $listener = new ExceptionListener($logger);

        $listener($event);

        $this->assertSame($event->getResponse()->getStatusCode(), 500);
    }

    public function testHandleLoggedException(): void
    {
        $exception = new LoggedException('Logged exception message.');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error');

        $kernel  = $this->createMock(KernelInterface::class);
        $request = $this->createMock(Request::class);

        $event = new ExceptionEvent($kernel, $request, 1, $exception);

        $listener = new ExceptionListener($logger);

        $listener($event);

        $this->assertSame(400, $event->getResponse()->getStatusCode());

        $data = json_decode($event->getResponse()->getContent(), true);

        $this->assertIsArray($data['errors']);
        $this->assertSame('Logged exception message.', $data['errors'][0]);
    }
}
