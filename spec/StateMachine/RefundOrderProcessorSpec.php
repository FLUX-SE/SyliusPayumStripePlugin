<?php

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\RefundRequestFactoryInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

class RefundOrderProcessorSpec extends ObjectBehavior
{
    public function let(
        RefundRequestFactoryInterface $refundRequestFactory,
        Payum $payum
    ): void {
        $this->beConstructedWith($refundRequestFactory, $payum);
    }

    public function it_is_invokable(
        Payum $payum,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        GatewayConfigInterface $gatewayConfig,
        GatewayInterface $gateway,
        TokenFactoryInterface $tokenFactory,
        TokenInterface $token,
        RefundRequestFactoryInterface $refundRequestFactory,
        ModelAggregateInterface $request
    ): void {

        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getConfig()->willReturn(['factory'=>'stripe_checkout_session']);
        $gatewayName = 'stripe_checkout_session_with_sca';
        $gatewayConfig->getGatewayName()->willReturn($gatewayName);

        $payum->getGateway($gatewayName)->willReturn($gateway);

        $payum->getTokenFactory()->willReturn($tokenFactory);
        $tokenFactory->createToken($gatewayName, $payment, 'sylius_shop_order_after_pay')->willReturn($token);

        $request->beConstructedWith([$token]);
        $refundRequestFactory->createNewWithToken($token)->willReturn($request);

        $gateway->execute($request)->shouldBeCalled();

        $this->__invoke($payment);
    }

    public function it_do_nothing_when_it_is_not_a_completed_state(
        PaymentInterface $payment
    ):void {
        $payment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $this->__invoke($payment);
    }
}
