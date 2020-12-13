<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier;

use Chemaclass\TickerNews\Domain\Crawler\CrawlResult;
use Chemaclass\TickerNews\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\TickerNews\Domain\ReadModel\Company;

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
            $company = $crawlResult->getCompany($symbol);
            $conditionNames = $this->matchConditions($policyGroup, $company);

            if (!empty($conditionNames)) {
                $result->add($company, $conditionNames);
            }
        }

        if (!$result->isEmpty()) {
            $this->sendNotification($result);
        }

        return $result;
    }

    private function matchConditions(PolicyGroup $policyGroup, Company $company): array
    {
        $conditionNames = [];

        foreach ($policyGroup->conditions() as $conditionName => $callablePolicy) {
            if (!$callablePolicy($company)) {
                continue;
            }

            if (is_int($conditionName)) {
                $conditionName = get_class($callablePolicy);
            }

            $conditionNames[] = $conditionName;
        }

        return $conditionNames;
    }

    private function sendNotification(NotifyResult $result): void
    {
        foreach ($this->channels as $channel) {
            $channel->send($result);
        }
    }
}
