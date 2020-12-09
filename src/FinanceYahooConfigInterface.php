<?php

declare(strict_types=1);

namespace Chemaclass\FinanceYahoo;

use Chemaclass\FinanceYahoo\Crawler\ReadModel\Ticker;

interface FinanceYahooConfigInterface
{
    /**
     * @return Ticker[]
     */
    public function getTickers(): array;
}
