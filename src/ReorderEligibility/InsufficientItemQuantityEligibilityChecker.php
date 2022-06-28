<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;
use Webmozart\Assert\Assert;

final class InsufficientItemQuantityEligibilityChecker implements ReorderEligibilityChecker
{
    public function __construct(
        private ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
    ) {
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        $orderProductNamesToQuantity = [];
        $reorderProductNamesToQuantity = [];

        foreach ($order->getItems()->getValues() as $item) {
            $productName = $item->getProductName();
            Assert::notNull($productName);
            $orderProductNamesToQuantity[$productName] = $item->getQuantity();
        }
        foreach ($reorder->getItems()->getValues() as $item) {
            $productName = $item->getProductName();
            Assert::notNull($productName);
            $reorderProductNamesToQuantity[$productName] = $item->getQuantity();
        }

        $insufficientItems = [];

        foreach (array_keys($orderProductNamesToQuantity) as $productName) {
            if (!array_key_exists($productName, $reorderProductNamesToQuantity)) {
                continue;
            }

            if ($orderProductNamesToQuantity[$productName] > $reorderProductNamesToQuantity[$productName]) {
                $insufficientItems[] = $productName;
            }
        }

        if (0 === count($insufficientItems)) {
            return [];
        }

        $reorderEligibilityCheckerResponse = new ReorderEligibilityCheckerResponse(
            EligibilityCheckerFailureResponses::INSUFFICIENT_ITEM_QUANTITY,
            [
                '%order_items%' => $this->reorderEligibilityConstraintMessageFormatter->format($insufficientItems),
            ],
        );

        return [$reorderEligibilityCheckerResponse];
    }
}
