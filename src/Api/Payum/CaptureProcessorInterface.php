<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use Sylius\Component\Core\Model\PaymentInterface;

interface CaptureProcessorInterface
{
    public function __invoke(PaymentInterface $payment): array;
}
