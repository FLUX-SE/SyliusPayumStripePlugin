<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Extension;

use FluxSE\PayumStripe\Request\Api\Resource\AllInterface;
use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\AllSessionRequestFactoryInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\ExpireSessionRequestFactoryInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use Stripe\Checkout\Session;
use Stripe\Collection;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelExistingPaymentIntentExtensionSpec extends ObjectBehavior
{
    public function let(
        ExpireSessionRequestFactoryInterface $expireSessionRequestFactory,
        AllSessionRequestFactoryInterface $allSessionRequestFactory
    ): void {
        $this->beConstructedWith(
            $expireSessionRequestFactory,
            $allSessionRequestFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ExtensionInterface::class);
    }

    public function it_does_nothing_when_action_is_not_the_convert_payment_action_targeted(
        Context $context,
        ActionInterface $action
    ): void {
        $context->getAction()->willReturn($action);

        $this->onExecute($context);
    }

    public function it_does_nothing_when_payment_details_are_empty(
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

    public function it_does_nothing_when_payment_details_are_something_else_than_a_payment_intent(
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

    public function it_doesnt_found_the_related_session_and_do_nothing(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment,
        GatewayInterface $gateway,
        AllSessionRequestFactoryInterface $allSessionRequestFactory,
        AllInterface $allSessionRequest
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

        $allSessionRequestFactory
            ->createNew()
            ->willReturn($allSessionRequest);

        $allSessionRequest->setParameters([
            'payment_intent' => $piId
        ])->shouldBeCalled();
        $allSessionRequest->getApiResources()->willReturn(Collection::constructFrom(['data'=>[]]));

        $gateway->execute($allSessionRequest)->shouldBeCalled();

        $this->onExecute($context);
    }

    public function it_founds_a_related_expired_session_and_does_nothing(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment,
        GatewayInterface $gateway,
        AllSessionRequestFactoryInterface $allSessionRequestFactory,
        AllInterface $allSessionRequest
    ): void {
        $context->getAction()->willReturn($action);
        $context->getRequest()->willReturn($request);
        $request->getSource()->willReturn($payment);
        $context->getGateway()->willReturn($gateway);

        $piId = 'pi_test_0000000000000000000';
        $csId = 'cs_test_0000000000000000000';
        $payment->getDetails()->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);

        $allSessionRequestFactory
            ->createNew()
            ->willReturn($allSessionRequest);

        $allSessionRequest->setParameters([
            'payment_intent' => $piId
        ])->shouldBeCalled();
        $allSessionRequest->getApiResources()->willReturn(Collection::constructFrom([
            'data'=>[
                [
                    'id' => $csId,
                    'status' => Session::STATUS_EXPIRED,
                ]
            ]
        ]));

        $gateway->execute($allSessionRequest)->shouldBeCalled();

        $this->onExecute($context);
    }

    public function it_founds_a_related_session_and_expires_it(
        Context $context,
        ConvertPaymentActionInterface $action,
        Convert $request,
        PaymentInterface $payment,
        GatewayInterface $gateway,
        AllSessionRequestFactoryInterface $allSessionRequestFactory,
        AllInterface $allSessionRequest,
        ExpireSessionRequestFactoryInterface $expireSessionRequestFactory,
        CustomCallInterface $expireSessionRequest
    ): void {
        $context->getAction()->willReturn($action);
        $context->getRequest()->willReturn($request);
        $request->getSource()->willReturn($payment);
        $context->getGateway()->willReturn($gateway);

        $piId = 'pi_test_0000000000000000000';
        $csId = 'cs_test_0000000000000000000';
        $payment->getDetails()->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);

        $allSessionRequestFactory
            ->createNew()
            ->willReturn($allSessionRequest);

        $allSessionRequest->setParameters([
            'payment_intent' => $piId
        ])->shouldBeCalled();
        $allSessionRequest->getApiResources()->willReturn(Collection::constructFrom([
            'data'=>[
                [
                    'id' => $csId,
                    'status' => Session::STATUS_OPEN,
                ]
            ]
        ]));

        $gateway->execute($allSessionRequest)->shouldBeCalled();

        $expireSessionRequest->beConstructedWith([$csId]);

        $expireSessionRequestFactory
            ->createNew($csId)
            ->willReturn($expireSessionRequest);

        $gateway->execute($expireSessionRequest)->shouldBeCalled();

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
