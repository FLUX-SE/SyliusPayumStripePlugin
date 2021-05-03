<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Request\Api\Resource\CreateSession;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use Payum\Core\Bridge\Spl\ArrayObject;
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
        $model = [
            'id' => 'sess_1',
            'object' => Session::OBJECT_NAME,
            'payment_intent' => 'pi_1',
        ];

        $mock = $this->mocker->mockService('tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.create_session', AbstractCreateAction::class);

        $mock
            ->shouldReceive('setApi')
            ->once()
        ;
        $mock
            ->shouldReceive('setGateway')
            ->once()
        ;

        $mock
            ->shouldReceive('supports')
            ->andReturnUsing(function ($request) {
                return $request instanceof CreateSession;
            })
        ;

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreateSession $request) use ($model) {
                $rModel = $request->getModel();
                $session = Session::constructFrom(array_merge($model, $rModel->getArrayCopy()));
                $request->setApiResource($session);
            })
        ;

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

    public function mockSuccessfulPaymentWithoutWebhooks(
        callable $action
    ): void {
        $this->mockPaymentIntentSync($action, PaymentIntent::STATUS_SUCCEEDED);
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
        $model = [
            'id' => 'pi_1',
            'object' => PaymentIntent::OBJECT_NAME,
            'status' => $status,
        ];

        $mock = $this->mocker->mockService('tests.flux_se.sylius_payum_stripe_checkout_session_plugin.behat.mocker.action.retrieve_payment_intent', AbstractRetrieveAction::class);

        $mock
            ->shouldReceive('setApi')
            ->once()
        ;
        $mock
            ->shouldReceive('setGateway')
            ->once()
        ;

        $mock
            ->shouldReceive('supports')
            ->andReturnUsing(function ($request) use ($model) {
                return $request instanceof RetrievePaymentIntent;
            })
        ;

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (RetrievePaymentIntent $request) use ($model) {
                $request->setApiResource(PaymentIntent::constructFrom($model));
            })
        ;

        $action();

        $this->mocker->unmockAll();
    }
}
