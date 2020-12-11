<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel;

use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class EmailChannel implements ChannelInterface
{
    private string $toAddress;

    private MailerInterface $mailer;

    public function __construct(
        string $toAddress,
        MailerInterface $mailer
    ) {
        $this->toAddress = $toAddress;
        $this->mailer = $mailer;
    }

    public function send(Company $company, string $policyName): void
    {
        $email = (new Email())
            ->to($this->toAddress)
            ->from('finance.yahoo.api@noreply.com')
            ->subject("FY: $company => $policyName")
            ->html("<h2>{$company} reached the threshold settle-up as {$policyName}</h2>");

        $this->mailer->send($email);
    }
}
