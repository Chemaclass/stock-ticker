<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier;

interface ChannelInterface
{
    public function send(NotifyResult $notifyResult): void;
}
