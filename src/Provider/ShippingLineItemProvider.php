<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class ShippingLineItemProvider implements ShippingLineItemProviderInterface
{
    /** @var ShippingLineItemNameProviderInterface */
    private $shippingLineItemProvider;

    public function __construct(ShippingLineItemNameProviderInterface $shippingLineItemProvider)
    {
        $this->shippingLineItemProvider = $shippingLineItemProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineItem(OrderInterface $order): ?array
    {
        $shippingTotal = $order->getShippingTotal();

        if (0 === $shippingTotal) {
            return null;
        }

        return [
            'amount' => $shippingTotal,
            'currency' => $order->getCurrencyCode(),
            'name' => $this->shippingLineItemProvider->getItemName($order),
            'quantity' => 1,
        ];
    }
}
