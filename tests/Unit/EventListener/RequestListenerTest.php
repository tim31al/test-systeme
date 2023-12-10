<?php

namespace App\Tests\Unit\EventListener;

use App\EventListener\RequestListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestListenerTest extends TestCase
{
    public function testReturnWhenRequestNotPost(): void
    {
        $request = $this->createMock(Request::class);
        $request
            ->method('getMethod')
            ->willReturn('GET');
        $request
            ->expects(self::never())
            ->method('getContent');

        $event = $this->createMock(RequestEvent::class);
        $event
            ->method('getRequest')
            ->willReturn($request);

        $listener = new RequestListener();
        $listener($event);
    }

    public function testReturnWhenHeadersNotContainsContentType(): void
    {
        $request = new Request([], ['key1' => 'val1']);

        $event = $this->createMock(RequestEvent::class);
        $event
            ->method('getRequest')
            ->willReturn($request);

        $listener = new RequestListener();
        $listener($event);

        $data = $request->request->all();
        $this->assertCount(1, $data);

        $this->assertSame('val1', $data['key1']);
    }

    public function testReturnWhenHeadersContentTypeText(): void
    {
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'text/plain'], 'text');
        $request->setMethod('POST');

        $event = $this->createMock(RequestEvent::class);
        $event
            ->method('getRequest')
            ->willReturn($request);

        $listener = new RequestListener();
        $listener($event);

        $this->assertSame('text', $request->getContent());
    }

    public function testSetJson(): void
    {
        $content = '{"key1":"val1","key2":"val2"}';
        $request = new Request([], [], [], [], [], ['CONTENT_TYPE' => 'application/json'], $content);
        $request->setMethod('POST');

        $event = $this->createMock(RequestEvent::class);
        $event
            ->method('getRequest')
            ->willReturn($request);

        $listener = new RequestListener();
        $listener($event);

        $this->assertSame('application/json', $request->headers->get('Content-Type'));

        $data = $request->request->all();
        $this->assertCount(2, $data);
        $this->assertSame('val1', $data['key1']);
        $this->assertSame('val2', $data['key2']);
    }
}
