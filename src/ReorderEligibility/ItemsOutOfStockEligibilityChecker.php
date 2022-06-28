<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ItemsOutOfStockEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var ReorderEligibilityConstraintMessageFormatterInterface */
    private $reorderEligibilityConstraintMessageFormatter;

    /** @var AvailabilityCheckerInterface */
    private $availabilityChecker;

    public function __construct(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
        AvailabilityCheckerInterface $availabilityChecker,
    ) {
        $this->reorderEligibilityConstraintMessageFormatter = $reorderEligibilityConstraintMessageFormatter;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $productsOutOfStock = [];

        foreach ($order->getItems()->getValues() as $orderItem) {
            if (null === $orderItem->getVariant()) {
                continue;
            }

            /** @var ProductVariantInterface $productVariant */
            $productVariant = $orderItem->getVariant();
            if (!$this->availabilityChecker->isStockAvailable($productVariant)) {
                $productsOutOfStock[] = $orderItem->getProductName();
            }
        }

        if (0 === count($productsOutOfStock)) {
            return [];
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse(
            EligibilityCheckerFailureResponses::ITEMS_OUT_OF_STOCK,
            [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($productsOutOfStock),
            ],
        );

        return [$eligibilityCheckerResponse];
    }
}
