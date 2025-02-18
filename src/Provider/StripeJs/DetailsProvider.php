<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

final readonly class DetailsProvider implements DetailsProviderInterface
{
    public function __construct(private AmountProviderInterface $amountProvider, private CurrencyProviderInterface $currencyProvider, private PaymentMethodTypesProviderInterface $paymentMethodTypesProvider)
    {
    }

    public function getDetails(PaymentInterface $payment): array
    {
        return [
            'amount' => $this->amountProvider->getAmount($payment),
            'currency' => $this->currencyProvider->getCurrency($payment),
            'payment_method_types' => $this->paymentMethodTypesProvider->getPaymentMethodTypes($payment),
        ];
    }
}
