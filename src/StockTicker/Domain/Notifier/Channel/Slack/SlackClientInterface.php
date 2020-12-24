<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Channel\Slack;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface SlackClientInterface
{
    public function postToChannel(string $channel, string $text): ResponseInterface;
}
