<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

final class DetailsProvider implements DetailsProviderInterface
{
    public function getDetails(PaymentInterface $payment): array
    {
        return [
            'amount' => $payment->getAmount(),
            'currency' => $payment->getCurrencyCode(),
            'payment_method_types' => ['card'],
        ];
    }
}
