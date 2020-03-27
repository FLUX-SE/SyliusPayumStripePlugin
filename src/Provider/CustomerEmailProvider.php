<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class CustomerEmailProvider implements CustomerEmailProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getCustomerEmail(OrderInterface $order): ?string
    {
        $customer = $order->getCustomer();
        if (null === $customer) {
            return null;
        }

        return $order->getCustomer()->getEmail();
    }
}
