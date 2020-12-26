<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Channel\Slack;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackClientInterface;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;

final class SlackChannelTest extends TestCase
{
    private const EXAMPLE_SLACK_DESTINY_CHANNEL_ID = 'SLACK_DESTINY_CHANNEL_ID';

    public function testSend(): void
    {
        $channel = new SlackChannel(
            self::EXAMPLE_SLACK_DESTINY_CHANNEL_ID,
            $this->mockSlackClient(self::once()),
            $this->mockTemplateGenerator(self::once())
        );

        $notifyResult = (new NotifyResult())
            ->add($this->createCompany('1'), ['condition name 1'])
            ->add($this->createCompany('2'), ['condition name 1']);

        $channel->send($notifyResult);
    }

    private function mockSlackClient(InvokedCount $invokedCount): SlackClientInterface
    {
        $SlackClient = $this->createMock(SlackClientInterface::class);
        $SlackClient
            ->expects($invokedCount)
            ->method('postToChannel');

        return $SlackClient;
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
