<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Extension;

use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use SM\Factory\FactoryInterface;
use SM\SMException;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Webmozart\Assert\Assert;

/**
 * Reproduction of the Payum Core StorageExtension behaviour for Sylius payments
 *
 * @see \Payum\Core\Extension\StorageExtension
 */
final class UpdatePaymentStateExtension implements ExtensionInterface
{
    /** @var FactoryInterface */
    private $factory;

    /** @var StorageInterface */
    private $storage;

    /** @var GetStatusFactoryInterface */
    private $getStatusRequestFactory;

    /** @var PaymentInterface[] */
    private $scheduledPaymentsToProcess = [];

    public function __construct(
        FactoryInterface $factory,
        StorageInterface $storage,
        GetStatusFactoryInterface $getStatusRequestFactory
    ) {
        $this->factory = $factory;
        $this->storage = $storage;
        $this->getStatusRequestFactory = $getStatusRequestFactory;
    }

    public function onPreExecute(Context $context): void
    {
        /** @var mixed|ModelAggregateInterface $request */
        $request = $context->getRequest();

        if (false === $request instanceof ModelAggregateInterface) {
            return;
        }

        if ($request->getModel() instanceof IdentityInterface) {
            $payment = $this->storage->find($request->getModel());
        } else {
            /** @var PaymentInterface|mixed $payment */
            $payment = $request->getModel();
        }

        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $this->scheduleForProcessingIfSupported($payment);
    }

    public function onExecute(Context $context): void
    {
    }

    /**
     * @throws SMException
     */
    public function onPostExecute(Context $context): void
    {
        if (null !== $context->getException()) {
            return;
        }

        /** @var mixed|ModelAggregateInterface $request */
        $request = $context->getRequest();

        if ($request instanceof ModelAggregateInterface) {
            /** @var PaymentInterface|mixed $payment */
            $payment = $request->getModel();
            if ($payment instanceof PaymentInterface) {
                $this->scheduleForProcessingIfSupported($payment);
            }
        }

        if (count($context->getPrevious()) > 0) {
            return;
        }

        // Process scheduled payments only when we are post executing a
        // root payum request
        foreach ($this->scheduledPaymentsToProcess as $id => $payment) {
            $this->processPayment($context, $payment);
            unset($this->scheduledPaymentsToProcess[$id]);
        }
    }

    /**
     * @throws SMException
     */
    private function processPayment(Context $context, PaymentInterface $payment): void
    {
        $status = $this->getStatusRequestFactory->createNewWithModel($payment);
        $context->getGateway()->execute($status);
        $value = (string) $status->getValue();
        if ($payment->getState() === $value) {
            return;
        }

        if (PaymentInterface::STATE_UNKNOWN === $value) {
            return;
        }

        $this->updatePaymentState($payment, $value);
    }

    /**
     * @throws SMException
     */
    private function updatePaymentState(PaymentInterface $payment, string $nextState): void
    {
        $stateMachine = $this->factory->get($payment, PaymentTransitions::GRAPH);

        Assert::isInstanceOf($stateMachine, StateMachineInterface::class);

        $transition = $stateMachine->getTransitionToState($nextState);
        if (null === $transition) {
            return;
        }

        $stateMachine->apply($transition);
    }

    private function scheduleForProcessingIfSupported(PaymentInterface $payment): void
    {
        $id = $payment->getId();
        if (null === $id) {
            return;
        }

        if (false === is_int($id)) {
            return;
        }

        $this->scheduledPaymentsToProcess[$id] = $payment;
    }
}
