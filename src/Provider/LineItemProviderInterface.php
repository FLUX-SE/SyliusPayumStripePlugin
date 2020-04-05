<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

interface LineItemProviderInterface
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return array|null
     */
    public function getLineItem(OrderItemInterface $orderItem): ?array;
}
