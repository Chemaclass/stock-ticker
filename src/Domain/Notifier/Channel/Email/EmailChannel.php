<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel\Email;

use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class EmailChannel implements ChannelInterface
{
    private const NO_REPLY_EMAIL = 'finance.yahoo.api@noreply.com';

    private string $toAddress;

    private MailerInterface $mailer;

    private TemplateGeneratorInterface $templateGenerator;

    public function __construct(
        string $toAddress,
        MailerInterface $mailer,
        TemplateGeneratorInterface $templateGenerator
    ) {
        $this->toAddress = $toAddress;
        $this->mailer = $mailer;
        $this->templateGenerator = $templateGenerator;
    }

    public function send(NotifyResult $notifyResult): void
    {
        $email = (new Email())
            ->to($this->toAddress)
            ->from(self::NO_REPLY_EMAIL)
            ->subject($this->generateSubject($notifyResult))
            ->html($this->templateGenerator->generateHtml($notifyResult));

        $this->mailer->send($email);
    }

    private function generateSubject(NotifyResult $notifyResult): string
    {
        $symbols = implode(', ', array_values($notifyResult->symbols()));

        return "FinanceYahoo NEWS for {$symbols}";
    }
}
