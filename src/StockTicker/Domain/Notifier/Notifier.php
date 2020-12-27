<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier;

use Chemaclass\StockTicker\Domain\Crawler\CrawlResult;
use Chemaclass\StockTicker\Domain\Notifier\Policy\PolicyGroup;
use Chemaclass\StockTicker\Domain\WriteModel\Quote;

final class Notifier implements NotifierInterface
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
            $quote = $crawlResult->getQuote($symbol);
            $conditionNames = $this->matchConditions($policyGroup, $quote);

            if (!empty($conditionNames)) {
                $result->add($quote, $conditionNames);
            }
        }

        if (!$result->isEmpty()) {
            $this->sendNotification($result);
        }

        return $result;
    }

    private function matchConditions(PolicyGroup $policyGroup, Quote $quote): array
    {
        $conditionNames = [];

        foreach ($policyGroup->conditions() as $conditionName => $callablePolicy) {
            if (!$callablePolicy($quote)) {
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
