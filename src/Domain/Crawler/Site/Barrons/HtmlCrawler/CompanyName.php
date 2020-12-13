<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawler;

use Chemaclass\TickerNews\Domain\Crawler\Site\Barrons\HtmlCrawlerInterface;

final class CompanyName implements HtmlCrawlerInterface
{
    public function crawlHtml(string $html): array
    {
        return []; // TODO
    }
}
