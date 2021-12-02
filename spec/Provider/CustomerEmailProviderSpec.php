<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerEmailProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CustomerEmailProvider::class);
        $this->shouldHaveType(CustomerEmailProviderInterface::class);
    }

    public function it_get_customer_email_from_a_customer(
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $order->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('customer@domain.tld');

        $this->getCustomerEmail($order)->shouldReturn('customer@domain.tld');
    }

    public function it_could_return_null(
        OrderInterface $order
    ): void {
        $order->getCustomer()->willReturn(null);

        $this->getCustomerEmail($order)->shouldReturn(null);
    }
}
