<?php

declare(strict_types=1);

namespace Sylius\CustomerReorderPlugin\Controller;

use InvalidArgumentException;
use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\CustomerReorderPlugin\Reorder\ReordererInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CustomerReorderAction
{
    public function __construct(
        private CartSessionStorage $cartSessionStorage,
        private ChannelContextInterface $channelContext,
        private CustomerContextInterface $customerContext,
        private OrderRepositoryInterface $orderRepository,
        private ReordererInterface $reorderer,
        private UrlGeneratorInterface $urlGenerator,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($request->attributes->get('id'));

        $channel = $this->channelContext->getChannel();
        assert($channel instanceof ChannelInterface);

        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        try {
            $reorder = $this->reorderer->reorder($order, $channel, $customer);
        } catch (InvalidArgumentException $exception) {
            $this->requestStack->getSession()->getFlashBag()->add('info', $exception->getMessage());

            return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
        }

        $this->cartSessionStorage->setForChannel($channel, $reorder);

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_cart_summary'));
    }
}
