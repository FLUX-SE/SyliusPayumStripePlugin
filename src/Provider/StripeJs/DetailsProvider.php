<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

final class DetailsProvider implements DetailsProviderInterface
{
    private AmountProviderInterface $amountProvider;

    private CurrencyProviderInterface $currencyProvider;

    private PaymentMethodTypesProviderInterface $paymentMethodTypesProvider;

    public function __construct(
        AmountProviderInterface $amountProvider,
        CurrencyProviderInterface $currencyProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider,
    ) {
        $this->amountProvider = $amountProvider;
        $this->currencyProvider = $currencyProvider;
        $this->paymentMethodTypesProvider = $paymentMethodTypesProvider;
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
