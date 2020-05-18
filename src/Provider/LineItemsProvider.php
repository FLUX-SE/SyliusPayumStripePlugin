<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class LineItemsProvider implements LineItemsProviderInterface
{
    /** @var LineItemProviderInterface */
    private $lineItemProvider;

    /** @var ShippingLineItemProviderInterface */
    private $shippingLineItemProvider;

    public function __construct(
        LineItemProviderInterface $lineItemProvider,
        ShippingLineItemProviderInterface $shippingLineItemProvider
    ) {
        $this->lineItemProvider = $lineItemProvider;
        $this->shippingLineItemProvider = $shippingLineItemProvider;
    }

    /**
     * {@inheritdoc}
     */
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
