<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Gacela\Framework\AbstractConfig;

final class StockTickerConfig extends AbstractConfig
{
    public function getTemplatesDir(): string
    {
        return dirname(__DIR__) . '/notification';
    }

    public function getToAddress(): string
    {
        return $this->get('TO_ADDRESS');
    }

    public function getMailerUsername(): string
    {
        return $this->get('MAILER_USERNAME');
    }

    public function getMailerPassword(): string
    {
        return $this->get('MAILER_PASSWORD');
    }

    public function getSlackDestinyChannelId(): string
    {
        return $this->get('SLACK_DESTINY_CHANNEL_ID');
    }

    public function getSlackBotUserOauthAccessToken(): string
    {
        return $this->get('SLACK_BOT_USER_OAUTH_ACCESS_TOKEN');
    }

    public function isDebug(): bool
    {
        return $this->isTrue($this->get('DEBUG'));
    }

    private function isTrue(?string $bool): bool
    {
        return in_array($bool, ['true', '1', 'yes'], true);
    }
}
