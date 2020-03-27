<?php

declare(strict_types=1);

namespace Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;


use Sylius\Component\Core\Model\OrderInterface;

interface LineItemsProviderInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return string[]|null
     */
    public function getLineItems(OrderInterface $order): ?array;
}
