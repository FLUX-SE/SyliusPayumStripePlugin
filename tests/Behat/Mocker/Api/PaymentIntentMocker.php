<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractUpdateAction;
use FluxSE\PayumStripe\Action\Api\Resource\CancelPaymentIntentAction;
use FluxSE\PayumStripe\Action\Api\Resource\CapturePaymentIntentAction;
use FluxSE\PayumStripe\Request\Api\Resource\CancelPaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CapturePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CreatePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\RetrievePaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\UpdatePaymentIntent;
use Mockery\MockInterface;
use Stripe\PaymentIntent;

final readonly class PaymentIntentMocker
{
    public function __construct(
        private MockInterface&AbstractCreateAction $mockCreatePaymentIntentAction,
        private MockInterface&AbstractRetrieveAction $mockRetrievePaymentIntentAction,
        private MockInterface&AbstractUpdateAction $mockUpdatePaymentIntentAction,
        private MockInterface&AbstractRetrieveAction $mockCancelPaymentIntentAction,
        private MockInterface&AbstractRetrieveAction $mockCapturePaymentIntentAction,
    ){
    }

    public function mockCreateAction(): void
    {
        $this->mockCreatePaymentIntentAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockCreatePaymentIntentAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockCreatePaymentIntentAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn($request) => $request instanceof CreatePaymentIntent);

        $this->mockCreatePaymentIntentAction
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreatePaymentIntent $request) {
                /** @var \ArrayObject $rModel */
                $rModel = $request->getModel();
                $paymentIntent = PaymentIntent::constructFrom(array_merge([
                    'id' => 'pi_1',
                    'object' => PaymentIntent::OBJECT_NAME,
                    'client_secret' => '1234567890',
                ], $rModel->getArrayCopy()));
                $request->setApiResource($paymentIntent);
            });
    }

    public function mockRetrieveAction(string $status): void
    {
        $this->mockRetrievePaymentIntentAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockRetrievePaymentIntentAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockRetrievePaymentIntentAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn($request) => $request instanceof RetrievePaymentIntent);

        $this->mockRetrievePaymentIntentAction
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (RetrievePaymentIntent $request) use ($status) {
                $request->setApiResource(PaymentIntent::constructFrom([
                    'id' => $request->getId(),
                    'object' => PaymentIntent::OBJECT_NAME,
                    'status' => $status,
                    'client_secret' => '1234567890',
                ]));
            });
    }

    public function mockUpdateAction(string $status, string $captureMethod): void
    {
        $this->mockUpdatePaymentIntentAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockUpdatePaymentIntentAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockUpdatePaymentIntentAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn($request) => $request instanceof UpdatePaymentIntent);

        $this->mockUpdatePaymentIntentAction
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
        $this->mockCancelPaymentIntentAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockCancelPaymentIntentAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockCancelPaymentIntentAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn($request) => $request instanceof CancelPaymentIntent);

        $this->mockCancelPaymentIntentAction
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
        $this->mockCapturePaymentIntentAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockCapturePaymentIntentAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockCapturePaymentIntentAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn($request) => $request instanceof CapturePaymentIntent);

        $this->mockCapturePaymentIntentAction
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

    public function unmock(): void
    {
        $this->mockCreatePaymentIntentAction->expects([]);
        $this->mockRetrievePaymentIntentAction->expects([]);
        $this->mockUpdatePaymentIntentAction->expects([]);
        $this->mockCancelPaymentIntentAction->expects([]);
        $this->mockCapturePaymentIntentAction->expects([]);
    }
}
