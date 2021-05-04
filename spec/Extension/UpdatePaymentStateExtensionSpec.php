<?php

namespace spec\FluxSE\SyliusPayumStripePlugin\Extension;

use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use SM\Factory\FactoryInterface;
use SM\SMException;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

class UpdatePaymentStateExtensionSpec extends ObjectBehavior
{
    public function let(
        FactoryInterface $factory,
        StorageInterface $storage,
        GetStatusFactoryInterface $getStatusRequestFactory
    ): void {
        $this->beConstructedWith($factory, $storage, $getStatusRequestFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ExtensionInterface::class);
    }

    /**
     * @param Context|Collaborator $context
     * @param ModelAggregateInterface|Collaborator $request
     * @param IdentityInterface|Collaborator $model
     * @param StorageInterface|Collaborator $storage
     * @param PaymentInterface|Collaborator $payment
     */
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

    /**
     * @param Context|Collaborator $context
     * @param ModelAggregateInterface|Collaborator $request
     * @param PaymentInterface|Collaborator $model
     */
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

    public function it_onExecute_does_nothing(Context $context): void
    {
        $this->onExecute($context);
    }

    /**
     * @param Context|Collaborator $context
     * @param ModelAggregateInterface|Collaborator $request
     * @param PaymentInterface|Collaborator $payment
     * @param GetStatusInterface|Collaborator $status
     * @param GetStatusFactoryInterface|Collaborator $getStatusRequestFactory
     * @param GatewayInterface|Collaborator $gateway
     * @param FactoryInterface|Collaborator $factory
     * @param StateMachineInterface|Collaborator $stateMachine
     *
     * @throws SMException
     */
    public function it_OnPostExecute_apply_a_transition(
        Context $context,
        ModelAggregateInterface $request,
        PaymentInterface $payment,
        GetStatusInterface $status,
        GetStatusFactoryInterface $getStatusRequestFactory,
        GatewayInterface $gateway,
        FactoryInterface $factory,
        StateMachineInterface $stateMachine
    ): void {
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


        $factory->get($payment, PaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->getTransitionToState(PaymentInterface::STATE_COMPLETED)->willReturn('complete');
        $stateMachine->apply('complete')->shouldBeCalled();

        $this->onPostExecute($context);
    }
}
