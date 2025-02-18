<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final readonly class ShippingLineItemProvider implements ShippingLineItemProviderInterface
{
    public function __construct(private ShippingLineItemNameProviderInterface $shippingLineItemProvider)
    {
    }

    public function getLineItem(OrderInterface $order): ?array
    {
        $shippingTotal = $order->getShippingTotal();

        if (0 === $shippingTotal) {
            return null;
        }

        $priceData = [
            'unit_amount' => $shippingTotal,
            'currency' => $order->getCurrencyCode(),
            'product_data' => [
                'name' => $this->shippingLineItemProvider->getItemName($order),
            ],
        ];

        return [
            'price_data' => $priceData,
            'quantity' => 1,
        ];
    }
}
