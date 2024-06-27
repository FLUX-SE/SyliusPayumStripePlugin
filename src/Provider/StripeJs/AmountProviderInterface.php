<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

interface AmountProviderInterface
{
    public function getAmount(PaymentInterface $payment): int;
}
