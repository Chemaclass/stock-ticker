<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

interface StockTickerConfigInterface
{
    public function getTemplatesDir(): string;

    public function getToAddress(): string;

    public function getMailerUsername(): string;

    public function getMailerPassword(): string;

    public function getSlackDestinyChannelId(): string;

    public function getSlackBotUserOauthAccessToken(): string;

    public function isDebug(): bool;
}
