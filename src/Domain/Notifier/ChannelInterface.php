<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

interface ChannelInterface
{
    public function send(Company $company, string $policyName): void;
}
