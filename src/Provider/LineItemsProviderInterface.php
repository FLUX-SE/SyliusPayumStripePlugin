<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface LineItemsProviderInterface
{
    public function getLineItems(OrderInterface $order): ?array;
}
