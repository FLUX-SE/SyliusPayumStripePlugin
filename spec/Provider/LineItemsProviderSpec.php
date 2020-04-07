<?php

declare(strict_types=1);

namespace spec\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemsProvider;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemsProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class LineItemsProviderSpec extends ObjectBehavior
{
    public function let(LineItemProviderInterface $lineItemProvider): void
    {
        $this->beConstructedWith($lineItemProvider);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LineItemsProvider::class);
        $this->shouldHaveType(LineItemsProviderInterface::class);
    }

    public function it_get_line_items(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        LineItemProviderInterface $lineItemProvider
    ): void {
        $lineItem = [];
        $orderItems = new ArrayCollection([
            $orderItem->getWrappedObject(),
        ]);
        $order->getItems()->willReturn($orderItems);
        $lineItemProvider->getLineItem($orderItem)->willReturn($lineItem);

        $this->getLineItems($order)->shouldReturn([
            $lineItem,
        ]);
    }

    public function it_get_empty_line_items(
        OrderInterface $order
    ): void {
        $orderItems = new ArrayCollection([]);
        $order->getItems()->willReturn($orderItems);

        $this->getLineItems($order)->shouldReturn([]);
    }
}
