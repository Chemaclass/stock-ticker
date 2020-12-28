<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

final class StockTickerConfig implements StockTickerConfigInterface
{
    private string $templatesDir;

    private array $env;

    public function __construct(string $templatesDir = '', array $env = [])
    {
        $this->templatesDir = $templatesDir;
        $this->env = $env;
    }

    public function getTemplatesDir(): string
    {
        return $this->templatesDir;
    }

    public function getToAddress(): string
    {
        return $this->env['TO_ADDRESS'];
    }

    public function getMailerUsername(): string
    {
        return $this->env['MAILER_USERNAME'];
    }

    public function getMailerPassword(): string
    {
        return $this->env['MAILER_PASSWORD'];
    }

    public function getSlackDestinyChannelId(): string
    {
        return $this->env['SLACK_DESTINY_CHANNEL_ID'];
    }

    public function getSlackBotUserOauthAccessToken(): string
    {
        return $this->env['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'];
    }

    public function isDebug(): bool
    {
        return $this->isTrue($this->env['DEBUG'] ?? null);
    }

    private function isTrue(?string $bool): bool
    {
        return in_array($bool, ['true', '1', 'yes'], true);
    }
}
