<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Behat\Service\Mocker\MockerInterface;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api\CheckoutSessionMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api\PaymentIntentMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api\RefundMocker;

final class StripeCheckoutSessionMocker
{
    private MockerInterface $mocker;

    private CheckoutSessionMocker $checkoutSessionMocker;

    private PaymentIntentMocker $paymentIntentMocker;

    private RefundMocker $refundMocker;

    public function __construct(
        MockerInterface $mocker,
        CheckoutSessionMocker $checkoutSessionMocker,
        PaymentIntentMocker $paymentIntentMocker,
        RefundMocker $refundMocker
    ) {
        $this->mocker = $mocker;
        $this->checkoutSessionMocker = $checkoutSessionMocker;
        $this->paymentIntentMocker = $paymentIntentMocker;
        $this->refundMocker = $refundMocker;
    }

    public function mockCaptureOrAuthorize(callable $action): void
    {
        $this->checkoutSessionMocker->mockCreateAction();

        $this->mockSessionSync(
            $action,
            Session::STATUS_OPEN,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD
        );
    }

    public function mockCancelPayment(string $status, string $captureMethod): void
    {
        $this->mocker->unmockAll();

        $this->paymentIntentMocker->mockUpdateAction($status, $captureMethod);
        $this->paymentIntentMocker->mockCancelAction($captureMethod);
        $this->paymentIntentMocker->mockRetrieveAction(PaymentIntent::STATUS_CANCELED);
    }

    public function mockRefundPayment(): void
    {
        $this->mocker->unmockAll();

        $this->refundMocker->mockCreateAction();
    }

    public function mockExpirePayment(): void
    {
        $this->mocker->unmockAll();

        $this->checkoutSessionMocker->mockExpireAction();
        $this->checkoutSessionMocker->mockRetrieveAction(Session::STATUS_EXPIRED, Session::PAYMENT_STATUS_UNPAID);
        $this->paymentIntentMocker->mockRetrieveAction(PaymentIntent::STATUS_CANCELED);
    }

    public function mockCaptureAuthorization(string $status, string $captureMethod): void
    {
        $this->mocker->unmockAll();

        $this->paymentIntentMocker->mockUpdateAction($status, $captureMethod);
        $this->paymentIntentMocker->mockCaptureAction(PaymentIntent::STATUS_SUCCEEDED);
        $this->paymentIntentMocker->mockRetrieveAction(PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockGoBackPayment(callable $action): void
    {
        $this->mockExpireSession(Session::STATUS_OPEN);
        $this->mockSessionSync(
            $action,
            Session::STATUS_OPEN,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD
        );
    }

    public function mockSuccessfulPayment(callable $notifyAction, callable $action): void
    {
        $this->mockSessionSync(
            $notifyAction,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_PAID,
            PaymentIntent::STATUS_SUCCEEDED
        );
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockAuthorizePayment(callable $notifyAction, callable $action): void
    {
        $this->mockSessionSync(
            $notifyAction,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_CAPTURE
        );
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_CAPTURE);
    }

    public function mockSuccessfulPaymentWithoutWebhook(callable $action): void
    {
        $this->mockSessionSync(
            $action,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_PAID,
            PaymentIntent::STATUS_SUCCEEDED
        );
    }

    public function mockSuccessfulPaymentWithoutWebhookUsingAuthorize(callable $action): void
    {
        $this->mockSessionSync(
            $action,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_CAPTURE
        );
    }

    public function mockPaymentIntentSync(callable $action, string $status): void
    {
        $this->paymentIntentMocker->mockRetrieveAction($status);

        $action();

        $this->mocker->unmockAll();
    }

    public function mockSessionSync(
        callable $action,
        string $sessionStatus,
        string $paymentStatus,
        string $paymentIntentStatus
    ): void {
        $this->checkoutSessionMocker->mockRetrieveAction($sessionStatus, $paymentStatus);
        $this->mockPaymentIntentSync($action, $paymentIntentStatus);
    }

    public function mockExpireSession(string $sessionStatus): void
    {
        $this->checkoutSessionMocker->mockAllAction($sessionStatus);
        $this->checkoutSessionMocker->mockExpireAction();
    }
}
