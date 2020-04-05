<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface LineItemsProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return array|null
     */
    public function getLineItems(OrderInterface $order): ?array;
}
