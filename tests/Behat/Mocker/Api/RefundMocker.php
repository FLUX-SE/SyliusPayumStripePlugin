<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use ArrayObject;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Request\Api\Resource\CreateRefund;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Stripe\Refund;

final class RefundMocker
{
    public function __construct(
        private MockInterface&AbstractCreateAction $mockCreateRefundAction,
    ) {
    }

    public function mockCreateAction(): void
    {
        $this->mockCreateRefundAction
            ->shouldReceive('setApi')
            ->once();
        $this->mockCreateRefundAction
            ->shouldReceive('setGateway')
            ->once();

        $this->mockCreateRefundAction
            ->shouldReceive('supports')
            ->andReturnUsing(fn ($request) => $request instanceof CreateRefund);

        $this->mockCreateRefundAction
            ->shouldReceive('execute')
            ->once()
            ->andReturnUsing(function (CreateRefund $request) {
                /** @var ArrayObject<string, mixed> $rModel */
                $rModel = $request->getModel();
                $refund = Session::constructFrom(array_merge([
                    'id' => 're_1',
                    'object' => Refund::OBJECT_NAME,
                ], $rModel->getArrayCopy()));
                $request->setApiResource($refund);
            });
    }

    public function unmock(): void
    {
        $this->mockCreateRefundAction->expects([]);
    }
}
