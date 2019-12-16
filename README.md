InboundEmailBundle for Symfony [![Build Status](https://travis-ci.org/neyric/inbound-email-bundle.svg?branch=master)](https://travis-ci.org/neyric/inbound-email-bundle) [![Latest Stable Version](https://poser.pugx.org/neyric/inbound-email-bundle/v/stable)](https://packagist.org/packages/neyric/inbound-email-bundle) [![Total Downloads](https://poser.pugx.org/neyric/inbound-email-bundle/downloads)](https://packagist.org/packages/neyric/inbound-email-bundle) [![License](https://poser.pugx.org/neyric/inbound-email-bundle/license)](https://packagist.org/packages/neyric/inbound-email-bundle)
=================================================

Inbound emails for Symfony apps with Sendgrid and Mailjet support.

Principle
-------------------------------------------------

This bundle provides a standardized `InboundEmailEvent` to your application.

Additionnaly, emails replies are parsed using [willdurand/email-reply-parser](https://github.com/willdurand/EmailReplyParser),
which will strip the text content from quoted texts and signature.

The `InboundEmailEvent` is dispatched by configurable webhook-handler controllers, and currently supports two email gateways :

* Sendgrid (cf. [Inbound Email Parse Webhook documentation](https://sendgrid.com/docs/for-developers/parsing-email/inbound-email/) )
* Mailjet (cf. [Parse API documentation](https://dev.mailjet.com/email/guides/parse-api/))


Requirements
-------------------------------------------------
To use this bundle, you will need (as a minimum):
* PHP v7.1
* Symfony >= 4.3
* A Sendgrid or Mailjet account


Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require neyric/inbound-email-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require neyric/inbound-email-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new \neyric\InboundEmailBundle\NeyricInboundEmailBundle(),
        ];

        // ...
    }

    // ...
}
```


Configuration for Sendgrid
----------------------------------------

First, you'll need to setup the webhook handler.
In your routes.yaml file :

```yaml
neyric_inbound_email_sendgrid:
    path: /inbound_email/sendgrid/hook_handler # You can customize
    controller: neyric\InboundEmailBundle\Controller\SendgridController::hookHandlerAction
```

If you use symfony/expression-language component, it is recommended to add the following line to limit this route to the POST method :
```yaml
    condition:  "context.getMethod() in ['POST']"
```

Then, setup the Sendgrid Inbound Parse Webhook: Cf. https://sendgrid.com/docs/for-developers/parsing-email/setting-up-the-inbound-parse-webhook/


Configuration for Mailjet
----------------------------------------

First, you'll need to setup the webhook handler.
In your routes.yaml file :

```yaml
neyric_inbound_email_mailjet:
    path: /inbound_email/mailjet/hook_handler
    controller: neyric\InboundEmailBundle\Controller\MailjetController::hookHandlerAction
```

If you use symfony/expression-language component, it is recommended to add the following line to limit this route to the POST method :
```yaml
    condition:  "context.getMethod() in ['POST']"
```

Then, setup the Mailjet Parse API Webhook: Cf. https://dev.mailjet.com/email/guides/parse-api/


Application Usage
----------------------------------------

To receive the emails within your application, simply create an EventSubscriber class, which listens for the InboundEmailEvent::class event :

```php

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MyInboundEmailSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            InboundEmailEvent::class => ['onInboundEmail', 10], // 10 = priority
        ];
    }

    public function onInboundEmail(InboundEmailEvent $event)
    {
        // ... do something with $event
        // Check https://github.com/neyric/inbound-email-bundle/blob/master/Event/InboundEmailEvent.php for reference

        // If this subscriber can handle this event, it is recommended to stop the propagation
        // This will prevent other subscribers with lower priorities to be executed,
        // allowing event-based routing of your incoming emails.
        $event->stopPropagation();
    }
}
```

Then register the service with the `kernel.event_subscriber` (if necessary, depending on your service configuration)

```yaml
    App\Event\MyInboundEmailSubscriber:
        class: App\Event\MyInboundEmailSubscriber
        tags: ['kernel.event_subscriber']
```



Prepare a local tunnel
----------------------------------------

Using a local tunnel will save you a lot of time because you can test locally. The recommended choice is [ngrok](https://ngrok.com/). Ngrok is a tool to tunnel our local server to the web, making our local webhook handlers available to the email providers webhooks.


License
-------------------------------------------------
neyric/inbound-email-bundle is distributed under MIT license, see the [LICENSE file](https://github.com/neyric/inbound-email-bundle/blob/master/LICENSE).


Contacts
-------------------------------------------------
Report bugs or suggest features using [issue tracker on GitHub](https://github.com/neyric/inbound-email-bundle).
