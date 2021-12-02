<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemProviderSpec extends ObjectBehavior
{
    public function let(
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider
    ): void {
        $this->beConstructedWith(
            $lineItemImagesProvider,
            $lineItemNameProvider
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LineItemProvider::class);
        $this->shouldHaveType(LineItemProviderInterface::class);
    }

    public function it_get_line_item(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn(['/path/image.jpg']);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('1x - My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => '1x - My item name',
            'quantity' => 1,
            'images' => [
                '/path/image.jpg',
            ],
        ]);
    }

    public function it_get_line_item_with_no_images(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn([]);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('1x - My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => '1x - My item name',
            'quantity' => 1,
            'images' => [],
        ]);
    }

    public function it_get_line_item_when_quantity_is_greater_than_1(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider,
        LinetItemNameProviderInterface $lineItemNameProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn([]);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('2x - My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => '2x - My item name',
            'quantity' => 1,
            'images' => [],
        ]);
    }

    public function it_get_line_item_with_null_order(
        OrderItemInterface $orderItem
    ): void {
        $orderItem->getOrder()->willReturn(null);

        $this->getLineItem($orderItem)->shouldReturn(null);
    }
}
