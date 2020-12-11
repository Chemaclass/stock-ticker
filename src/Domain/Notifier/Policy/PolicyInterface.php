<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Policy;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

interface PolicyInterface
{
    public function __invoke(Company $company): bool;
}