<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use Sylius\Component\Core\Model\PaymentInterface;

interface AfterUrlProviderInterface
{
    public function getAfterPath(PaymentInterface $payment): string;

    public function getAfterParameters(PaymentInterface $payment): array;
}
