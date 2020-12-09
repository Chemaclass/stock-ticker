<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler\HtmlCrawler\Summary;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\Summary\CompanyFullNameCrawler;
use PHPUnit\Framework\TestCase;

final class CompanyFullNameCrawlerTest extends TestCase
{
    /** @test */
    public function crawlHtml(): void
    {
        $html = <<<HTML
<meta 
    name="description" 
    content="Find the latest The Name, Inc stock quote. And some more text"
/>
HTML;
        $crawler = new CompanyFullNameCrawler();
        $actual = $crawler->crawlHtml($html);

        self::assertEquals('The Name, Inc', $actual);
    }
}
