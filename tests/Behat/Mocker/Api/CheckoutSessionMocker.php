<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use FluxSE\PayumStripe\Action\Api\Resource\AbstractAllAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Request\Api\Resource\AllSession;
use FluxSE\PayumStripe\Request\Api\Resource\CreateSession;
use FluxSE\PayumStripe\Request\Api\Resource\ExpireSession;
use FluxSE\PayumStripe\Request\Api\Resource\RetrieveSession;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Stripe\Collection;

final class CheckoutSessionMocker
{
    public function __construct(
        private MockInterface&AbstractCreateAction $mockCreateSessionAction,
        private MockInterface&AbstractRetrieveAction $mockRetrieveSessionAction,
        private MockInterface&AbstractAllAction $mockAllSessionAction,
        private MockInterface&AbstractRetrieveAction $mockExpireSessionAction,
    ) {
    }

    public function mockCreateAction(): void
    {
        $this->mockCreateSessionAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockCreateSessionAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockCreateSessionAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn ($request) => $request instanceof CreateSession);

        $this->mockCreateSessionAction
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreateSession $request) {
                /** @var \ArrayObject<string, mixed> $rModel */
                $rModel = $request->getModel();
                $session = Session::constructFrom(array_merge([
                    'id' => 'cs_1',
                    'object' => Session::OBJECT_NAME,
                    'payment_intent' => 'pi_1',
                    'url' => 'https://checkout.stripe.com/c/pay/cs_1',
                ], $rModel->getArrayCopy()));
                $request->setApiResource($session);
            });
    }

    public function mockRetrieveAction(string $status, string $paymentStatus): void
    {
        $this->mockRetrieveSessionAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockRetrieveSessionAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockRetrieveSessionAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn ($request) => $request instanceof RetrieveSession);

        $this->mockRetrieveSessionAction
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

    public function mockAllAction(string $status): void
    {
        $this->mockAllSessionAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockAllSessionAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockAllSessionAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn ($request) => $request instanceof AllSession);

        $this->mockAllSessionAction
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
                    ]]),
                );
            });
    }

    public function mockExpireAction(): void
    {
        $this->mockExpireSessionAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockExpireSessionAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockExpireSessionAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn ($request) => $request instanceof ExpireSession);

        $this->mockExpireSessionAction
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (ExpireSession $request) {
                $request->setApiResource(Session::constructFrom([
                    'id' => $request->getId(),
                    'object' => Session::OBJECT_NAME,
                    'status' => Session::STATUS_EXPIRED,
                ]));
            });
    }

    public function unmock(): void
    {
        $this->mockCreateSessionAction->expects([]);
        $this->mockRetrieveSessionAction->expects([]);
        $this->mockAllSessionAction->expects([]);
        $this->mockExpireSessionAction->expects([]);
    }
}
