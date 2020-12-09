<?php

declare(strict_types=1);

namespace App\Company\HtmlCrawler\Summary;

use App\Company\HtmlCrawler\CrawlerInterface;
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
