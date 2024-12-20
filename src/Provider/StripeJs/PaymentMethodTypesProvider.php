<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

final readonly class PaymentMethodTypesProvider implements PaymentMethodTypesProviderInterface
{
    /**
     * @param string[] $paymentMethodTypes
     */
    public function __construct(private array $paymentMethodTypes)
    {
    }

    public function getPaymentMethodTypes(PaymentInterface $payment): array
    {
        return $this->paymentMethodTypes;
    }
}
