<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

interface DetailsProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getDetails(PaymentInterface $payment): array;
}
