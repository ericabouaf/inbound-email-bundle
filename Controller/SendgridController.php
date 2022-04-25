<?php

namespace neyric\InboundEmailBundle\Controller;

use neyric\InboundEmailBundle\Event\InboundEmailEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class SendgridController extends AbstractWebhookController
{
    const UTF8 = 'utf8';

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

        $attachments = $request->files->all();

        $text = null;
        if (array_key_exists('text', $qp)) {
            $text = $qp['text'];
        }

        $html = null;
        if (array_key_exists('html', $qp)) {
            $html = $qp['html'];
        }

        // charsets
        $charsets = json_decode($qp['charsets'], true);
        if ($charsets) {

            if ($charsets['to'] != self::UTF8) {
                $to = iconv($charsets['to'] , self::UTF8, $to);
            }

            if (!is_null($html) && $charsets['html'] != self::UTF8) {
                $html = iconv($charsets['html'] , self::UTF8, $html);
            }

            if ($charsets['subject'] != self::UTF8) {
                $subject = iconv($charsets['subject'] , self::UTF8, $subject);
            }

            if ($charsets['from'] != self::UTF8) {
                $from = iconv($charsets['from'] , self::UTF8, $from);
            }

            if (!is_null($text) && $charsets['text'] != self::UTF8) {
                $text = iconv($charsets['text'] , self::UTF8, $text);
            }

        }


        $event = new InboundEmailEvent($from, $to, $subject, $text, $html, $attachments);
        $this->dispatch($event);

        return JsonResponse::create([ 'success' => true ]);
    }
}
