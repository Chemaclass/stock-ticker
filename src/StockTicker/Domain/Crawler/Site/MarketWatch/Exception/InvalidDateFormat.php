<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\MarketWatch\Exception;

use RuntimeException;

final class InvalidDateFormat extends RuntimeException
{
    public static function couldNotCreateFromTimestamp(int $timestamp): self
    {
        return new self(sprintf(
            'Could not create a dateTime for timestamp: "%s"',
            $timestamp,
        ));
    }
}
