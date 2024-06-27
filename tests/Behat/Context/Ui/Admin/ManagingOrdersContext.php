<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeCheckoutSessionMocker;

class ManagingOrdersContext implements Context
{
    private FactoryInterface $stateMachineFactory;

    private ObjectManager $objectManager;

    private StripeCheckoutSessionMocker $stripeCheckoutSessionMocker;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        ObjectManager $objectManager,
        StripeCheckoutSessionMocker $stripeCheckoutSessionMocker
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->objectManager = $objectManager;
        $this->stripeCheckoutSessionMocker = $stripeCheckoutSessionMocker;
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

        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE);

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

        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
        $stateMachine->apply(PaymentTransitions::TRANSITION_AUTHORIZE);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is not yet paid as "([^"]+)" Stripe Checkout Session$/
     */
    public function thisOrderIsNotYetPaidStripeCheckoutSession(OrderInterface $order, string $stripeCheckoutSessionId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = [
            'object' => Session::OBJECT_NAME,
            'id' => $stripeCheckoutSessionId,
            'status' => Session::STATUS_OPEN,
            'payment_status' => Session::PAYMENT_STATUS_UNPAID,
        ];
        $payment->setDetails($details);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is not yet paid as "([^"]+)" Stripe JS$/
     */
    public function thisOrderIsNotYetPaidStripeJs(OrderInterface $order, string $stripePaymentIntentId): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

        $details = [
            'object' => PaymentIntent::OBJECT_NAME,
            'id' => $stripePaymentIntentId,
            'status' => PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
        ];
        $payment->setDetails($details);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) payment has been canceled$/
     */
    public function thisOrderPaymentHasBeenCancelled(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();

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
        $status = $details['status'] ?? PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD;
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
            PaymentIntent::CAPTURE_METHOD_AUTOMATIC
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
        $status = $details['status'] ?? PaymentIntent::STATUS_REQUIRES_CAPTURE;
        $captureMethod = $details['capture_method'] ?? PaymentIntent::CAPTURE_METHOD_MANUAL;

        $this->stripeCheckoutSessionMocker->mockCaptureAuthorization($status, $captureMethod);
    }
}
