<?php

declare(strict_types=1);

namespace spec\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\CustomerEmailProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\DetailsProvider;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\DetailsProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\LineItemsProviderInterface;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\PaymentMethodTypesProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;

class DetailsProviderSpec extends ObjectBehavior
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
