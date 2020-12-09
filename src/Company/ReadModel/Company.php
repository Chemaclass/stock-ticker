<?php

declare(strict_types=1);

namespace App\Company\ReadModel;

/** @psalm-immutable */
final class Company
{
    private Summary $summary;

    public function __construct(Summary $summary)
    {
        $this->summary = $summary;
    }
}
