<?php

namespace neyric\InboundEmailBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use neyric\InboundEmailBundle\Tests\TestInboundEmailListener;

class MailjetControllerTest extends WebTestCase
{
    // Data sample from https://dev.mailjet.com/email/guides/parse-api/
    const MAILJET_TEST_PAYLOAD = [
        "Sender" => "pilot@mailjet.com",
        "Recipient" => "passenger@mailjet.com",
        "Date" => "20150410T160638",
        "From" => "Pilot <pilot@mailjet.com>",
        "Subject" => "Hey! It's Friday!",
        "Headers" => [
            "Return-Path" => ["<pilot@mailjet.com>"],
            "Received" => [
                "by 10.107.134.160 with HTTP; Fri, 10 Apr 2015 09:06:38 -0700 (PDT)"
            ],
            "DKIM-Signature" => [
                "v=1; a=rsa-sha256; c=relaxed/relaxed;        d=mailjet.com; s=google;        h=mime-version:date:message-id:subject:from:to:content-type;        bh=tsc4ruu5r5loLtAFUwhFp8BIbKzV0AYljT0+Bb/QwWI=;        b=............"
            ],
            "MIME-Version" => ["1.0"],
            "Content-Transfer-Encoding" => ["quoted-printable"],
            "Content-Type" => [
                "multipart/alternative; boundary=001a1141f3c406f1b2051360f37d"
            ],
            "X-CSA-Complaints" => ["whitelist-complaints@eco.de"],
            "List-Unsubscribe" => [
                "<mailto:unsub-e7221da9.org1.x61425y8x4pt@bnc3.mailjet.com>"
            ],
            "X-Google-DKIM-Signature" => [
                "v=1; a=rsa-sha256; c=relaxed/relaxed;        d=1e100.net; s=20130820;        h=x-gm-message-state:mime-version:date:message-id:subject:from:to         :content-type;        bh=tsc4ruu5r5loLtAFUwhFp8BIbKzV0AYljT0+Bb/QwWI=;        b=..........."
            ],
            "X-Gm-Message-State" => [
                "ALoCoQlJBEYSiauMbHc8RXQpv3sUJvPmYAd7exYJKZIZFRZtFkSHqDEP59rQK6oIp9mCwPKCirCL"
            ],
            "X-Received" => [
                "by 10.107.41.72 with SMTP id p69mr3774075iop.58.1428681998638; Fri, 10 Apr 2015 09:06:38 -0700 (PDT)"
            ],
            "Date" => "Fri, 10 Apr 2015 18:06:38 +0200",
            "Message-ID" => "<CAE5Zh0ZpHZ6G5DC+He5426a4RkVab7uWaTDwiMcHzOR=YB3urA@mail.gmail.com>",
            "Subject" => "Hey! It's Friday!",
            "From" => "Pilot <pilot@mailjet.com>",
            "To" => "passenger@mailjet.com"
        ],
        "Parts" => [
            [
                "Headers" => [
                    "Content-Type" => "text/plain; charset=UTF-8"
                ],
                "ContentRef" => "Text-part"
            ],
            [
                "Headers" => [
                    "Content-Type" => "text/html; charset=UTF-8",
                    "Content-Transfer-Encoding" => "quoted-printable"
                ],
                "ContentRef" => "Html-part"
            ]
        ],
        "Text-part" => "Hi,\n\nImportant notice: it's Friday. Friday *afternoon*, even!\n\n\nHave a *great* weekend!\nThe Anonymous Friday Teller\n",
        "Html-part" => "<div dir=\"ltr\">Hi,<div><br></div><div>Important notice: it&#39;s Friday. Friday <i>afternoon</i>, even!</div><div><br><br></div><div>Have a <i style=\"font-weight:bold\">great</i> weekend!</div><div>The Anonymous Friday Teller</div></div>\n",
        "SpamAssassinScore" => "0.602",
        "CustomID" => "helloworld",
        "Payload" => "{'message': 'helloworld'}"
    ];

    public function testHookHandlerAction()
    {
        $client = static::createClient();

        // Access the kernel container (booted from createClient)
        $container = static::$kernel->getContainer();

        // Subscribe a test listener
        $dispatcher = $container->get('event_dispatcher');
        $listener = new TestInboundEmailListener();
        $dispatcher->addListener(InboundEmailEvent::class, [$listener, 'onInboundEmail']);

        $crawler = $client->request('POST', '/inbound_email/mailjet/hook_handler', self::MAILJET_TEST_PAYLOAD);

        $this->assertResponseIsSuccessful();
        $this->assertTrue($listener->onInboundEmailInvoked);

        $event = $listener->event;
        $this->assertEquals('pilot@mailjet.com', $event->getFrom());
    }
}
