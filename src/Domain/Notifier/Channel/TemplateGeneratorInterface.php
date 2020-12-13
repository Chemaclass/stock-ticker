<?php

declare(strict_types=1);

namespace Chemaclass\TickerNews\Domain\Notifier\Channel;

use Chemaclass\TickerNews\Domain\Notifier\NotifyResult;

interface TemplateGeneratorInterface
{
    public function generateHtml(NotifyResult $notifyResult): string;
}
