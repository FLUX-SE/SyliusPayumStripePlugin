<?php

declare(strict_types=1);

namespace spec\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemImagesProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemProvider;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

class LineItemProviderSpec extends ObjectBehavior
{
    public function let(LineItemImagesProviderInterface $lineItemImagesProvider): void
    {
        $this->beConstructedWith($lineItemImagesProvider);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LineItemProvider::class);
        $this->shouldHaveType(LineItemProviderInterface::class);
    }

    public function it_get_line_item(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $orderItem->getVariantName()->willReturn(null);
        $orderItem->getProductName()->willReturn('My product name');
        $orderItem->getQuantity()->willReturn(1);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn(['/path/image.jpg']);

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My product name',
            'quantity' => 1,
            'images' => [
                '/path/image.jpg',
            ],
        ]);
    }

    public function it_get_line_item_with_product_variant_name(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $orderItem->getVariantName()->willReturn('My product variant name');
        $orderItem->getQuantity()->willReturn(1);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn(['/path/image.jpg']);

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My product variant name',
            'quantity' => 1,
            'images' => [
                '/path/image.jpg',
            ],
        ]);
    }

    public function it_get_line_item_with_no_images(
        OrderItemInterface $orderItem,
        OrderInterface $order,
        LineItemImagesProviderInterface $lineItemImagesProvider
    ): void {
        $orderItem->getOrder()->willReturn($order);
        $orderItem->getTotal()->willReturn(1000);
        $orderItem->getVariantName()->willReturn('My product variant name');
        $orderItem->getQuantity()->willReturn(1);
        $order->getCurrencyCode()->willReturn('USD');
        $lineItemImagesProvider->getImageUrls($orderItem)->willReturn([]);

        $this->getLineItem($orderItem)->shouldReturn([
            'amount' => 1000,
            'currency' => 'USD',
            'name' => 'My product variant name',
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
