<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\Api;

use ArrayObject;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractAllAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractCreateAction;
use FluxSE\PayumStripe\Action\Api\Resource\AbstractRetrieveAction;
use FluxSE\PayumStripe\Request\Api\Resource\AllSession;
use FluxSE\PayumStripe\Request\Api\Resource\CreateSession;
use FluxSE\PayumStripe\Request\Api\Resource\ExpireSession;
use FluxSE\PayumStripe\Request\Api\Resource\RetrieveSession;
use Stripe\Checkout\Session;
use Stripe\Collection;
use Sylius\Behat\Service\Mocker\MockerInterface;

final class CheckoutSessionMocker
{
    /** @var MockerInterface */
    private $mocker;

    public function __construct(MockerInterface $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockCreateAction(): void
    {
        $mockCreateSession = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.create_session',
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
                /** @var ArrayObject $rModel */
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
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.retrieve_session',
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

    public function mockAllAction(string $status): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.all_session',
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
                    ]])
                );
            });
    }

    public function mockExpireAction(): void
    {
        $mock = $this->mocker->mockService(
            'tests.flux_se.sylius_payum_stripe_plugin.behat.mocker.action.expire_session',
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
            ->once()
            ->andReturnUsing(function (ExpireSession $request) {
                $request->setApiResource(Session::constructFrom([
                    'id' => $request->getId(),
                    'object' => Session::OBJECT_NAME,
                    'status' => Session::STATUS_EXPIRED,
                ]));
            });
    }
}
