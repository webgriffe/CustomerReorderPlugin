<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderProcessing\ReorderProcessor;

final class OrderFactory implements OrderFactoryInterface
{
    /**
     * @param FactoryInterface<OrderInterface> $baseOrderFactory
     */
    public function __construct(private FactoryInterface $baseOrderFactory, private ReorderProcessor $reorderProcessor)
    {
    }

    public function createFromExistingOrder(OrderInterface $order, ChannelInterface $channel): OrderInterface
    {
        $reorder = $this->baseOrderFactory->createNew();

        $reorder->setChannel($channel);
        $this->reorderProcessor->process($order, $reorder);

        return $reorder;
    }
}
