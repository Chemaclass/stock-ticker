<?php

declare(strict_types=1);

namespace App\Company\Crawler;

use Symfony\Component\DomCrawler\Crawler;

final class CompanyFullNameCrawler implements CrawlerInterface
{
    public const NAME = 'CompanyFullName';

    public function crawlHtml(string $html): string
    {
        $text = (new Crawler($html))
                ->filter('meta[name="description"]')
                ->attr('content') ?? '';

        preg_match('/^Find the latest (?<name>.*) stock quote/', $text, $matches);

        return $matches['name'] ?? 'No name found?';
    }
}
