<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Command;

class CaptureAuthorizedPayment implements PaymentIdAwareCommandInterface
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
