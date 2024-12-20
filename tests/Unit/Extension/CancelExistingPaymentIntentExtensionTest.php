<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Extension;

use FluxSE\PayumStripe\Request\Api\Resource\AllInterface;
use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Extension\CancelExistingPaymentIntentExtension;
use FluxSE\SyliusPayumStripePlugin\Factory\AllSessionRequestFactoryInterface;
use FluxSE\SyliusPayumStripePlugin\Factory\ExpireSessionRequestFactoryInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Convert;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stripe\Checkout\Session;
use Stripe\Collection;
use Stripe\PaymentIntent;
use Stripe\SetupIntent;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelExistingPaymentIntentExtensionTest extends TestCase
{
    private MockObject&ExpireSessionRequestFactoryInterface $expireSessionRequestFactoryMock;

    private MockObject&AllSessionRequestFactoryInterface $allSessionRequestFactoryMock;

    private CancelExistingPaymentIntentExtension $cancelExistingPaymentIntentExtension;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->expireSessionRequestFactoryMock = $this->createMock(ExpireSessionRequestFactoryInterface::class);
        $this->allSessionRequestFactoryMock = $this->createMock(AllSessionRequestFactoryInterface::class);
        $this->cancelExistingPaymentIntentExtension = new CancelExistingPaymentIntentExtension($this->expireSessionRequestFactoryMock, $this->allSessionRequestFactoryMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, $this->cancelExistingPaymentIntentExtension);
    }

    /**
     * @throws Exception
     */
    public function testDoesNothingWhenActionIsNotTheConvertPaymentActionTargeted(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ActionInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testDoesNothingWhenPaymentDetailsAreEmpty(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ConvertPaymentActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ConvertPaymentActionInterface::class);
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getSource')->willReturn($paymentMock);
        $paymentMock->expects($this->atLeastOnce())->method('getDetails')->willReturn([]);
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testDoesNothingWhenPaymentDetailsAreSomethingElseThanAPaymentIntent(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ConvertPaymentActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ConvertPaymentActionInterface::class);
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getSource')->willReturn($paymentMock);
        $paymentMock->expects($this->atLeastOnce())->method('getDetails')->willReturn([
            'object' => SetupIntent::OBJECT_NAME,
        ]);
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testDoesntFoundTheRelatedSessionAndDoNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ConvertPaymentActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ConvertPaymentActionInterface::class);
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var GatewayInterface&MockObject $gatewayMock */
        $gatewayMock = $this->createMock(GatewayInterface::class);
        /** @var AllInterface&MockObject $allSessionRequestMock */
        $allSessionRequestMock = $this->createMock(AllInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getSource')->willReturn($paymentMock);
        $contextMock->expects($this->atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $piId = 'pi_test_0000000000000000000';
        $paymentMock->expects($this->atLeastOnce())->method('getDetails')->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);
        $this->allSessionRequestFactoryMock->expects($this->atLeastOnce())->method('createNew')
            ->willReturn($allSessionRequestMock);
        $allSessionRequestMock->expects($this->atLeastOnce())->method('setParameters')->with([
            'payment_intent' => $piId,
        ]);
        $allSessionRequestMock->expects($this->atLeastOnce())->method('getApiResources')->willReturn(Collection::constructFrom(['data' => []]));
        $gatewayMock->expects($this->atLeastOnce())->method('execute')->with($allSessionRequestMock);
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testFoundsARelatedExpiredSessionAndDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ConvertPaymentActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ConvertPaymentActionInterface::class);
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var GatewayInterface&MockObject $gatewayMock */
        $gatewayMock = $this->createMock(GatewayInterface::class);
        /** @var AllInterface&MockObject $allSessionRequestMock */
        $allSessionRequestMock = $this->createMock(AllInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getSource')->willReturn($paymentMock);
        $contextMock->expects($this->atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $piId = 'pi_test_0000000000000000000';
        $csId = 'cs_test_0000000000000000000';
        $paymentMock->expects($this->atLeastOnce())->method('getDetails')->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);
        $this->allSessionRequestFactoryMock->expects($this->atLeastOnce())->method('createNew')
            ->willReturn($allSessionRequestMock);
        $allSessionRequestMock->expects($this->atLeastOnce())->method('setParameters')->with([
            'payment_intent' => $piId,
        ]);
        $allSessionRequestMock->expects($this->atLeastOnce())->method('getApiResources')->willReturn(Collection::constructFrom([
            'data' => [
                [
                    'id' => $csId,
                    'status' => Session::STATUS_EXPIRED,
                ],
            ],
        ]));
        $gatewayMock->expects($this->atLeastOnce())->method('execute')->with($allSessionRequestMock);
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testFoundsARelatedSessionAndExpiresIt(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ConvertPaymentActionInterface&MockObject $actionMock */
        $actionMock = $this->createMock(ConvertPaymentActionInterface::class);
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var GatewayInterface&MockObject $gatewayMock */
        $gatewayMock = $this->createMock(GatewayInterface::class);
        /** @var AllInterface&MockObject $allSessionRequestMock */
        $allSessionRequestMock = $this->createMock(AllInterface::class);
        /** @var CustomCallInterface&MockObject $expireSessionRequestMock */
        $expireSessionRequestMock = $this->createMock(CustomCallInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getAction')->willReturn($actionMock);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getSource')->willReturn($paymentMock);
        $contextMock->expects($this->atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $piId = 'pi_test_0000000000000000000';
        $csId = 'cs_test_0000000000000000000';
        $paymentMock->expects($this->atLeastOnce())->method('getDetails')->willReturn([
            'id' => $piId,
            'object' => PaymentIntent::OBJECT_NAME,
        ]);
        $this->allSessionRequestFactoryMock->expects($this->once())->method('createNew')
            ->willReturn($allSessionRequestMock);
        $allSessionRequestMock->expects($this->once())->method('setParameters')->with([
            'payment_intent' => $piId,
        ]);
        $allSessionRequestMock->expects($this->atLeastOnce())->method('getApiResources')->willReturn(Collection::constructFrom([
            'data' => [
                [
                    'id' => $csId,
                    'status' => Session::STATUS_OPEN,
                ],
            ],
        ]));
        $this->expireSessionRequestFactoryMock->expects($this->once())->method('createNew')->with($csId)
            ->willReturn($expireSessionRequestMock);

        $matcher = $this->exactly(2);
        $gatewayMock
            ->expects($matcher)
            ->method('execute')
            ->willReturnCallback(function ($request) use ($matcher, $allSessionRequestMock, $expireSessionRequestMock) {
                match ($matcher->numberOfInvocations()) {
                    1 =>  $this->assertEquals($allSessionRequestMock, $request),
                    2 =>  $this->assertEquals($expireSessionRequestMock, $request),
                };
            });
        $this->cancelExistingPaymentIntentExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPreExecuteDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        $this->cancelExistingPaymentIntentExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        $this->cancelExistingPaymentIntentExtension->onPostExecute($contextMock);
    }
}
