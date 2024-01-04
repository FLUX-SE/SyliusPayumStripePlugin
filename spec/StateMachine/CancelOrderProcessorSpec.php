<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\CancelRequestFactoryInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use SM\Event\TransitionEvent;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class CancelOrderProcessorSpec extends ObjectBehavior
{
    public function let(
        CancelRequestFactoryInterface $cancelRequestFactory,
        Payum $payum
    ): void {
        $this->beConstructedWith($cancelRequestFactory, $payum);
    }

    public function it_is_invokable(
        Payum $payum,
        PaymentInterface $payment,
        TransitionEvent $event,
        PaymentMethodInterface $paymentMethod,
        GatewayConfigInterface $gatewayConfig,
        GatewayInterface $gateway,
        TokenFactoryInterface $tokenFactory,
        TokenInterface $token,
        CancelRequestFactoryInterface $cancelRequestFactory,
        ModelAggregateInterface $request
    ): void {

        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getConfig()->willReturn(['factory' => 'stripe_checkout_session']);
        $gatewayName = 'stripe_checkout_session_with_sca';
        $gatewayConfig->getGatewayName()->willReturn($gatewayName);

        $payum->getGateway($gatewayName)->willReturn($gateway);

        $payum->getTokenFactory()->willReturn($tokenFactory);
        $tokenFactory->createToken($gatewayName, $payment, 'payum_notify_do')->willReturn($token);

        $request->beConstructedWith([$token]);
        $cancelRequestFactory->createNewWithToken($token)->willReturn($request);

        $gateway->execute($request)->shouldBeCalled();

        $this->__invoke($payment, $event);
    }

    public function it_do_nothing_when_gateway_is_unknown(
        PaymentInterface $payment,
        TransitionEvent $event,
        PaymentMethodInterface $paymentMethod,
        GatewayConfigInterface $gatewayConfig
    ): void {
        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getConfig()->willReturn(['factory' => 'foo']);

        $this->__invoke($payment, $event);
    }
}
