<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Command;

class RefundPayment implements PaymentIdAwareCommandInterface
{
    protected int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }
}
