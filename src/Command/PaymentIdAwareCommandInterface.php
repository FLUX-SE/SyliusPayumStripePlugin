<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Command;

interface PaymentIdAwareCommandInterface
{
    public function getPaymentId(): int;
}
