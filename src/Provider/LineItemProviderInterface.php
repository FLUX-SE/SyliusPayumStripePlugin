<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

interface LineItemProviderInterface
{
    /**
     * @return string[]|null
     */
    public function getLineItem(OrderItemInterface $orderItem): ?array;
}
