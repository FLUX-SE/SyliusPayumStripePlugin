<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface ShippingLineItemProviderInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getLineItem(OrderInterface $order): ?array;
}
