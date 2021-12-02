<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemsProviderSpec extends ObjectBehavior
{
    public function let(
        LineItemProviderInterface $lineItemProvider,
        ShippingLineItemProviderInterface $shippingLineItemProvider
    ): void {
        $this->beConstructedWith(
            $lineItemProvider,
            $shippingLineItemProvider
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LineItemsProvider::class);
        $this->shouldHaveType(LineItemsProviderInterface::class);
    }

    public function it_get_line_items(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        LineItemProviderInterface $lineItemProvider,
        ShippingLineItemProviderInterface $shippingLineItemProvider
    ): void {
        $lineItem = [];
        $orderItems = new ArrayCollection([
            $orderItem->getWrappedObject(),
        ]);
        $order->getItems()->willReturn($orderItems);
        $lineItemProvider->getLineItem($orderItem)->willReturn($lineItem);
        $shippingLineItemProvider->getLineItem($order)->willReturn(null);

        $this->getLineItems($order)->shouldReturn([
            $lineItem,
        ]);
    }

    public function it_get_empty_line_items(
        OrderInterface $order,
        ShippingLineItemProviderInterface $shippingLineItemProvider
    ): void {
        $orderItems = new ArrayCollection([]);
        $order->getItems()->willReturn($orderItems);
        $shippingLineItemProvider->getLineItem($order)->willReturn(null);

        $this->getLineItems($order)->shouldReturn([]);
    }
}
