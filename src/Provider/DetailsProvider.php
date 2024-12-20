<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;

final class DetailsProvider implements DetailsProviderInterface
{
    /** @var CustomerEmailProviderInterface */
    private $customerEmailProvider;

    /** @var LineItemsProviderInterface */
    private $lineItemsProvider;

    /** @var PaymentMethodTypesProviderInterface */
    private $paymentMethodTypesProvider;

    /** @var ModeProviderInterface */
    private $modeProvider;

    public function __construct(
        CustomerEmailProviderInterface $customerEmailProvider,
        LineItemsProviderInterface $lineItemsProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider,
        ModeProviderInterface $modeProvider,
    ) {
        $this->customerEmailProvider = $customerEmailProvider;
        $this->lineItemsProvider = $lineItemsProvider;
        $this->paymentMethodTypesProvider = $paymentMethodTypesProvider;
        $this->modeProvider = $modeProvider;
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
