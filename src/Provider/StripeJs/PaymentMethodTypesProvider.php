<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider\StripeJs;

use Sylius\Component\Core\Model\PaymentInterface;

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

    public function getPaymentMethodTypes(PaymentInterface $payment): array
    {
        return $this->paymentMethodTypes;
    }
}
