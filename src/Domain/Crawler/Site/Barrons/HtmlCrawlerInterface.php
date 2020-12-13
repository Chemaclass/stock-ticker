<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Crawler\Site\Barrons;

interface HtmlCrawlerInterface
{
    /**
     * @return mixed
     */
    public function crawlHtml(string $html);
}