<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface PaymentMethodTypesProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return string[]
     */
    public function getPaymentMethodTypes(OrderInterface $order): array;
}
