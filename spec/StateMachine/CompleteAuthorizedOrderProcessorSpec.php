<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\CaptureRequestFactoryInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class CompleteAuthorizedOrderProcessorSpec extends ObjectBehavior
{
    public function let(
        CaptureRequestFactoryInterface $captureRequestFactory,
        Payum $payum
    ): void {
        $this->beConstructedWith($captureRequestFactory, $payum);
    }

    public function it_is_invokable(
        Payum $payum,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        GatewayConfigInterface $gatewayConfig,
        GatewayInterface $gateway,
        TokenFactoryInterface $tokenFactory,
        TokenInterface $token,
        CaptureRequestFactoryInterface $captureRequestFactory,
        ModelAggregateInterface $request
    ): void {
        $payment->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $payment->getMethod()->willReturn($paymentMethod);
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfig->getConfig()->willReturn(['factory' => 'stripe_checkout_session']);
        $gatewayName = 'stripe_checkout_session_with_sca';
        $gatewayConfig->getGatewayName()->willReturn($gatewayName);

        $payum->getGateway($gatewayName)->willReturn($gateway);

        $payum->getTokenFactory()->willReturn($tokenFactory);
        $tokenFactory->createToken($gatewayName, $payment, 'sylius_shop_order_after_pay')->willReturn($token);

        $request->beConstructedWith([$token]);
        $captureRequestFactory->createNewWithToken($token)->willReturn($request);

        $gateway->execute($request)->shouldBeCalled();

        $this->__invoke($payment);
    }

    public function it_do_nothing_when_it_is_not_an_authorized_state(
        PaymentInterface $payment
    ): void {
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $this->__invoke($payment);
    }
}
