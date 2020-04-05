<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface DetailsProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return array
     */
    public function getDetails(OrderInterface $order): array;
}
