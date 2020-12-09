<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahooTests\Unit\Crawler\HtmlCrawler\Summary;

use Chemaclass\FinanceYahoo\Crawler\HtmlCrawler\Summary\EstimateReturnCrawler;
use PHPUnit\Framework\TestCase;

final class EstimateReturnCrawlerTest extends TestCase
{
    /** @test */
    public function crawlHtml(): void
    {
        $html = <<<HTML
<div id="fr-val-mod">
    <div class="IbBox">XX.XX</div>
    <div class="IbBox">-19% Est. Return</div>
</div>
HTML;
        $crawler = new EstimateReturnCrawler();
        $actual = $crawler->crawlHtml($html);

        self::assertEquals('-19% Est. Return', $actual);
    }
}
