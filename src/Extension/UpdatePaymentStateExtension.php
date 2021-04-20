<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Extension;

use ArrayAccess;
use FluxSE\PayumStripe\Action\Api\WebhookEvent\AbstractPaymentAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\Request\Notify;
use Payum\Core\Security\TokenInterface;
use SM\Factory\FactoryInterface;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Webmozart\Assert\Assert;

final class UpdatePaymentStateExtension implements ExtensionInterface
{
    /** @var FactoryInterface */
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function onPreExecute(Context $context): void
    {
    }

    public function onExecute(Context $context): void
    {
    }

    public function onPostExecute(Context $context): void
    {
        if (null !== $context->getException()) {
            return;
        }

        $request = $context->getRequest();

        if (false === $request instanceof Notify) {
            return;
        }

        $token = $request->getToken();
        if (false === $token instanceof TokenInterface) {
            return;
        }

        $webhookPayment = false;
        foreach ($context->getPrevious() as $prevContext) {
            if ($prevContext->getAction() instanceof AbstractPaymentAction) {
                $webhookPayment = true;
                break;
            }
        }

        if (false === $webhookPayment) {
            return;
        }

        $details = $request->getModel();
        if (!$details instanceof ArrayObject) {
            return;
        }

        $payment = $request->getFirstModel();
        if (false === $payment instanceof PaymentInterface) {
            return;
        }

        $payment->setDetails($details->getArrayCopy());

        $context->getGateway()->execute($status = new GetStatus($payment));
        /** @var string $value */
        $value = $status->getValue();
        if ($payment->getState() !== $value && PaymentInterface::STATE_UNKNOWN !== $value) {
            $this->updatePaymentState($payment, $value);
        }
    }

    private function updatePaymentState(PaymentInterface $payment, string $nextState): void
    {
        $stateMachine = $this->factory->get($payment, PaymentTransitions::GRAPH);

        Assert::isInstanceOf($stateMachine, StateMachineInterface::class);

        if (null !== $transition = $stateMachine->getTransitionToState($nextState)) {
            $stateMachine->apply($transition);
        }
    }
}