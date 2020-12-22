<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class PaymentMethodTypesProvider implements PaymentMethodTypesProviderInterface
{
    /** @var string[] */
    private $paymentMethodTypes;

    /**
     * @param string[] $paymentMethodTypes
     */
    public function __construct(array $paymentMethodTypes)
    {
        $this->paymentMethodTypes = $paymentMethodTypes;
    }

    public function getPaymentMethodTypes(OrderInterface $order): array
    {
        return $this->paymentMethodTypes;
    }
}
