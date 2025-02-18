<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final readonly class DetailsProvider implements DetailsProviderInterface
{
    public function __construct(private CustomerEmailProviderInterface $customerEmailProvider, private LineItemsProviderInterface $lineItemsProvider, private PaymentMethodTypesProviderInterface $paymentMethodTypesProvider, private ModeProviderInterface $modeProvider)
    {
    }

    public function getDetails(OrderInterface $order): array
    {
        $details = [];

        $customerEmail = $this->customerEmailProvider->getCustomerEmail($order);
        if (null !== $customerEmail) {
            $details['customer_email'] = $customerEmail;
        }

        $details['mode'] = $this->modeProvider->getMode($order);

        $lineItems = $this->lineItemsProvider->getLineItems($order);
        if (null !== $lineItems) {
            $details['line_items'] = $lineItems;
        }

        $paymentMethodTypes = $this->paymentMethodTypesProvider->getPaymentMethodTypes($order);
        if ([] !== $paymentMethodTypes) {
            $details['payment_method_types'] = $paymentMethodTypes;
        }

        return $details;
    }
}
