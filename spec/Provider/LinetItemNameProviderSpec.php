<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;

class LinetItemNameProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LinetItemNameProvider::class);
        $this->shouldHaveType(LinetItemNameProviderInterface::class);
    }

    public function it_get_item_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProductName()->willReturn('My Product');
        $orderItem->getVariantName()->willReturn('variant');

        $this->getItemName($orderItem)->shouldReturn('1x - My Product variant');
    }

    public function it_get_item_name_with_variant_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProductName()->willReturn(null);
        $orderItem->getVariantName()->willReturn('My variant name');

        $this->getItemName($orderItem)->shouldReturn('1x - My variant name');
    }

    public function it_get_item_name_with_product_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProductName()->willReturn('My variant name');
        $orderItem->getVariantName()->willReturn(null);

        $this->getItemName($orderItem)->shouldReturn('1x - My variant name');
    }

    public function it_get_item_name_without_variant_name_or_product_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProductName()->willReturn(null);
        $orderItem->getVariantName()->willReturn(null);

        $this->getItemName($orderItem)->shouldReturn('1x - ');
    }
}
