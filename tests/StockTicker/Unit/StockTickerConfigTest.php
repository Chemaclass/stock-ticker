<?php

declare(strict_types=1);

namespace Chemaclass\StockTickerTests\Unit;

use Chemaclass\StockTicker\StockTickerConfig;
use Generator;
use PHPUnit\Framework\TestCase;

final class StockTickerConfigTest extends TestCase
{
    private const EXAMPLE_TEMPLATE_DIR = 'example template dir';

    public function testTemplateDir(): void
    {
        $config = new StockTickerConfig(self::EXAMPLE_TEMPLATE_DIR, []);

        self::assertSame(self::EXAMPLE_TEMPLATE_DIR, $config->getTemplatesDir());
    }

    public function testEmailChannelConfig(): void
    {
        $env = [
            'TO_ADDRESS' => 'TO_ADDRESS',
            'MAILER_USERNAME' => 'MAILER_USERNAME',
            'MAILER_PASSWORD' => 'MAILER_PASSWORD',
        ];

        $config = new StockTickerConfig(self::EXAMPLE_TEMPLATE_DIR, $env);

        self::assertSame($env['TO_ADDRESS'], $config->getToAddress());
        self::assertSame($env['MAILER_USERNAME'], $config->getMailerUsername());
        self::assertSame($env['MAILER_PASSWORD'], $config->getMailerPassword());
    }

    public function testSlackChannelConfig(): void
    {
        $env = [
            'SLACK_DESTINY_CHANNEL_ID' => 'SLACK_DESTINY_CHANNEL_ID',
            'SLACK_BOT_USER_OAUTH_ACCESS_TOKEN' => 'SLACK_BOT_USER_OAUTH_ACCESS_TOKEN',
        ];

        $config = new StockTickerConfig(self::EXAMPLE_TEMPLATE_DIR, $env);

        self::assertSame($env['SLACK_DESTINY_CHANNEL_ID'], $config->getSlackDestinyChannelId());
        self::assertSame($env['SLACK_BOT_USER_OAUTH_ACCESS_TOKEN'], $config->getSlackBotUserOauthAccessToken());
    }

    /**
     * @dataProvider providerDebugConfig
     */
    public function testDebugConfig(string $debugParam, bool $expected): void
    {
        $config = new StockTickerConfig(self::EXAMPLE_TEMPLATE_DIR, [
            'DEBUG' => $debugParam,
        ]);

        self::assertEquals($expected, $config->isDebug());
    }

    public function providerDebugConfig(): Generator
    {
        yield [
            'debugParam' => 'true',
            'expected' => true,
        ];

        yield [
            'debugParam' => '1',
            'expected' => true,
        ];

        yield [
            'debugParam' => 'yes',
            'expected' => true,
        ];

        yield [
            'debugParam' => 'false',
            'expected' => false,
        ];

        yield [
            'debugParam' => '0',
            'expected' => false,
        ];

        yield [
            'debugParam' => 'no',
            'expected' => false,
        ];
    }
}
