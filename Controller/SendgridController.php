<?php

namespace neyric\InboundEmailBundle\Controller;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SendgridController extends AbstractWebhookController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function hookHandlerAction(Request $request)
    {
        $qp = $request->request->all();
        $this->logger->info("Sendgrid HookEvent", $qp);

        // Parse Sendgrid 'envelope' field (from & to)
        $envelope = json_decode($qp['envelope'], true);

        // Subject
        $subject = $qp['subject'];
        $to = $envelope['to'][0];
        $from = $envelope['from'];
        $html = $qp['html'];
        $text = $qp['text'];

        $event = new InboundEmailEvent($from, $to, $subject, $text, $html);
        $this->dispatch($event);

        return JsonResponse::create([ 'success' => true ]);
    }
}
