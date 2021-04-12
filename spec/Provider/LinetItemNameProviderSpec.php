<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

class LinetItemNameProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LinetItemNameProvider::class);
        $this->shouldHaveType(LinetItemNameProviderInterface::class);
    }

    public function it_get_product_and_variant_name_when_product_has_options(
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getProductName()->willReturn('My Product');
        $orderItem->getVariantName()->willReturn('variant');

        $product->hasOptions()->willReturn(true);

        $this->getItemName($orderItem)->shouldReturn('1x - My Product variant');
    }

    public function it_get_product_and_variant_name_when_product_has_no_options(
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getProductName()->willReturn('My Product');
        $orderItem->getVariantName()->willReturn('variant');

        $product->hasOptions()->willReturn(false);

        $this->getItemName($orderItem)->shouldReturn('1x - variant');
    }

    public function it_get_item_name_with_variant_name(
        OrderItemInterface $orderItem,
        ProductInterface $product
    ): void {
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getProduct()->willReturn($product);
        $orderItem->getProductName()->willReturn(null);
        $orderItem->getVariantName()->willReturn('My variant name');

        $product->hasOptions()->willReturn(false);

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
