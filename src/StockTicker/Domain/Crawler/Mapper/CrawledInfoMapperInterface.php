<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Mapper;

use Chemaclass\StockTicker\Domain\WriteModel\Quote;

interface CrawledInfoMapperInterface
{
    public function mapQuote(array $info): Quote;
}
