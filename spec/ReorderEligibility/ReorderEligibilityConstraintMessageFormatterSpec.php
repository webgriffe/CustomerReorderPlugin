<?php

declare(strict_types=1);

namespace spec\Sylius\CustomerReorderPlugin\ReorderEligibility;

use PhpSpec\ObjectBehavior;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatter;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityConstraintMessageFormatterInterface;

final class ReorderEligibilityConstraintMessageFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ReorderEligibilityConstraintMessageFormatter::class);
    }

    public function it_implements_reorder_eligibility_constraint_message_formatter_interface(): void
    {
        $this->shouldImplement(ReorderEligibilityConstraintMessageFormatterInterface::class);
    }

    public function it_pops_array_element_if_there_is_only_one(): void
    {
        $this->format(['test_element_01'])->shouldReturn('test_element_01');
    }

    public function it_returns_array_elements_separated_by_comma(): void
    {
        $this->format(['test_element_01, test_element_02'])->shouldReturn('test_element_01, test_element_02');
    }
}
