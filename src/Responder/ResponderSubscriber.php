<?php

namespace Kamyshev\ResponderBundle\Responder;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponderSubscriber implements EventSubscriberInterface
{
    /** @var ResponderInterface[] $responders */
    private $responders;

    public function __construct(iterable $responders)
    {
        $this->responders = (function (ResponderInterface ...$responders) {
            return $responders;
        })(...$responders);
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();
        $meta = new ResultMetadata(get_class($result));

        $specificResponder = array_find(
            $this->responders,
            function (ResponderInterface $responder) use ($meta, $result) {
                return $responder->supports($result, $meta);
            }
        );

        if ($specificResponder) {
            $response = $specificResponder->make($result, $meta);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'onKernelView',
        ];
    }
}
