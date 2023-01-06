<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit\Domain\Notifier\Channel\Slack;

use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\HttpSlackClient;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HttpSlackClientTest extends TestCase
{
    public function test_post_to_channel(): void
    {
        $client = new HttpSlackClient(
            $this->mockHttpClient(self::once()),
        );

        $client->postToChannel('channel_id', 'text');
    }

    private function mockHttpClient(InvokedCount $invokedCount): HttpClientInterface
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient
            ->expects($invokedCount)
            ->method('request');

        return $httpClient;
    }
}
