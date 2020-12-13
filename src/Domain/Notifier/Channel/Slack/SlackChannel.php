<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier\Channel\Slack;

use Chemaclass\TickerNews\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\TickerNews\Domain\Notifier\ChannelInterface;
use Chemaclass\TickerNews\Domain\Notifier\NotifyResult;

final class SlackChannel implements ChannelInterface
{
    private SlackHttpClient $slackClient;

    private string $slackDestinyChannelId;

    private TemplateGeneratorInterface $templateGenerator;

    public function __construct(
        string $slackDestinyChannelId,
        SlackHttpClient $slackClient,
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
