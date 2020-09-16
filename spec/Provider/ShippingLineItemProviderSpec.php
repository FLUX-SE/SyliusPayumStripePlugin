<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;

class ShippingLineItemProviderSpec extends ObjectBehavior
{
    public function let(
        ShippingLineItemNameProviderInterface $shippingLineItemNameProvider
    ): void {
        $this->beConstructedWith(
            $shippingLineItemNameProvider
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingLineItemProvider::class);
        $this->shouldHaveType(ShippingLineItemProviderInterface::class);
    }

    public function it_get_line_item(
        OrderInterface $order,
        ShippingLineItemNameProviderInterface $shippingLineItemNameProvider
    ): void {
        $shippingLineItemNameProvider->getItemName($order)->willReturn('My shipping method');
        $order->getShippingTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');

        $this->getLineItem($order)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My shipping method',
            'quantity' => 1,
        ]);
    }

    public function it_get_line_item_when_there_is_no_shipping(
        OrderInterface $order
    ): void {
        $order->getShippingTotal()->willReturn(0);
        $this->getLineItem($order)->shouldReturn(null);
    }
}
