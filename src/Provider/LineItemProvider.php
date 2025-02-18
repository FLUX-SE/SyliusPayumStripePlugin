<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final readonly class LineItemProvider implements LineItemProviderInterface
{
    public function __construct(private LineItemImagesProviderInterface $lineItemImagesProvider, private LinetItemNameProviderInterface $lineItemNameProvider)
    {
    }

    public function getLineItem(OrderItemInterface $orderItem): ?array
    {
        /** @var OrderInterface|null $order */
        $order = $orderItem->getOrder();

        if (null === $order) {
            return null;
        }

        $priceData = [
            'unit_amount' => $orderItem->getTotal(),
            'currency' => $order->getCurrencyCode(),
            'product_data' => [
                'name' => $this->lineItemNameProvider->getItemName($orderItem),
                'images' => $this->lineItemImagesProvider->getImageUrls($orderItem),
            ],
        ];

        return [
            'price_data' => $priceData,
            'quantity' => 1,
        ];
    }
}
