<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use Stripe\PaymentIntent;
use Sylius\Behat\Service\Mocker\MockerInterface;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api\PaymentIntentMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api\RefundMocker;

final class StripeJsMocker
{
    private MockerInterface $mocker;

    private PaymentIntentMocker $paymentIntentMocker;

    private RefundMocker $refundMocker;

    public function __construct(
        MockerInterface $mocker,
        PaymentIntentMocker $paymentIntentMocker,
        RefundMocker $refundMocker,
    ) {
        $this->mocker = $mocker;
        $this->paymentIntentMocker = $paymentIntentMocker;
        $this->refundMocker = $refundMocker;
    }

    public function mockCaptureOrAuthorize(callable $action): void
    {
        $this->mocker->unmockAll();

        $this->paymentIntentMocker->mockCreateAction();
        $this->mockPaymentIntentSync(
            $action,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
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

    public function mockCaptureAuthorization(string $status, string $captureMethod): void
    {
        $this->mocker->unmockAll();

        $this->paymentIntentMocker->mockUpdateAction($status, $captureMethod);
        $this->paymentIntentMocker->mockCaptureAction(PaymentIntent::STATUS_SUCCEEDED);
        $this->paymentIntentMocker->mockRetrieveAction(PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockGoBackPayment(callable $action): void
    {
        $this->mockPaymentIntentSync(
            $action,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD,
        );
    }

    public function mockSuccessfulPayment(callable $notifyAction, callable $action): void
    {
        $this->mockPaymentIntentSync(
            $notifyAction,
            PaymentIntent::STATUS_SUCCEEDED,
        );
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockAuthorizePayment(callable $notifyAction, callable $action): void
    {
        $this->mockPaymentIntentSync(
            $notifyAction,
            PaymentIntent::STATUS_REQUIRES_CAPTURE,
        );
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_CAPTURE);
    }

    public function mockSuccessfulPaymentWithoutWebhook(callable $action): void
    {
        $this->mockPaymentIntentSync(
            $action,
            PaymentIntent::STATUS_SUCCEEDED,
        );
    }

    public function mockSuccessfulPaymentWithoutWebhookUsingAuthorize(callable $action): void
    {
        $this->mockPaymentIntentSync(
            $action,
            PaymentIntent::STATUS_REQUIRES_CAPTURE,
        );
    }

    public function mockPaymentIntentSync(callable $action, string $status): void
    {
        $this->paymentIntentMocker->mockRetrieveAction($status);

        $action();

        $this->mocker->unmockAll();
    }
}
