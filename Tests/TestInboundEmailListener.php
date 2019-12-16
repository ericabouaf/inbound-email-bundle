<?php 

namespace neyric\InboundEmailBundle\Tests;

class TestInboundEmailListener
{
    public $onInboundEmailInvoked = false;
    public $event = null;

    public function onInboundEmail($event)
    {
        $this->onInboundEmailInvoked = true;
        $this->event = $event;
    }

}