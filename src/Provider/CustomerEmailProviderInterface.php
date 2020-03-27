<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface CustomerEmailProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return string|null
     */
    public function getCustomerEmail(OrderInterface $order): ?string;
}
