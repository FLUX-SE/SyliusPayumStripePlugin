<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\PaymentMethodTypesProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;

final class DetailsProviderSpec extends ObjectBehavior
{
    public function let(
        CustomerEmailProviderInterface $customerEmailProvider,
        LineItemsProviderInterface $lineItemsProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider
    ): void {
        $this->beConstructedWith(
            $customerEmailProvider,
            $lineItemsProvider,
            $paymentMethodTypesProvider
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DetailsProvider::class);
        $this->shouldHaveType(DetailsProviderInterface::class);
    }

    public function it_get_full_details(
        OrderInterface $order,
        CustomerEmailProviderInterface $customerEmailProvider,
        LineItemsProviderInterface $lineItemsProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider
    ): void {
        $customerEmailProvider->getCustomerEmail($order)->willReturn('customer@domain.tld');
        $lineItemsProvider->getLineItems($order)->willReturn([]);
        $paymentMethodTypesProvider->getPaymentMethodTypes($order)->willReturn(['card']);

        $this->getDetails($order)->shouldReturn([
            'customer_email' => 'customer@domain.tld',
            'line_items' => [],
            'payment_method_types' => ['card'],
        ]);
    }

    public function it_get_minimum_details(
        OrderInterface $order,
        CustomerEmailProviderInterface $customerEmailProvider,
        LineItemsProviderInterface $lineItemsProvider,
        PaymentMethodTypesProviderInterface $paymentMethodTypesProvider
    ): void {
        $customerEmailProvider->getCustomerEmail($order)->willReturn(null);
        $lineItemsProvider->getLineItems($order)->willReturn(null);
        $paymentMethodTypesProvider->getPaymentMethodTypes($order)->willReturn(['card']);

        $this->getDetails($order)->shouldReturn([
            'payment_method_types' => ['card'],
        ]);
    }
}
