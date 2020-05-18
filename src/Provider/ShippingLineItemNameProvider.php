<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class ShippingLineItemNameProvider implements ShippingLineItemNameProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getItemName(OrderInterface $order): string
    {
        $shipmentNames = [];
        foreach ($order->getShipments() as $shipment) {
            $shipmentMethod = $shipment->getMethod();
            if (null === $shipmentMethod) {
                continue;
            }

            $shipmentNames[] = $shipmentMethod->getName();
        }

        return implode(', ', $shipmentNames);
    }
}
