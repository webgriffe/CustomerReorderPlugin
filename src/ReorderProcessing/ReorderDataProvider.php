<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReorderDataProvider implements ReorderProcessor
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[\Override]
    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        $reorder->setCustomer($order->getCustomer());
        $reorder->setCurrencyCode($order->getCurrencyCode());
        $reorder->setNotes($order->getNotes());
        $reorder->setLocaleCode($order->getLocaleCode());

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress !== null) {
            $this->entityManager->initializeObject($billingAddress);
            $reorder->setBillingAddress(clone $billingAddress);
        }
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress !== null) {
            $this->entityManager->initializeObject($shippingAddress);
            $reorder->setShippingAddress(clone $shippingAddress);
        }
    }
}
