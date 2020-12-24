<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit;

use Chemaclass\StockTicker\StockTickerConfigInterface;

final class FakeStockTickerConfig implements StockTickerConfigInterface
{
    public function getTemplatesDir(): string
    {
        return '';
    }

    public function getToAddress(): string
    {
        return '';
    }

    public function getMailerUsername(): string
    {
        return '';
    }

    public function getMailerPassword(): string
    {
        return '';
    }

    public function getSlackDestinyChannelId(): string
    {
        return '';
    }

    public function getSlackBotUserOauthAccessToken(): string
    {
        return '';
    }
}
