<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractAllAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Request\Api\Resource\AllSession;
use FluxSE\PayumStripe\Request\Api\Resource\CreateSession;
use FluxSE\PayumStripe\Request\Api\Resource\ExpireSession;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\RetrieveSession;
use Stripe\Checkout\Session;
use Stripe\Collection;
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
        $this->mockCreateSessionAction();

        $this->mockSessionSync(
            $action,
            Session::STATUS_OPEN,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD
        );
    }

    public function mockGoBackPayment(
        callable $action
    ): void {
        $this->mockExpireSession(Session::STATUS_OPEN);
        $this->mockSessionSync(
            $action,
            Session::STATUS_OPEN,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_PAYMENT_METHOD
        );
    }

    public function mockSuccessfulPayment(
        callable $notifyAction,
        callable $action
    ): void {
        $this->mockSessionSync(
            $notifyAction,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_PAID,
            PaymentIntent::STATUS_SUCCEEDED
        );
        $this->mockPaymentIntentSync($action,PaymentIntent::STATUS_SUCCEEDED);
    }

    public function mockAuthorizePayment(
        callable $notifyAction,
        callable $action
    ): void {
        $this->mockSessionSync(
            $notifyAction,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_CAPTURE
        );
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_REQUIRES_CAPTURE);
    }

    public function mockSuccessfulPaymentWithoutWebhook(
        callable $action
    ): void {
        $this->mockSessionSync(
            $action,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_PAID,
            PaymentIntent::STATUS_SUCCEEDED
        );
    }

    public function mockSuccessfulPaymentWithoutWebhookUsingAuthorize(
        callable $action
    ): void {
        $this->mockSessionSync(
            $action,
            Session::STATUS_COMPLETE,
            Session::PAYMENT_STATUS_UNPAID,
            PaymentIntent::STATUS_REQUIRES_CAPTURE
        );
    }

    public function mockPaymentIntentSync(callable $action, string $status): void
    {
        $this->mockRetrievePaymentIntentAction($status);

        $action();

        $this->mocker->unmockAll();
    }

    public function mockSessionSync(
        callable $action,
        string $sessionStatus,
        string $paymentStatus,
        string $paymentIntentStatus
    ): void {
        $this->mockRetrieveSessionAction($sessionStatus, $paymentStatus);
        $this->mockPaymentIntentSync($action, $paymentIntentStatus);
    }

    public function mockExpireSession(string $sessionStatus): void
    {
        $this->mockAllSessionAction($sessionStatus);
        $this->mockExpireSessionAction();
    }

    private function mockCreateSessionAction(): void
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
                    'id' => 'cs_1',
                    'object' => Session::OBJECT_NAME,
                    'payment_intent' => 'pi_1',
                ], $rModel->getArrayCopy()));
                $request->setApiResource($session);
            });
    }

    private function mockRetrievePaymentIntentAction(string $status): void
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
                    'id' => $request->getId(),
                    'object' => PaymentIntent::OBJECT_NAME,
                    'status' => $status,
                ]));
            });
    }

    private function mockRetrieveSessionAction(string $status, string $paymentStatus): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.retrieve_session',
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
                return $request instanceof RetrieveSession;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (RetrieveSession $request) use ($status, $paymentStatus) {
                $request->setApiResource(Session::constructFrom([
                    'id' => $request->getId(),
                    'object' => Session::OBJECT_NAME,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_intent' => 'pi_1',
                ]));
            });
    }

    private function mockAllSessionAction(string $status): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.all_session',
            AbstractAllAction::class
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
                return $request instanceof AllSession;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (AllSession $request) use ($status) {
                $request->setApiResources(
                    Collection::constructFrom(['data' => [
                        [
                            'id' => 'cs_1',
                            'object' => Session::OBJECT_NAME,
                            'status' => $status,
                        ],
                    ]]));
            });
    }

    private function mockExpireSessionAction(): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.expire_session',
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
                return $request instanceof ExpireSession;
            });

        $mock
            ->shouldReceive('execute')
            ->once();
    }
}
