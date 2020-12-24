<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawler\News;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizer;
use Chemaclass\StockTicker\Domain\NewsNotifier;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\HttpSlackClient;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use DateTimeZone;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class StockTickerFactory implements StockTickerFactoryInterface
{
    public const NAME = 'NAME';
    public const PRICE = 'PRICE';
    public const CURRENCY = 'CURRENCY';
    public const CHANGE = 'CHANGE';
    public const CHANGE_PERCENT = 'CHANGE_PERCENT';
    public const TREND = 'TREND';
    public const NEWS = 'NEWS';
    public const URL = 'URL';

    private StockTickerConfigInterface $config;

    public function __construct(StockTickerConfigInterface $config)
    {
        $this->config = $config;
    }

    public function createNewsNotifier(ChannelInterface ...$channels): NewsNotifier
    {
        return new NewsNotifier(
            HttpClient::create(),
            ...$channels
        );
    }

    /**
     * @return Domain\Crawler\SiteCrawlerInterface[]
     */
    public function createSiteCrawlers(int $maxNewsToFetch): array
    {
        return [
            $this->createFinanceYahooSiteCrawler($maxNewsToFetch),
            $this->createBarronsSiteCrawler($maxNewsToFetch),
        ];
    }

    /**
     * @return ChannelInterface[]
     */
    public function createChannels(array $channelNames): array
    {
        $flipped = array_flip($channelNames);
        $channels = [];

        if (isset($flipped[EmailChannel::class])) {
            $channels[] = $this->createEmailChannel();
        }

        if (isset($flipped[SlackChannel::class])) {
            $channels[] = $this->createSlackChannel();
        }

        return $channels;
    }

    private function createFinanceYahooSiteCrawler(int $maxNewsToFetch): FinanceYahooSiteCrawler
    {
        return new FinanceYahooSiteCrawler([
            self::NAME => new JsonExtractor\QuoteSummaryStore\CompanyName(),
            self::PRICE => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
            self::CURRENCY => new JsonExtractor\QuoteSummaryStore\Currency(),
            self::CHANGE => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
            self::CHANGE_PERCENT => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
            self::TREND => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
            self::NEWS => new JsonExtractor\StreamStore\News($this->createNewsNormalizer($maxNewsToFetch)),
            self::URL => new JsonExtractor\RouteStore\ExternalUrl(),
        ]);
    }

    private function createNewsNormalizer(int $maxNewsToFetch): NewsNormalizer
    {
        return new NewsNormalizer(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch);
    }

    private function createBarronsSiteCrawler(int $maxNewsToFetch): BarronsSiteCrawler
    {
        return new BarronsSiteCrawler([
            self::NEWS => new News($this->createNewsNormalizer($maxNewsToFetch)),
        ]);
    }

    private function createEmailChannel(string $templateName = 'email.twig'): EmailChannel
    {
        return new EmailChannel(
            $this->config->getToAddress(),
            new Mailer(new GmailSmtpTransport(
                $this->config->getMailerUsername(),
                $this->config->getMailerPassword()
            )),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader($this->config->getTemplatesDir())),
                $templateName
            )
        );
    }

    private function createSlackChannel(string $templateName = 'slack.twig'): SlackChannel
    {
        return new SlackChannel(
            $this->config->getSlackDestinyChannelId(),
            new HttpSlackClient(HttpClient::create([
                'auth_bearer' => $this->config->getSlackBotUserOauthAccessToken(),
            ])),
            new TwigTemplateGenerator(
                new Environment(new FilesystemLoader($this->config->getTemplatesDir())),
                $templateName
            )
        );
    }
}
