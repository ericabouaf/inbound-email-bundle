<?php

namespace neyric\InboundEmailBundle\Controller;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use EmailReplyParser\EmailReplyParser;
use Psr\Log\LoggerInterface;

class AbstractWebhookController
{
    protected $eventDispatcher;
    protected $logger;
    protected $visible_text_enabled;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger,
        bool $visible_text_enabled
    )
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->visible_text_enabled = $visible_text_enabled;
    }

    protected function dispatch(InboundEmailEvent $event)
    {
        if ($this->visible_text_enabled) {
            $this->parseVisibleText($event);
        }

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * Fill the `visibleText` using EmailReplyParser
     */
    protected function parseVisibleText(InboundEmailEvent $event)
    {
        $text = $event->getText();
        
        $visibleText = EmailReplyParser::parseReply($text);

        $event->setVisibleText($visibleText);
    }

}