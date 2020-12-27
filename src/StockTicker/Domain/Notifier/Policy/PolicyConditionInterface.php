<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Policy;

use Chemaclass\StockTicker\Domain\WriteModel\Quote;

interface PolicyConditionInterface
{
    public function __invoke(Quote $quote): bool;
}
