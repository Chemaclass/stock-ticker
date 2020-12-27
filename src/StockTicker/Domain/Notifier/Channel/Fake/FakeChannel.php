<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Channel\Fake;

use Chemaclass\StockTicker\Domain\Notifier\ChannelInterface;
use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;

final class FakeChannel implements ChannelInterface
{
    public function send(NotifyResult $notifyResult): void
    {
        // Intentionally blank :)
    }
}
