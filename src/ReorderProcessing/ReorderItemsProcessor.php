<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

if (!interface_exists(\Sylius\Resource\Factory\FactoryInterface::class)) {
    class_alias(\Sylius\Component\Resource\Factory\FactoryInterface::class, \Sylius\Resource\Factory\FactoryInterface::class);
}
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ReorderItemsProcessor implements ReorderProcessor
{
    /**
     * @param FactoryInterface<OrderItemInterface> $orderItemFactory
     */
    public function __construct(
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private OrderModifierInterface $orderModifier,
        private AvailabilityCheckerInterface $availabilityChecker,
        private FactoryInterface $orderItemFactory,
    ) {
    }

    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        $orderItems = $order->getItems();

        foreach ($orderItems as $orderItem) {
            $productVariant = $orderItem->getVariant();
            if (null === $productVariant ||
                !$this->availabilityChecker->isStockAvailable($productVariant)
            ) {
                continue;
            }
            if (!$this->availabilityChecker->isStockSufficient($productVariant, $orderItem->getQuantity())) {
                $onHand = $productVariant->getOnHand();
                Assert::integer($onHand);
                $onHold = $productVariant->getOnHold();
                Assert::integer($onHold);
                $reorderItemQuantity = $onHand - $onHold;
            } else {
                $reorderItemQuantity = $orderItem->getQuantity();
            }
            $newItem = $this->orderItemFactory->createNew();

            $newItem->setVariant($productVariant);
            $newItem->setUnitPrice($orderItem->getUnitPrice());
            $newItem->setProductName($orderItem->getProductName());
            $newItem->setVariantName($orderItem->getVariantName());

            $this->orderItemQuantityModifier->modify($newItem, $reorderItemQuantity);
            $this->orderModifier->addToOrder($reorder, $newItem);
        }
    }
}
