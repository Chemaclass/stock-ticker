<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Mapper;

use Chemaclass\StockTicker\Domain\WriteModel\Quote;

final class CrawledInfoMapper implements CrawledInfoMapperInterface
{
    /** @var null|callable */
    private $mapper;

    public function __construct(?callable $mapper = null)
    {
        $this->mapper = $mapper;
    }

    public function mapQuote(array $info): Quote
    {
        if ($this->mapper) {
            $info = ($this->mapper)($info);
        }

        return (new Quote())->fromArray($info);
    }
}
