<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Channel\Slack;

use Chemaclass\StockTicker\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;

final class SlackChannel implements ChannelInterface
{
    private string $slackDestinyChannelId;

    private SlackClientInterface $slackClient;

    private TemplateGeneratorInterface $templateGenerator;

    public function __construct(
        string $slackDestinyChannelId,
        SlackClientInterface $slackClient,
        TemplateGeneratorInterface $templateGenerator
    ) {
        $this->slackClient = $slackClient;
        $this->slackDestinyChannelId = $slackDestinyChannelId;
        $this->templateGenerator = $templateGenerator;
    }

    public function send(NotifyResult $notifyResult): void
    {
        $this->slackClient->postToChannel(
            $this->slackDestinyChannelId,
            $this->templateGenerator->generateHtml($notifyResult)
        );
    }
}
