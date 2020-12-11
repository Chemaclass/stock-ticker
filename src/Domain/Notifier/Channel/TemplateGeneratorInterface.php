<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo\Domain\Notifier\Channel;

use Chemaclass\FinanceYahoo\Domain\Notifier\NotifyResult;

interface TemplateGeneratorInterface
{
    public function generateHtml(NotifyResult $notifyResult): string;
}
