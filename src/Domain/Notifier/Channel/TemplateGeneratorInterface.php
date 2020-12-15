<?php

declare(strict_types=1);

namespace Chemaclass\StockTicker\Domain\Notifier\Channel;

use Chemaclass\StockTicker\Domain\Notifier\NotifyResult;

interface TemplateGeneratorInterface
{
    public function generateHtml(NotifyResult $notifyResult): string;
}
