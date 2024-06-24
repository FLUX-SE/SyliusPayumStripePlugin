<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use ArrayObject;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractUpdateAction;
use FluxSE\PayumStripe\Request\Api\Resource\CancelPaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CapturePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CreatePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\UpdatePaymentIntent;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class PaymentIntentMocker
{
    private MockerInterface $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockCreateAction(): void
    {
        $mockCreatePaymentIntent = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.create_payment_intent',
            AbstractCreateAction::class
        );

        $mockCreatePaymentIntent
            ->shouldReceive('setApi')
            ->once();
        $mockCreatePaymentIntent
            ->shouldReceive('setGateway')
            ->once();

        $mockCreatePaymentIntent
            ->shouldReceive('supports')
            ->andReturnUsing(function ($request) {
                return $request instanceof CreatePaymentIntent;
            });

        $mockCreatePaymentIntent
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreatePaymentIntent $request) {
                /** @var ArrayObject $rModel */
                $rModel = $request->getModel();
                $session = Session::constructFrom(array_merge([
                    'id' => 'pi_1',
                    'object' => PaymentIntent::OBJECT_NAME,
                ], $rModel->getArrayCopy()));
                $request->setApiResource($session);
            });
    }

    public function mockRetrieveAction(string $status): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.retrieve_payment_intent',
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

    public function mockUpdateAction(string $status, string $captureMethod): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.update_payment_intent',
            AbstractUpdateAction::class
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
                return $request instanceof UpdatePaymentIntent;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (UpdatePaymentIntent $request) use ($status, $captureMethod) {
                $values = array_merge([
                    'id' => $request->getId(),
                    'object' => PaymentIntent::OBJECT_NAME,
                    'status' => $status,
                    'capture_method' => $captureMethod,
                ], $request->getParameters());
                $request->setApiResource(PaymentIntent::constructFrom($values));
            });
    }

    public function mockCancelAction(string $captureMethod): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.cancel_payment_intent',
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
                return $request instanceof CancelPaymentIntent;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CancelPaymentIntent $request) use ($captureMethod) {
                $request->setApiResource(PaymentIntent::constructFrom([
                    'id' => $request->getId(),
                    'object' => PaymentIntent::OBJECT_NAME,
                    'capture_method' => $captureMethod,
                    'status' => PaymentIntent::STATUS_CANCELED,
                ]));
            });
    }

    public function mockCaptureAction(string $status): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.capture_payment_intent',
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
                return $request instanceof CapturePaymentIntent;
            });

        $mock
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CapturePaymentIntent $request) use ($status) {
                $request->setApiResource(PaymentIntent::constructFrom([
                    'id' => $request->getId(),
                    'object' => PaymentIntent::OBJECT_NAME,
                    'status' => $status,
                    'capture_method' => PaymentIntent::CAPTURE_METHOD_MANUAL,
                ]));
            });
    }
}
