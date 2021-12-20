<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Extension;

use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\CancelPaymentIntentRequestFactoryInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelExistingPaymentIntentExtensionSpec extends ObjectBehavior
{
    public function let(
        CancelPaymentIntentRequestFactoryInterface $cancelPaymentIntentRequestFactory
    ): void {
        $this->beConstructedWith($cancelPaymentIntentRequestFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ExtensionInterface::class);
    }

    public function it_do_nothing_when_action_is_not_the_convert_payment_action_targeted(
        Context $context,
        ActionInterface $action
    ): void {
        $context->getAction()->willReturn($action);

        $this->onExecute($context);
    }

    public function it_do_nothing_when_payment_details_are_empty(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment
    ): void {
        $context->getAction()->willReturn($action);
        $context->getRequest()->willReturn($request);
        $request->getSource()->willReturn($payment);

        $payment->getDetails()->willReturn([]);

        $this->onExecute($context);
    }

    public function it_do_nothing_when_payment_details_are_something_else_than_a_payment_intent(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment
    ): void {
        $context->getAction()->willReturn($action);
        $context->getRequest()->willReturn($request);
        $request->getSource()->willReturn($payment);

        $payment->getDetails()->willReturn([
            'object' => SetupIntent::OBJECT_NAME,
        ]);

        $this->onExecute($context);
    }

    public function it_found_a_previous_payment_intent_and_cancel_it(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment,
        GatewayInterface $gateway,
        CancelPaymentIntentRequestFactoryInterface $cancelPaymentIntentRequestFactory,
        CustomCallInterface $cancelPaymentIntentRequest
    ): void {
        $context->getAction()->willReturn($action);
        $context->getRequest()->willReturn($request);
        $request->getSource()->willReturn($payment);
        $context->getGateway()->willReturn($gateway);

        $piId = 'pi_test_0000000000000000000';
        $payment->getDetails()->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);

        $cancelPaymentIntentRequest->beConstructedWith([$piId]);

        $cancelPaymentIntentRequestFactory
            ->createNew($piId)
            ->willReturn($cancelPaymentIntentRequest);

        $gateway->execute($cancelPaymentIntentRequest)->shouldBeCalled();

        $this->onExecute($context);
    }

    public function it_onPreExecute_does_nothing(Context $context): void
    {
        $this->onPreExecute($context);
    }

    public function it_onPostExecute_does_nothing(Context $context): void
    {
        $this->onPostExecute($context);
    }
}
