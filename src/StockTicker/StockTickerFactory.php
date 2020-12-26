<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapper;
use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapperInterface;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\HtmlCrawler\News;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\StockTicker\Domain\Crawler\Site\Shared\NewsNormalizer;
use Chemaclass\StockTicker\Domain\Crawler\SiteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Email\EmailChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\HttpSlackClient;
use Chemaclass\StockTicker\Domain\Notifier\Channel\Slack\SlackChannel;
use Chemaclass\StockTicker\Domain\Notifier\Channel\TwigTemplateGenerator;
use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\Notifier;
use Chemaclass\StockTicker\Domain\Notifier\NotifierInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifierPolicy;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;
use DateTimeZone;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class StockTickerFactory implements StockTickerFactoryInterface
{
    private const NAME = 'NAME';
    private const PRICE = 'PRICE';
    private const CURRENCY = 'CURRENCY';
    private const CHANGE = 'CHANGE';
    private const CHANGE_PERCENT = 'CHANGE_PERCENT';
    private const MARKET_CAP = 'MARKET_CAP';
    private const TREND = 'TREND';
    private const NEWS = 'NEWS';
    private const URL = 'URL';

    private StockTickerConfigInterface $config;

    public function __construct(StockTickerConfigInterface $config)
    {
        $this->config = $config;
    }

    public function createCompanyCrawler(SiteCrawlerInterface ...$siteCrawlers): QuoteCrawlerInterface
    {
        return new QuoteCrawler(
            HttpClient::create(),
            $this->createCrawledInfoMapper(),
            ...$siteCrawlers
        );
    }

    public function createNotifier(NotifierPolicy $policy, ChannelInterface ...$channels): NotifierInterface
    {
        return new Notifier($policy, ...$channels);
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

    private function createCrawledInfoMapper(): CrawledInfoMapperInterface
    {
        return new CrawledInfoMapper(function (array $info): array {
            $info[Quote::COMPANY_NAME] = $info[self::NAME];
            $info[Quote::REGULAR_MARKET_PRICE] = $info[self::PRICE];
            $info[Quote::CURRENCY] = $info[self::CURRENCY];
            $info[Quote::REGULAR_MARKET_CHANGE] = $info[self::CHANGE];
            $info[Quote::REGULAR_MARKET_CHANGE_PERCENT] = $info[self::CHANGE_PERCENT];
            $info[Quote::LAST_TREND] = $info[self::TREND];
            $info[Quote::MARKET_CAP] = $info[self::MARKET_CAP];
            $info[Quote::LATEST_NEWS] = $info[self::NEWS];
            $info[Quote::URL] = $info[self::URL][0];

            return $info;
        });
    }

    private function createFinanceYahooSiteCrawler(int $maxNewsToFetch): FinanceYahooSiteCrawler
    {
        return new FinanceYahooSiteCrawler([
            self::NAME => new JsonExtractor\QuoteSummaryStore\CompanyName(),
            self::PRICE => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
            self::CURRENCY => new JsonExtractor\QuoteSummaryStore\Currency(),
            self::CHANGE => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
            self::CHANGE_PERCENT => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
            self::MARKET_CAP => new JsonExtractor\QuoteSummaryStore\MarketCap(),
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
