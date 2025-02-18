<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final readonly class PaymentMethodTypesProvider implements PaymentMethodTypesProviderInterface
{
    /**
     * @param string[] $paymentMethodTypes
     */
    public function __construct(private array $paymentMethodTypes)
    {
    }

    public function getPaymentMethodTypes(OrderInterface $order): array
    {
        return $this->paymentMethodTypes;
    }
}
