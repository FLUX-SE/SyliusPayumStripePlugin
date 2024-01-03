<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractUpdateAction;
use FluxSE\PayumStripe\Request\Api\Resource\CancelPaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CapturePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\UpdatePaymentIntent;
use Stripe\PaymentIntent;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class PaymentIntentMocker
{
    /** @var MockerInterface */
    private $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
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
