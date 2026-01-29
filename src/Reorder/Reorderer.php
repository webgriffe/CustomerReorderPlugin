<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Reorder;

use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\CustomerReorderPlugin\Checker\OrderCustomerRelationCheckerInterface;
use Sylius\CustomerReorderPlugin\Factory\OrderFactoryInterface;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ReorderEligibilityChecker;
use Sylius\CustomerReorderPlugin\ReorderEligibility\ResponseProcessing\ReorderEligibilityCheckerResponseProcessorInterface;

final class Reorderer implements ReordererInterface
{
    public function __construct(
        private OrderFactoryInterface $orderFactory,
        private EntityManagerInterface $entityManager,
        private ReorderEligibilityChecker $reorderEligibilityChecker,
        private ReorderEligibilityCheckerResponseProcessorInterface $reorderEligibilityCheckerResponseProcessor,
        private OrderCustomerRelationCheckerInterface $orderCustomerRelationCheckerInterface,
    ) {
    }

    #[\Override]
    public function reorder(
        OrderInterface $order,
        ChannelInterface $channel,
        CustomerInterface $customer,
    ): OrderInterface {
        if (!$this->orderCustomerRelationCheckerInterface->wasOrderPlacedByCustomer($order, $customer)) {
            throw new InvalidArgumentException("The customer is not the order's owner.");
        }

        $reorder = $this->orderFactory->createFromExistingOrder($order, $channel);
        if (0 === count($reorder->getItems()->getValues())) {
            throw new InvalidArgumentException('sylius.reorder.none_of_items_is_available');
        }

        $reorderEligibilityChecks = $this->reorderEligibilityChecker->check($order, $reorder);
        $this->reorderEligibilityCheckerResponseProcessor->process($reorderEligibilityChecks);

        $this->entityManager->persist($reorder);
        $this->entityManager->flush();

        return $reorder;
    }
}
