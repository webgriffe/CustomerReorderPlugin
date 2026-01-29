<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Laminas\Stdlib\PriorityQueue;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class CompositeReorderProcessor implements ReorderProcessor
{
    /** @var PriorityQueue<ReorderProcessor, int> */
    private PriorityQueue $reorderProcessors;

    public function __construct()
    {
        /** @var PriorityQueue<ReorderProcessor, int> $priorityQueue */
        $priorityQueue = new PriorityQueue();
        $this->reorderProcessors = $priorityQueue;
    }

    public function addProcessor(ReorderProcessor $orderProcessor, int $priority = 0): void
    {
        $this->reorderProcessors->insert($orderProcessor, $priority);
    }

    #[\Override]
    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        /** @var mixed|ReorderProcessor $reorderProcessor */
        foreach ($this->reorderProcessors as $reorderProcessor) {
            Assert::isInstanceOf($reorderProcessor, ReorderProcessor::class);
            $reorderProcessor->process($order, $reorder);
        }
    }
}
