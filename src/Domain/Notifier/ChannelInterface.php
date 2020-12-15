<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier;

interface ChannelInterface
{
    public function send(NotifyResult $notifyResult): void;
}
