<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Channel\Email;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

final class EmailChannelTest extends TestCase
{
    private const EXAMPLE_EMAIL = 'example.email@noreply.com';

    public function test_send(): void
    {
        $channel = new EmailChannel(
            self::EXAMPLE_EMAIL,
            $this->mockMailer(self::once()),
            $this->mockTemplateGenerator(self::once()),
        );

        $notifyResult = (new NotifyResult())
            ->add($this->createCompany('1'), ['condition name 1'])
            ->add($this->createCompany('2'), ['condition name 1']);

        $channel->send($notifyResult);
    }

    private function mockMailer(InvokedCount $invokedCount): MailerInterface
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer
            ->expects($invokedCount)
            ->method('send');

        return $mailer;
    }

    private function mockTemplateGenerator(InvokedCount $invokedCount): TemplateGeneratorInterface
    {
        $templateGenerator = $this->createMock(TemplateGeneratorInterface::class);
        $templateGenerator
            ->expects($invokedCount)
            ->method('generateHtml');

        return $templateGenerator;
    }

    private function createCompany(string $symbol): Quote
    {
        return (new Quote())
            ->setSymbol($symbol);
    }
}
