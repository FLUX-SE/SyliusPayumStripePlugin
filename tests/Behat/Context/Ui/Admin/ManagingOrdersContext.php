<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeCheckoutSessionMocker;
use Webmozart\Assert\Assert;

class ManagingOrdersContext implements Context
{
    public function __construct(
        private readonly StateMachineInterface $stateMachine,
        private readonly ObjectManager $objectManager,
        private readonly StripeCheckoutSessionMocker $stripeCheckoutSessionMocker,
    ) {
    }

    /**
     * @Given /^(this order) is already paid as "([^"]+)" Stripe payment intent$/
     */
    public function thisOrderIsAlreadyPaid(OrderInterface $order, string $stripePaymentIntentId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = [
            'object' => PaymentIntent::OBJECT_NAME,
            'id' => $stripePaymentIntentId,
            'status' => PaymentIntent::STATUS_SUCCEEDED,
            'capture_method' => PaymentIntent::CAPTURE_METHOD_AUTOMATIC,
        ];
        $payment->setDetails($details);

        $this->applyTransitionToState($payment, PaymentInterface::STATE_COMPLETED);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is already authorized as "([^"]+)" Stripe payment intent$/
     */
    public function thisOrderIsAlreadyAuthorized(OrderInterface $order, string $stripePaymentIntentId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = [
            'object' => PaymentIntent::OBJECT_NAME,
            'id' => $stripePaymentIntentId,
            'status' => PaymentIntent::STATUS_REQUIRES_CAPTURE,
            'capture_method' => PaymentIntent::CAPTURE_METHOD_MANUAL,
        ];
        $payment->setDetails($details);

        $this->applyTransitionToState($payment, PaymentInterface::STATE_AUTHORIZED);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is not yet paid as "([^"]+)" Stripe Checkout Session$/
     */
    public function thisOrderIsNotYetPaidStripeCheckoutSession(OrderInterface $order, string $stripeCheckoutSessionId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $payment->setDetails([
            'object' => Session::OBJECT_NAME,
            'id' => $stripeCheckoutSessionId,
            'status' => Session::STATUS_OPEN,
            'payment_status' => Session::PAYMENT_STATUS_UNPAID,
        ]);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is not yet paid as "([^"]+)" Stripe JS$/
     */
    public function thisOrderIsNotYetPaidStripeJs(OrderInterface $order, string $stripePaymentIntentId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $payment->setDetails([
            'object' => PaymentIntent::OBJECT_NAME,
            'id' => $stripePaymentIntentId,
            'status' => PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
        ]);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) payment has been canceled$/
     */
    public function thisOrderPaymentHasBeenCancelled(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $this->applyTransitionToState($payment, PaymentInterface::STATE_CANCELLED);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) payment has been expired$/
     */
    public function thisOrderPaymentHasBeenExpired(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = [
            'expires_at' => 42,
        ];

        $payment->setDetails($details);

        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $stateMachine->apply(PaymentTransitions::TRANSITION_CANCEL);

        $this->objectManager->flush();
    }

    /**
     * @Given /^I am prepared to cancel (this order)$/
     */
    public function iAmPreparedToCancelThisOrder(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = $payment->getDetails();
        /** @var string $status */
        $status = $details['status'] ?? PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD;
        /** @var string $captureMethod */
        $captureMethod = $details['capture_method'] ?? PaymentIntent::CAPTURE_METHOD_AUTOMATIC;

        $this->stripeCheckoutSessionMocker->mockCancelPayment($status, $captureMethod);
    }

    /**
     * @Given I am prepared to expire the checkout session on this order
     */
    public function iAmPreparedToExpireTheCheckoutSessionOnThisOrder(): void
    {
        $this->stripeCheckoutSessionMocker->mockExpirePayment();
    }

    /**
     * @Given I am prepared to cancel the payment intent on this order
     */
    public function iAmPreparedToExpireThePaymentIntentOnThisOrder(): void
    {
        $this->stripeCheckoutSessionMocker->mockCancelPayment(
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
            PaymentIntent::CAPTURE_METHOD_AUTOMATIC,
        );
    }

    /**
     * @Given I am prepared to refund this order
     */
    public function iAmPreparedToRefundThisOrder(): void
    {
        $this->stripeCheckoutSessionMocker->mockRefundPayment();
    }

    /**
     * @Given /^I am prepared to capture authorization of (this order)$/
     */
    public function iAmPreparedToCaptureAuthorizationOfThisOrder(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = $payment->getDetails();
        /** @var string $status */
        $status = $details['status'] ?? PaymentIntent::STATUS_REQUIRES_CAPTURE;
        /** @var string $captureMethod */
        $captureMethod = $details['capture_method'] ?? PaymentIntent::CAPTURE_METHOD_MANUAL;

        $this->stripeCheckoutSessionMocker->mockCaptureAuthorization($status, $captureMethod);
    }

    private function applyTransitionToState(PaymentInterface $payment, string $state): void
    {
        $transition = $this->stateMachine->getTransitionToState(
            $payment,
            PaymentTransitions::GRAPH,
            $state,
        );

        Assert::notNull($transition, 'Transition cannot be null at this point.');

        $this->stateMachine->apply(
            $payment,
            PaymentTransitions::GRAPH,
            $transition,
        );
    }
}
