<?php

namespace neyric\InboundEmailBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use neyric\InboundEmailBundle\Tests\TestInboundEmailListener;

class SendgridControllerTest extends WebTestCase
{
    public function testHookHandlerAction()
    {
        $sendgridPayload = [
            "headers" => "Received: by mx0054p1mdw1.sendgrid.net with ...",
            "dkim" => "{@somedomain-com.20150623.gappssmtp.com : pass}",
            "to" => "Some Recipient <somerecipient@sendgrid.com>",
            "html" => "<div dir=\"ltr\">with content for InboundEmailBundle !!<br clear=\"all\"><div><br></div><div>Cool :)</div><div><br></div>\n",
            "from" => "Some Sender <sender@sendgrid.com>",
            "text" => "with content for InboundEmailBundle !!\r\n\r\nCool :)\r\n\r\n-- \r\SomeOne\r\nWith a signature\r\nMyCompany\n",
            "sender_ip" => "127.0.0.1",
            "envelope" => "{\"to\":[\"somerecipient@sendgrid.com\"],\"from\":\"sender@sendgrid.com\"}",
            "attachments" => "0",
            "subject" => "A test for InboundEmailBundle",
            "charsets" => "{\"to\":\"UTF-8\",\"html\":\"UTF-8\",\"subject\":\"UTF-8\",\"from\":\"UTF-8\",\"text\":\"UTF-8\"}",
            "SPF" => "pass",
        ];

        $client = static::createClient();

        // Access the kernel container (booted from createClient)
        $container = static::$kernel->getContainer();

        // Subscribe a test listener
        $dispatcher = $container->get('event_dispatcher');
        $listener = new TestInboundEmailListener();
        $dispatcher->addListener(InboundEmailEvent::class, [$listener, 'onInboundEmail']);

        $crawler = $client->request('POST', '/inbound_email/sendgrid/hook_handler', $sendgridPayload);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($listener->onInboundEmailInvoked);

        $event = $listener->event;
        $this->assertEquals('sender@sendgrid.com', $event->getFrom());
        $this->assertEquals("A test for InboundEmailBundle", $event->getSubject());

        // Test the visibleText EmailReplyParser
        $this->assertEquals("with content for InboundEmailBundle !!\n\nCool :)", $event->getVisibleText());
    }


    public function testHookHandlerActionWithoutHtmlPart()
    {
        $sendgridPayload = [
            "headers" => "Received: by mx0054p1mdw1.sendgrid.net with ...",
            "dkim" => "{@somedomain-com.20150623.gappssmtp.com : pass}",
            "to" => "Some Recipient <somerecipient@sendgrid.com>",
            // "html" => "<div dir=\"ltr\">with content for InboundEmailBundle !!<br clear=\"all\"><div><br></div><div>Cool :)</div><div><br></div>\n",
            "from" => "Some Sender <sender@sendgrid.com>",
            "text" => "with content for InboundEmailBundle !!\r\n\r\nCool :)\r\n\r\n-- \r\SomeOne\r\nWith a signature\r\nMyCompany\n",
            "sender_ip" => "127.0.0.1",
            "envelope" => "{\"to\":[\"somerecipient@sendgrid.com\"],\"from\":\"sender@sendgrid.com\"}",
            "attachments" => "0",
            "subject" => "A test for InboundEmailBundle",
            "charsets" => "{\"to\":\"UTF-8\",\"html\":\"UTF-8\",\"subject\":\"UTF-8\",\"from\":\"UTF-8\",\"text\":\"UTF-8\"}",
            "SPF" => "pass",
        ];

        $client = static::createClient();

        // Access the kernel container (booted from createClient)
        $container = static::$kernel->getContainer();

        // Subscribe a test listener
        $dispatcher = $container->get('event_dispatcher');
        $listener = new TestInboundEmailListener();
        $dispatcher->addListener(InboundEmailEvent::class, [$listener, 'onInboundEmail']);

        $crawler = $client->request('POST', '/inbound_email/sendgrid/hook_handler', $sendgridPayload);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($listener->onInboundEmailInvoked);

        $event = $listener->event;
        $this->assertEquals('sender@sendgrid.com', $event->getFrom());
        $this->assertEquals("A test for InboundEmailBundle", $event->getSubject());

        // Test the visibleText EmailReplyParser
        $this->assertEquals("with content for InboundEmailBundle !!\n\nCool :)", $event->getVisibleText());
    }

}
