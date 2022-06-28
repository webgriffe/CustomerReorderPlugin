<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class ReorderPromotionsEligibilityChecker implements ReorderEligibilityChecker
{
    public function __construct(
        private ReorderEligibilityConstraintMessageFormatterInterface $reorderEligibilityConstraintMessageFormatter,
    ) {
    }

    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        if (0 === count($reorder->getItems()->getValues()) ||
            $order->getPromotions()->getValues() === $reorder->getPromotions()->getValues()
        ) {
            return [];
        }

        $disabledPromotions = [];

        /** @var PromotionInterface $promotion */
        foreach ($order->getPromotions()->getValues() as $promotion) {
            if (!in_array($promotion, $reorder->getPromotions()->getValues(), true)) {
                $disabledPromotions[] = $promotion->getName();
            }
        }

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse(
            EligibilityCheckerFailureResponses::REORDER_PROMOTIONS_CHANGED,
            [
                '%promotion_names%' => $this->reorderEligibilityConstraintMessageFormatter->format($disabledPromotions),
            ],
        );

        return [$eligibilityCheckerResponse];
    }
}
