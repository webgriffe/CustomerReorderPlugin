<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\ReorderProcessing;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ReorderDataProvider implements ReorderProcessor
{
    public function process(OrderInterface $order, OrderInterface $reorder): void
    {
        $reorder->setCustomer($order->getCustomer());
        $reorder->setCurrencyCode($order->getCurrencyCode());
        $reorder->setNotes($order->getNotes());
        $reorder->setLocaleCode($order->getLocaleCode());

        $billingAddress = $order->getBillingAddress();
        if ($billingAddress !== null) {
            $reorder->setBillingAddress(clone $billingAddress);
        }
        $shippingAddress = $order->getShippingAddress();
        if ($shippingAddress !== null) {
            $reorder->setShippingAddress(clone $shippingAddress);
        }
    }
}
