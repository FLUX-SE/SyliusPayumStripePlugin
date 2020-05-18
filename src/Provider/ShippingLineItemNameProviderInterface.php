<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface ShippingLineItemNameProviderInterface
{
    public function getItemName(OrderInterface $order): string;
}
