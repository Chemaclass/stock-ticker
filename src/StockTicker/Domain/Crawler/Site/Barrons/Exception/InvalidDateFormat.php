<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Crawler\Site\Barrons\Exception;

use RuntimeException;

final class InvalidDateFormat extends RuntimeException
{
    public static function forIncomingDate(string $incomingDate): self
    {
        return new self(sprintf(
            'Format not found for the incomingDate: "%s"',
            $incomingDate,
        ));
    }

    public static function couldNotCreateDateTime(string $incomingDate, string $incomingFormat): self
    {
        return new self(sprintf(
            'Could not create a dateTime for incomingDate: "%s" to this format: "%s"',
            $incomingDate,
            $incomingFormat,
        ));
    }
}
