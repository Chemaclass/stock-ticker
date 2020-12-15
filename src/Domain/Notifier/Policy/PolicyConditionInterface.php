<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Policy;

use Chemaclass\StockTicker\Domain\ReadModel\Company;

interface PolicyConditionInterface
{
    public function __invoke(Company $company): bool;
}
