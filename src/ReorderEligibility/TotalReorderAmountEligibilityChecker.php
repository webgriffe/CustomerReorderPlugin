<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderEligibility;

use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\EligibilityCheckerFailureResponses;

final class TotalReorderAmountEligibilityChecker implements ReorderEligibilityChecker
{
    public function __construct(private MoneyFormatterInterface $moneyFormatter)
    {
    }

    #[\Override]
    public function check(OrderInterface $order, OrderInterface $reorder): array
    {
        if ($order->getTotal() === $reorder->getTotal()) {
            return [];
        }

        /** @var string $currencyCode */
        $currencyCode = $order->getCurrencyCode();
        $formattedTotal = $this->moneyFormatter->format($order->getTotal(), $currencyCode);

        $eligibilityCheckerResponse = new ReorderEligibilityCheckerResponse(
            EligibilityCheckerFailureResponses::TOTAL_AMOUNT_CHANGED,
            [
                '%order_total%' => $formattedTotal,
            ],
        );

        return [$eligibilityCheckerResponse];
    }
}
