<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\NotifierPolicy;
use Chemaclass\FinanceYahoo\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\FinanceYahoo\Domain\ReadModel\Company;

final class Notifier
{
    private NotifierPolicy $policy;

    /** @var ChannelInterface[] */
    private array $channels;

    public function __construct(
        NotifierPolicy $policy,
        ChannelInterface ...$channels
    ) {
        $this->policy = $policy;
        $this->channels = $channels;
    }

    /**
     * @param $companies array<string,Company>
     */
    public function notify(array $companies): NotifyResult
    {
        $result = new NotifyResult();

        foreach ($this->policy->groupedBySymbol() as $symbol => $policyGroup) {
            if (!isset($companies[$symbol])) {
                continue;
            }

            $company = $companies[$symbol];
            $policyName = $this->matchPolicy($policyGroup, $company);

            if (!empty($policyName)) {
                $this->sendNotification($company, $policyName);
                $result->add($company, $policyName);
            }
        }

        return $result;
    }

    private function sendNotification(Company $company, string $policyName): void
    {
        foreach ($this->channels as $channel) {
            $channel->send($company, $policyName);
        }
    }

    /**
     * If any of the policies are true, then it can notify.
     */
    private function matchPolicy(PolicyGroup $policyGroup, Company $company): string
    {
        foreach ($policyGroup->policies() as $policyName => $callablePolicy) {
            if ($callablePolicy($company)) {
                return $policyName;
            }
        }

        return '';
    }
}
