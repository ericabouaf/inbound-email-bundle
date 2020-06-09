<?php

namespace neyric\InboundEmailBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class InboundEmailEvent extends Event
{
    public const NAME = 'inbound.email';
    
    protected $from;
    protected $to;
    protected $subject;
    protected $html;
    protected $text;

    protected $visibleText;

    public function __construct(string $from, string $to, string $subject, ?string $text, ?string $html)
    {
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->text = $text;
        $this->html = $html;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }
    
    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
    
    public function getVisibleText(): string
    {
        return $this->visibleText;
    }

    public function setVisibleText(string $visibleText)
    {
        $this->visibleText = $visibleText;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function toArray(): array
    {
        return [
            'from' => $this->getFrom(),
            'to' => $this->getTo(),
            'subject' => $this->getSubject(),
            'html' => $this->getHtml(),
            'text' => $this->getText(),
            'visibleText' => $this->getVisibleText(),
        ];
    }

}
