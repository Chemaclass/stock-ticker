<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel;

use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class EmailChannel implements ChannelInterface
{
    private const NOREPLY_EMAIL = 'finance.yahoo.api@noreply.com';

    private string $toAddress;

    private MailerInterface $mailer;

    public function __construct(
        string $toAddress,
        MailerInterface $mailer
    ) {
        $this->toAddress = $toAddress;
        $this->mailer = $mailer;
    }

    public function send(NotifyResult $notifyResult): void
    {
        $email = (new Email())
            ->to($this->toAddress)
            ->from(self::NOREPLY_EMAIL)
            ->subject($this->generateSubject($notifyResult))
            ->html($this->generateHtml($notifyResult));

        $this->mailer->send($email);
    }

    private function generateSubject(NotifyResult $notifyResult): string
    {
        $symbols = implode(', ', array_values($notifyResult->symbols()));

        return "FinanceYahoo alert for {$symbols}";
    }

    private function generateHtml(NotifyResult $notifyResult): string
    {
        $text = '<h1>Policy threshold reached for these companies</h1>';

        foreach ($notifyResult->symbols() as $symbol) {
            $companyName = (string) $notifyResult->companyForSymbol($symbol)->info('name');
            $text .= "<h2>{$companyName} <small>$symbol</small></h2>";
            $text .= '<ul>';
            $text .= implode(array_map(
                static fn (string $s): string => "<li>{$s}</li>",
                $notifyResult->policiesForSymbol($symbol)
            ));
            $text .= '</ul>';
        }

        return $text;
    }
}
