<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker;

use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapper;
use Chemaclass\StockTicker\Domain\Crawler\Mapper\CrawledInfoMapperInterface;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\QuoteCrawlerInterface;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons;
use Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\BarronsSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\FinanceYahooSiteCrawler;
use Chemaclass\StockTicker\Domain\Crawler\Site\FinanceYahoo\JsonExtractor;
use Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch;
use Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch\MarketWatchSiteCrawler;
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
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final class StockTickerFactory implements StockTickerFactoryInterface
{
    private const URLS = 'URLS';

    private StockTickerConfigInterface $config;

    public function __construct(StockTickerConfigInterface $config)
    {
        $this->config = $config;
    }

    public function createQuoteCrawler(SiteCrawlerInterface ...$siteCrawlers): QuoteCrawlerInterface
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
            $this->createMarketWatchSiteCrawler($maxNewsToFetch),
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
            $info[Quote::URL] = $info[self::URLS][0];

            return $info;
        });
    }

    private function createFinanceYahooSiteCrawler(int $maxNewsToFetch): FinanceYahooSiteCrawler
    {
        return new FinanceYahooSiteCrawler([
            Quote::COMPANY_NAME => new JsonExtractor\QuoteSummaryStore\CompanyName(),
            Quote::REGULAR_MARKET_PRICE => new JsonExtractor\QuoteSummaryStore\RegularMarketPrice(),
            Quote::CURRENCY => new JsonExtractor\QuoteSummaryStore\Currency(),
            Quote::REGULAR_MARKET_CHANGE => new JsonExtractor\QuoteSummaryStore\RegularMarketChange(),
            Quote::REGULAR_MARKET_CHANGE_PERCENT => new JsonExtractor\QuoteSummaryStore\RegularMarketChangePercent(),
            Quote::MARKET_CAP => new JsonExtractor\QuoteSummaryStore\MarketCap(),
            Quote::LAST_TREND => new JsonExtractor\QuoteSummaryStore\RecommendationTrend(),
            Quote::LATEST_NEWS => new JsonExtractor\StreamStore\News($this->createNewsNormalizer($maxNewsToFetch)),
            self::URLS => new JsonExtractor\RouteStore\ExternalUrl(),
        ]);
    }

    private function createNewsNormalizer(int $maxNewsToFetch): NewsNormalizer
    {
        return new NewsNormalizer(new DateTimeZone('Europe/Berlin'), $maxNewsToFetch);
    }

    private function createBarronsSiteCrawler(int $maxNewsToFetch): BarronsSiteCrawler
    {
        return new BarronsSiteCrawler([
            Quote::LATEST_NEWS => new Barrons\HtmlCrawler\News($this->createNewsNormalizer($maxNewsToFetch)),
        ]);
    }

    private function createMarketWatchSiteCrawler(int $maxNewsToFetch): MarketWatchSiteCrawler
    {
        return new MarketWatchSiteCrawler([
            Quote::LATEST_NEWS => new MarketWatch\HtmlCrawler\News($this->createNewsNormalizer($maxNewsToFetch)),
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
                $this->createTwigEnvironment(),
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
                $this->createTwigEnvironment(),
                $templateName
            )
        );
    }

    private function createTwigEnvironment(): Environment
    {
        $loader = new FilesystemLoader($this->config->getTemplatesDir());
        $isDebug = $this->config->isDebug();
        $twig = new Environment($loader, ['debug' => $isDebug]);

        if ($isDebug) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }
}
