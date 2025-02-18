<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

interface LineItemProviderInterface
{
    /**
     * @return array<string, mixed>|null
     */
    public function getLineItem(OrderItemInterface $orderItem): ?array;
}
