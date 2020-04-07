<?php

declare(strict_types=1);

namespace spec\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\PaymentMethodTypesProvider;
use Prometee\SyliusPayumStripeCheckoutSessionPlugin\Provider\PaymentMethodTypesProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;

class PaymentMethodTypesProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PaymentMethodTypesProvider::class);
        $this->shouldHaveType(PaymentMethodTypesProviderInterface::class);
    }

    public function it_get_payment_method_types(
        OrderInterface $order
    ): void {
        $this->getPaymentMethodTypes($order)->shouldReturn([
            'card',
        ]);
    }
}
