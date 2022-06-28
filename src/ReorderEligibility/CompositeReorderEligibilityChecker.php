<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Laminas\Stdlib\PriorityQueue;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class CompositeReorderEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var PriorityQueue<ReorderEligibilityChecker, int> */
    private $eligibilityCheckers;

    public function __construct()
    {
        /** @var PriorityQueue<ReorderEligibilityChecker, int> $priorityQueue */
        $priorityQueue = new PriorityQueue();
        $this->eligibilityCheckers = $priorityQueue;
    }

    public function addChecker(ReorderEligibilityChecker $eligibilityChecker, int $priority = 0): void
    {
        $this->eligibilityCheckers->insert($eligibilityChecker, $priority);
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $eligibilityCheckersFailures = [];

        /** @var mixed|ReorderEligibilityChecker $eligibilityChecker */
        foreach ($this->eligibilityCheckers as $eligibilityChecker) {
            Assert::isInstanceOf($eligibilityChecker, ReorderEligibilityChecker::class);
            $eligibilityCheckersFailures = array_merge(
                $eligibilityCheckersFailures,
                $eligibilityChecker->check($order, $reorder),
            );
        }

        return $eligibilityCheckersFailures;
    }
}
