<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Extension;

use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

/**
 * Reproduction of the Payum Core StorageExtension behavior for Sylius payments
 *
 * @see \Payum\Core\Extension\StorageExtension
 */
final class UpdatePaymentStateExtension implements ExtensionInterface
{
    /** @var PaymentInterface[] */
    private array $scheduledPaymentsToProcess = [];

    public function __construct(
        private readonly StateMachineInterface $stateMachine,
        private readonly StorageInterface $storage,
        private readonly GetStatusFactoryInterface $getStatusRequestFactory,
    ) {
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

    private function processPayment(Context $context, PaymentInterface $payment): void
    {
        $status = $this->getStatusRequestFactory->createNewWithModel($payment);
        $context->getGateway()->execute($status);
        /** @var string $value */
        $value = $status->getValue();
        if ($payment->getState() === $value) {
            return;
        }

        if (PaymentInterface::STATE_UNKNOWN === $value) {
            return;
        }

        $this->updatePaymentState($payment, $value);
    }

    private function updatePaymentState(PaymentInterface $payment, string $nextState): void
    {
        $transition = $this->stateMachine->getTransitionToState(
            $payment,
            PaymentTransitions::GRAPH,
            $nextState,
        );
        if (null === $transition) {
            return;
        }

        $this->stateMachine->apply(
            $payment,
            PaymentTransitions::GRAPH,
            $transition,
        );
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
