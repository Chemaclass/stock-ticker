<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel\Slack;

use Chemaclass\FinanceYahoo\Domain\Notifier\Channel\TemplateGeneratorInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\ChannelInterface;
use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;

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
