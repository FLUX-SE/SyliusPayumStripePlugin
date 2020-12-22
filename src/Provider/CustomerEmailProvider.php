<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class CustomerEmailProvider implements CustomerEmailProviderInterface
{
    public function getCustomerEmail(OrderInterface $order): ?string
    {
        $customer = $order->getCustomer();
        if (null === $customer) {
            return null;
        }

        return $customer->getEmail();
    }
}
