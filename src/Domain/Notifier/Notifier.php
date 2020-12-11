<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

use Chemaclass\FinanceYahoo\Domain\Crawler\CrawlResult;
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

    public function notify(CrawlResult $crawlResult): NotifyResult
    {
        $result = new NotifyResult();

        foreach ($this->policy->groupedBySymbol() as $symbol => $policyGroup) {
            $company = $crawlResult->get($symbol);
            $policyNames = $this->matchPolicy($policyGroup, $company);

            if (!empty($policyNames)) {
                $result->add($company, $policyNames);
            }
        }

        $this->sendNotification($result);

        return $result;
    }

    /**
     * If any of the policies are true, then it can notify.
     */
    private function matchPolicy(PolicyGroup $policyGroup, Company $company): array
    {
        $policyNames = [];

        foreach ($policyGroup->policies() as $policyName => $callablePolicy) {
            if ($callablePolicy($company)) {
                $policyNames[] = $policyName;
            }
        }

        return $policyNames;
    }

    private function sendNotification(NotifyResult $result): void
    {
        foreach ($this->channels as $channel) {
            $channel->send($result);
        }
    }
}
