<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final readonly class LineItemsProvider implements LineItemsProviderInterface
{
    public function __construct(private LineItemProviderInterface $lineItemProvider, private ShippingLineItemProviderInterface $shippingLineItemProvider)
    {
    }

    public function getLineItems(OrderInterface $order): ?array
    {
        $lineItems = [];
        foreach ($order->getItems() as $orderItem) {
            $lineItem = $this->lineItemProvider->getLineItem($orderItem);
            if (null !== $lineItem) {
                $lineItems[] = $lineItem;
            }
        }

        $lineItem = $this->shippingLineItemProvider->getLineItem($order);
        if (null !== $lineItem) {
            $lineItems[] = $lineItem;
        }

        return $lineItems;
    }
}
