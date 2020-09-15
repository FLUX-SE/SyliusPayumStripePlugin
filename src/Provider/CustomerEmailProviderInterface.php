<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface CustomerEmailProviderInterface
{
    public function getCustomerEmail(OrderInterface $order): ?string;
}
