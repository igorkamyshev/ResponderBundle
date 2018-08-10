<?php

namespace Kamyshev\Tests\ResponderBundle\Responder;

use Kamyshev\ResponderBundle\Responder\ResponderInterface;
use Kamyshev\ResponderBundle\Responder\ResponderSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponderSubscriberTest extends TestCase
{
    public function testOnKernelViewWithSuitableResponder()
    {
        $responseSet = false;
        $response = null;

        $event = $this->mockEvent(function ($res) use (&$responseSet, &$response) {
            $responseSet = true;
            $response = $res;
        });

        $subscriber = new ResponderSubscriber([
            $this->mockSuitableResponder('ok'),
        ]);

        $subscriber->onKernelView($event);

        $this->assertTrue($responseSet);
        $this->assertEquals('ok', $response->getContent());
    }

    public function testOnKernelViewWithDifferentResponders()
    {
        $responseSet = false;
        $response = null;

        $event = $this->mockEvent(function ($res) use (&$responseSet, &$response) {
            $responseSet = true;
            $response = $res;
        });

        $subscriber = new ResponderSubscriber([
            $this->mockSuitableResponder('ok'),
            $this->mockUnsuitableResponder(),
        ]);

        $subscriber->onKernelView($event);

        $this->assertTrue($responseSet);
        $this->assertEquals('ok', $response->getContent());
    }

    public function testOnKernelViewWithManySuitableResponders()
    {
        $responseSet = false;
        $response = null;

        $event = $this->mockEvent(function ($res) use (&$responseSet, &$response) {
            $responseSet = true;
            $response = $res;
        });

        $subscriber = new ResponderSubscriber([
            $this->mockSuitableResponder('ok'),
            $this->mockSuitableResponder('not ok'),
        ]);

        $subscriber->onKernelView($event);

        $this->assertTrue($responseSet);
        $this->assertEquals('ok', $response->getContent());
    }

    public function testOnKernelViewWithUnsuitableResponder()
    {
        $responseSet = false;
        $response = null;

        $event = $this->mockEvent(function ($res) use (&$responseSet, &$response) {
            $responseSet = true;
            $response = $res;
        });

        $subscriber = new ResponderSubscriber([
            $this->mockUnsuitableResponder(),
        ]);

        $subscriber->onKernelView($event);

        $this->assertFalse($responseSet);
        $this->assertNull($response);
    }

    private function mockSuitableResponder(string $content)
    {
        $responder = $this->createMock(ResponderInterface::class);

        $responder
            ->method('supports')
            ->willReturn(true);

        $responder
            ->method('make')
            ->willReturn(new Response($content));

        return $responder;
    }

    private function mockUnsuitableResponder()
    {
        $responder = $this->createMock(ResponderInterface::class);

        $responder
            ->method('supports')
            ->willReturn(false);

        return $responder;
    }

    private function mockEvent(callable $onSetResponse)
    {
        $event = $this->createMock(GetResponseForControllerResultEvent::class);

        $event
            ->method('getControllerResult')
            ->willReturn(new \stdClass());

        $event
            ->method('setResponse')
            ->willReturnCallback($onSetResponse);

        return $event;
    }
}
