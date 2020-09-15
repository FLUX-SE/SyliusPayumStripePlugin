<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use LogicException;
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

        $this->getItemName($orderItem)->shouldReturn('1x - My Product');
    }

    public function it_get_item_name_with_variant_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getProductName()->willReturn(null);
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getVariantName()->willReturn('My variant name');

        $this->getItemName($orderItem)->shouldReturn('1x - My variant name');
    }

    public function it_throw_logic_exception_when_get_item_name_without_variant_name_or_product_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getProductName()->willReturn(null);
        $orderItem->getVariantName()->willReturn(null);

        $this
            ->shouldThrow(LogicException::class)
            ->during('getItemName', [$orderItem])
        ;
    }

    public function it_throw_logic_exception_when_get_item_name_with_an_empty_product_name(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getProductName()->willReturn('');

        $this
            ->shouldThrow(LogicException::class)
            ->during('getItemName', [$orderItem])
        ;
    }
}
