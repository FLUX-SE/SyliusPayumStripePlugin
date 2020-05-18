<?php

declare(strict_types=1);

namespace spec\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemImagesProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemProvider;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LinetItemNameProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class LineItemProviderSpec extends ObjectBehavior
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
        $orderItem->getQuantity()->willReturn(1);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn(['/path/image.jpg']);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My item name',
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
        $orderItem->getQuantity()->willReturn(1);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn([]);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My item name',
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
        $orderItem->getQuantity()->willReturn(2);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn([]);
        $lineItemNameProvider->getItemName($orderItem)->willReturn('My item name');

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 500,
            'currency' => 'USD',
            'name' => 'My item name',
            'quantity' => 2,
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
