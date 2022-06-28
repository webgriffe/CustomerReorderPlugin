<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ReorderItemsProcessor implements ReorderProcessor
{
    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    /** @var FactoryInterface */
    private $orderItemFactory;

    public function __construct(
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        OrderModifierInterface $orderModifier,
        AvailabilityCheckerInterface $availabilityChecker,
        FactoryInterface $orderItemFactory,
    ) {
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->orderModifier = $orderModifier;
        $this->availabilityChecker = $availabilityChecker;
        $this->orderItemFactory = $orderItemFactory;
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

            /** @var OrderItemInterface $newItem */
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
