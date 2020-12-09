<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\Summary;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\CrawlerInterface;
use Symfony\Component\DomCrawler\Crawler;

final class CompanyFullNameCrawler implements CrawlerInterface
{
    public function crawlHtml(string $html): string
    {
        $text = (new Crawler($html))
                ->filter('meta[name="description"]')
                ->attr('content') ?? '';

        preg_match('/^Find the latest (?<name>.*) stock quote/', $text, $matches);

        return $matches['name'] ?? 'No name found?';
    }
}
