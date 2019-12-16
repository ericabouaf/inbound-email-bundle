<?php

namespace neyric\InboundEmailBundle\Controller;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Transform a Mailjet webhook call into an InboundEmailEvent
 * Reference: https://dev.mailjet.com/email/guides/parse-api/
 */
class MailjetController extends AbstractWebhookController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function hookHandlerAction(Request $request)
    {
        $qp = $request->request->all();
        $this->logger->info("Mailjet HookEvent", $qp);

        $subject = $qp['Subject'];
        $to = $qp['Recipient'];
        $from = $qp['Sender'];
        $html = $qp['Html-part'];
        $text = $qp['Text-part'];

        $event = new InboundEmailEvent($from, $to, $subject, $text, $html);
        $this->dispatch($event);

        return JsonResponse::create([ 'success' => true ]);
    }
}
