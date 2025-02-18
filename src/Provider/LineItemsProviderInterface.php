<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface LineItemsProviderInterface
{
    /**
     * @return array<array-key, mixed>|null
     */
    public function getLineItems(OrderInterface $order): ?array;
}
