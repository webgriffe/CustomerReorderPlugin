<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;
use Webmozart\Assert\Assert;

final class ReorderItemPricesEligibilityChecker implements ReorderEligibilityChecker
{
    /** @var ReorderEligibilityConstraintMessageFormatterInterface */
    private $reorderEligibilityConstraintMessageFormatter;

    public function __construct(
        ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
    ) {
        $this->reorderEligibilityConstraintMessageFormatter = $reorderEligibilityConstraintMessageFormatter;
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $orderProductNamesToTotal = [];
        $reorderProductNamesToTotal = [];

        foreach ($order->getItems()->getValues() as $orderItem) {
            $productName = $orderItem->getProductName();
            Assert::notNull($productName);
            $orderProductNamesToTotal[$productName] = $orderItem->getUnitPrice();
        }

        foreach ($reorder->getItems()->getValues() as $reorderItem) {
            $productName = $reorderItem->getProductName();
            Assert::notNull($productName);
            $reorderProductNamesToTotal[$productName] = $reorderItem->getUnitPrice();
        }

        $orderItemsWithChangedPrice = [];

        foreach (array_keys($orderProductNamesToTotal) as $productName) {
            if (!array_key_exists($productName, $reorderProductNamesToTotal)) {
                continue;
            }

            if ($orderProductNamesToTotal[$productName] !== $reorderProductNamesToTotal[$productName]) {
                array_push($orderItemsWithChangedPrice, $productName);
            }
        }

        if (0 === count($orderItemsWithChangedPrice)) {
            return [];
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse(
            EligibilityCheckerFailureResponses::REORDER_ITEMS_PRICES_CHANGED,
            [
                '%product_names%' => $this->reorderEligibilityConstraintMessageFormatter->format($orderItemsWithChangedPrice),
            ],
        );

        return [$eligibilityCheckerResponse];
    }
}
