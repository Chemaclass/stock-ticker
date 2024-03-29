<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Channel\Slack;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class HttpSlackClient implements SlackClientInterface
{
    public const SLACK_API_POST_MESSAGE = 'https://slack.com/api/chat.postMessage';

    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function postToChannel(string $channel, string $text): ResponseInterface
    {
        return $this->client->request(
            'POST',
            self::SLACK_API_POST_MESSAGE,
            [
                'json' => [
                    'channel' => $channel,
                    'text' => $text,
                    'as_user' => true,
                ],
            ],
        );
    }
}
