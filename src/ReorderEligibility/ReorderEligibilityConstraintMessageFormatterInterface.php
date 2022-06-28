<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

interface ReorderEligibilityConstraintMessageFormatterInterface
{
    /** @var string[] $messageParameters */
    public function format(array $messageParameters): string;
}
