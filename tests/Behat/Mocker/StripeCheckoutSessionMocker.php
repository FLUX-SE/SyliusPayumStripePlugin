<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Request\Api\Resource\CreateSession;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class StripeCheckoutSessionMocker
{
    /** @var MockerInterface */
    private $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockCreatePayment(callable $action): void
    {
        $this->mockCreateSession();

        $this->mockPaymentIntentRequiresPaymentMethodStatus($action);

        $this->mocker->unmockAll();
    }

    public function mockGoBackPayment(
        callable $action
    ): void {
        $this->mockPaymentIntentRequiresPaymentMethodStatus($action);
    }

    public function mockSuccessfulPayment(
        callable $notifyAction,
        callable $action
    ): void {
        $this->mockPaymentIntentSync($notifyAction, PaymentIntent::STATUS_SUCCEEDED);
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockAuthorizePayment(
        callable $notifyAction,
        callable $action
    ): void {
        $this->mockPaymentIntentSync($notifyAction, PaymentIntent::STATUS_REQUIRES_CAPTURE);
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_CAPTURE);
    }

    public function mockSuccessfulPaymentWithoutWebhook(
        callable $action
    ): void {
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockSuccessfulPaymentWithoutWebhookUsingAuthorize(
        callable $action
    ): void {
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_CAPTURE);
    }

    /**
     * @see https://stripe.com/docs/payments/intents#payment-intent
     */
    public function mockPaymentIntentRequiresPaymentMethodStatus(callable $action): void
    {
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD);
    }

    public function mockPaymentIntentSync(callable $action, string $status): void
    {
        $this->mockRetrievePaymentIntent($status);

        $action();

        $this->mocker->unmockAll();
    }

    private function mockCreateSession(): void
    {
        $mockCreateSession = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.create_session',
            AbstractCreateAction::class
        );

        $mockCreateSession
            ->shouldReceive('setApi')
            ->once();
        $mockCreateSession
            ->shouldReceive('setGateway')
            ->once();

        $mockCreateSession
            ->shouldReceive('supports')
            ->andReturnUsing(function ($request) {
                return $request instanceof CreateSession;
            });

        $mockCreateSession
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreateSession $request) {
                $rModel = $request->getModel();
                $session = Session::constructFrom(array_merge([
                    'id' => 'sess_1',
                    'object' => Session::OBJECT_NAME,
                    'payment_intent' => 'pi_1',
                ], $rModel->getArrayCopy()));
                $request->setApiResource($session);
            });
    }

    private function mockRetrievePaymentIntent(string $status): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.retrieve_payment_intent',
            AbstractRetrieveAction::class
        );

        $mock
            ->shouldReceive('setApi')
            ->once();
        $mock
            ->shouldReceive('setGateway')
            ->once();

        $mock
            ->shouldReceive('supports')
            ->andReturnUsing(function ($request) {
                return $request instanceof RetrievePaymentIntent;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (RetrievePaymentIntent $request) use ($status) {
                $request->setApiResource(PaymentIntent::constructFrom([
                    'id' => 'pi_1',
                    'object' => PaymentIntent::OBJECT_NAME,
                    'status' => $status,
                ]));
            });
    }
}
