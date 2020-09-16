<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderItemInterface;

interface LinetItemNameProviderInterface
{
    public function getItemName(OrderItemInterface $orderItem): string;
}
