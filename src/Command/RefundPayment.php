<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Command;

class RefundPayment implements PaymentIdAwareCommandInterface
{
    public function __construct(protected int $paymentId)
    {
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }
}
