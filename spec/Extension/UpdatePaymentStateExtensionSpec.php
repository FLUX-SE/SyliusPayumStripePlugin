<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Extension;

use Exception;
use FluxSE\SyliusPayumStripePlugin\Abstraction\StateMachine\StateMachineInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Model\PaymentInterface as PayumPaymentInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class UpdatePaymentStateExtensionSpec extends ObjectBehavior
{
    public function let(
        StateMachineInterface $stateMachine,
        StorageInterface $storage,
        GetStatusFactoryInterface $getStatusRequestFactory
    ): void {
        $this->beConstructedWith($stateMachine, $storage, $getStatusRequestFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ExtensionInterface::class);
    }

    public function it_onPreExecute_with_Identity_finds_the_related_payment_and_stores_it(
        Context $context,
        ModelAggregateInterface $request,
        IdentityInterface $model,
        StorageInterface $storage,
        PaymentInterface $payment
    ): void {
        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($model);

        $storage->find($model)->willReturn($payment);
        $model->getId()->willReturn(1);

        $this->onPreExecute($context);
    }

    public function it_onPreExecute_with_Payment_stores_it(
        Context $context,
        ModelAggregateInterface $request,
        PaymentInterface $model
    ): void {
        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($model);
        $model->getId()->willReturn(1);

        $this->onPreExecute($context);
    }

    public function it_onPreExecute_without_Payment_or_Identify_does_nothing(
        Context $context,
        ModelAggregateInterface $request,
        PayumPaymentInterface $model
    ): void {
        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($model);

        $this->onPreExecute($context);
    }

    public function it_onPreExecute_without_ModelAggregateInterface_does_nothing(
        Context $context,
        TokenAggregateInterface $request
    ): void {
        $context->getRequest()->willReturn($request);

        $this->onPreExecute($context);
    }

    public function it_onExecute_does_nothing(Context $context): void
    {
        $this->onExecute($context);
    }

    public function it_OnPostExecute_apply_a_transition(
        Context $context,
        ModelAggregateInterface $request,
        PaymentInterface $payment,
        GetStatusInterface $status,
        GetStatusFactoryInterface $getStatusRequestFactory,
        GatewayInterface $gateway,
        StateMachineInterface $stateMachine
    ): void {
        $context->getException()->willReturn(null);
        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($payment);
        $payment->getId()->willReturn(1);

        $context->getPrevious()->willReturn([]);

        $context->getGateway()->willReturn($gateway);
        $status->beConstructedWith([$payment]);
        $getStatusRequestFactory->createNewWithModel($payment)->willReturn($status);

        $gateway->execute($status)->shouldBeCalled();
        $payment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $status->getValue()->willReturn(PaymentInterface::STATE_COMPLETED);

        $stateMachine->getTransitionToState(
            $payment,
            PaymentTransitions::GRAPH,
            PaymentInterface::STATE_COMPLETED
        )->willReturn('complete');
        $stateMachine->apply(
            $payment,
            PaymentTransitions::GRAPH,
            'complete'
        )->shouldBeCalled();

        $this->onPostExecute($context);
    }

    public function it_OnPostExecute_apply_a_transition_without_a_Sylius_PaymentInterface_when_there_was_previously_stored_payment(
        Context $previousContext,
        ModelAggregateInterface $previousRequest,
        PaymentInterface $previousPayment,
        Context $context,
        ModelAggregateInterface $request,
        PayumPaymentInterface $payment,
        GetStatusInterface $status,
        GetStatusFactoryInterface $getStatusRequestFactory,
        GatewayInterface $gateway,
        StateMachineInterface $stateMachine
    ): void {
        $context->getException()->willReturn(null);
        $previousContext->getRequest()->willReturn($previousRequest);
        $previousRequest->getModel()->willReturn($previousPayment);
        $previousPayment->getId()->willReturn(1);

        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($payment);

        $context->getPrevious()->willReturn([]);

        $context->getGateway()->willReturn($gateway);
        $status->beConstructedWith([$previousPayment]);
        $getStatusRequestFactory->createNewWithModel($previousPayment)->willReturn($status);

        $gateway->execute($status)->shouldBeCalled();
        $previousPayment->getState()->willReturn(PaymentInterface::STATE_NEW);
        $status->getValue()->willReturn(PaymentInterface::STATE_COMPLETED);

        $stateMachine->getTransitionToState(
            $previousPayment,
            PaymentTransitions::GRAPH,
            PaymentInterface::STATE_COMPLETED
        )->willReturn('complete');
        $stateMachine->apply(
            $previousPayment,
            PaymentTransitions::GRAPH,
            'complete'
        )->shouldBeCalled();

        $this->onPreExecute($previousContext);

        $this->onPostExecute($context);
    }

    public function it_OnPostExecute_without_ModelAggregateInterface_does_nothing_if_there_is_previous_context(
        Context $context,
        TokenAggregateInterface $request
    ): void {
        $context->getException()->willReturn(null);
        $context->getRequest()->willReturn($request);

        $context->getPrevious()->willReturn([1]);

        $this->onPostExecute($context);
    }

    public function it_OnPostExecute_without_ModelAggregateInterface_does_nothing_if_there_is_no_previous_context(
        Context $context,
        TokenAggregateInterface $request
    ): void {
        $context->getException()->willReturn(null);
        $context->getRequest()->willReturn($request);

        $context->getPrevious()->willReturn([]);

        $this->onPostExecute($context);
    }

    public function it_OnPostExecute_with_ModelAggregateInterface_does_nothing_if_it_is_not_a_sylius_PaymentInterface(
        Context $context,
        ModelAggregateInterface $request,
        PayumPaymentInterface $model
    ): void {
        $context->getException()->willReturn(null);
        $context->getRequest()->willReturn($request);
        $request->getModel()->willReturn($model);

        $context->getPrevious()->willReturn([]);

        $this->onPostExecute($context);
    }

    public function it_onPostExecute_with_exception_does_nothing(
        Context $context,
        TokenAggregateInterface $request
    ): void {
        $exception = new Exception();
        $context->getException()->willReturn($exception);

        $this->onPostExecute($context);
    }
}
