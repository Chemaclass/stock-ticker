<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier\Policy;

use Chemaclass\TickerNews\Domain\ReadModel\Company;

interface PolicyConditionInterface
{
    public function __invoke(Company $company): bool;
}
