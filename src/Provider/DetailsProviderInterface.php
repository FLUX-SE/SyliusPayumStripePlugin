<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

interface DetailsProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getDetails(OrderInterface $order): array;
}
