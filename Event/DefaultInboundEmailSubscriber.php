<?php

namespace neyric\InboundEmailBundle\Event;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Psr\Log\LoggerInterface;

class DefaultInboundEmailSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            InboundEmailEvent::class => ['onInboundEmail', -255], // lowest priority
        ];
    }

    public function onInboundEmail(InboundEmailEvent $event)
    {
        $this->logger->warning("InboundEmail DefaultInboundEmailSubscriber: no handler for this inbound email", $event->toArray());

        $event->stopPropagation();
    }
}