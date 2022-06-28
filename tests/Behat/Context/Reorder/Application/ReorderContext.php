<?php

declare(strict_types=1);

namespace Tests\Sylius\CustomerReorderPlugin\Behat\Context\Reorder\Application;

use Behat\Behat\Context\Context;
use Exception;
use InvalidArgumentException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Webmozart\Assert\Assert;

final class ReorderContext implements Context
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CustomerRepositoryInterface $customerRepository,
        private ReordererInterface $reorderer,
    ) {
    }

    /**
     * @When the customer :customerEmail tries to reorder the order :orderNumber
     */
    public function theCustomerTriesToReorderTheOrder(string $customerEmail, string $orderNumber): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);
        $channel = $order->getChannel();
        Assert::notNull($channel);

        try {
            $this->reorderer->reorder($order, $channel, $customer);
        } catch (InvalidArgumentException $exception) {
            return;
        }

        throw new Exception('Reorder should fail');
    }

    /**
     * @Then the order :orderNumber should not be reordered
     */
    public function theOrderShouldNotBeReordered(string $orderNumber): void
    {
        // skipped intentionally - not relevant as the condition was checked in previous step
    }
}
