<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface PaymentMethodTypesProviderInterface
{
    /**
     * @return string[]
     */
    public function getPaymentMethodTypes(OrderInterface $order): array;
}
