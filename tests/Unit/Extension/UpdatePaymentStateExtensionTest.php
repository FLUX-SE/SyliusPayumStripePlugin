<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Extension;

use FluxSE\SyliusPayumStripePlugin\Extension\UpdatePaymentStateExtension;
use Payum\Core\Extension\Context;
use Payum\Core\Extension\ExtensionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Model\PaymentInterface as PayumPaymentInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\TokenAggregateInterface;
use Payum\Core\Storage\IdentityInterface;
use Payum\Core\Storage\StorageInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class UpdatePaymentStateExtensionTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachineMock;

    private MockObject&StorageInterface $storageMock;

    private MockObject&GetStatusFactoryInterface $getStatusRequestFactoryMock;

    private UpdatePaymentStateExtension $updatePaymentStateExtension;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->storageMock = $this->createMock(StorageInterface::class);
        $this->getStatusRequestFactoryMock = $this->createMock(GetStatusFactoryInterface::class);
        $this->updatePaymentStateExtension = new UpdatePaymentStateExtension($this->stateMachineMock, $this->storageMock, $this->getStatusRequestFactoryMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(ExtensionInterface::class, $this->updatePaymentStateExtension);
    }

    /**
     * @throws Exception
     */
    public function testOnPreExecuteWithIdentityFindsTheRelatedPaymentAndStoresIt(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var IdentityInterface&MockObject $modelMock */
        $modelMock = $this->createMock(IdentityInterface::class); // deprecated : implements the Serializable interface, which is deprecated. Implement __serialize() and __unserialize() instead (or in addition, if support for old PHP versions is necessary)
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($modelMock);
        $this->storageMock->expects(self::atLeastOnce())->method('find')->with($modelMock)->willReturn($paymentMock);
        $paymentMock->expects(self::once())->method('getId')->willReturn(1);

        $this->updatePaymentStateExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPreExecuteWithPaymentStoresIt(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PaymentInterface&MockObject $modelMock */
        $modelMock = $this->createMock(PaymentInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($modelMock);
        $modelMock->expects(self::atLeastOnce())->method('getId')->willReturn(1);
        $this->updatePaymentStateExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPreExecuteWithoutPaymentOrIdentifyDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PayumPaymentInterface&MockObject $modelMock */
        $modelMock = $this->createMock(PayumPaymentInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($modelMock);
        $this->updatePaymentStateExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPreExecuteWithoutModelAggregateInterfaceDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var TokenAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(TokenAggregateInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $this->updatePaymentStateExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnExecuteDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);

        $contextMock->expects(self::never())->method(self::anything());

        $this->updatePaymentStateExtension->onExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteApplyATransition(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var GetStatusInterface&MockObject $statusMock */
        $statusMock = $this->createMock(GetStatusInterface::class);
        /** @var GatewayInterface&MockObject $gatewayMock */
        $gatewayMock = $this->createMock(GatewayInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($paymentMock);
        $paymentMock->expects(self::atLeastOnce())->method('getId')->willReturn(1);
        $contextMock->expects(self::atLeastOnce())->method('getPrevious')->willReturn([]);
        $contextMock->expects(self::atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $this->getStatusRequestFactoryMock->expects(self::atLeastOnce())->method('createNewWithModel')->with($paymentMock)->willReturn($statusMock);
        $gatewayMock->expects(self::atLeastOnce())->method('execute')->with($statusMock);
        $paymentMock->expects(self::atLeastOnce())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $statusMock->expects(self::atLeastOnce())->method('getValue')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->stateMachineMock->expects(self::atLeastOnce())->method('getTransitionToState')->with($paymentMock, PaymentTransitions::GRAPH, PaymentInterface::STATE_COMPLETED)->willReturn('complete');
        $this->stateMachineMock->expects(self::atLeastOnce())->method('apply')->with($paymentMock, PaymentTransitions::GRAPH, 'complete');
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteApplyATransitionWithoutASyliusPaymentInterfaceWhenThereWasPreviouslyStoredPayment(): void
    {
        /** @var Context&MockObject $previousContextMock */
        $previousContextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $previousRequestMock */
        $previousRequestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PaymentInterface&MockObject $previousPaymentMock */
        $previousPaymentMock = $this->createMock(PaymentInterface::class);
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PayumPaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PayumPaymentInterface::class);
        /** @var GetStatusInterface&MockObject $statusMock */
        $statusMock = $this->createMock(GetStatusInterface::class);
        /** @var GatewayInterface&MockObject $gatewayMock */
        $gatewayMock = $this->createMock(GatewayInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn(null);
        $previousContextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($previousRequestMock);
        $previousRequestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($previousPaymentMock);
        $previousPaymentMock->expects(self::atLeastOnce())->method('getId')->willReturn(1);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($paymentMock);
        $contextMock->expects(self::atLeastOnce())->method('getPrevious')->willReturn([]);
        $contextMock->expects(self::atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $this->getStatusRequestFactoryMock->expects(self::atLeastOnce())->method('createNewWithModel')->with($previousPaymentMock)->willReturn($statusMock);
        $gatewayMock->expects(self::atLeastOnce())->method('execute')->with($statusMock);
        $previousPaymentMock->expects(self::atLeastOnce())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $statusMock->expects(self::atLeastOnce())->method('getValue')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->stateMachineMock->expects(self::atLeastOnce())->method('getTransitionToState')->with($previousPaymentMock, PaymentTransitions::GRAPH, PaymentInterface::STATE_COMPLETED)->willReturn('complete');
        $this->stateMachineMock->expects(self::atLeastOnce())->method('apply')->with($previousPaymentMock, PaymentTransitions::GRAPH, 'complete');
        $this->updatePaymentStateExtension->onPreExecute($previousContextMock);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteWithoutModelAggregateInterfaceDoesNothingIfThereIsPreviousContext(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var TokenAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(TokenAggregateInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $contextMock->expects(self::atLeastOnce())->method('getPrevious')->willReturn([1]);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteWithoutModelAggregateInterfaceDoesNothingIfThereIsNoPreviousContext(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var TokenAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(TokenAggregateInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $contextMock->expects(self::atLeastOnce())->method('getPrevious')->willReturn([]);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteWithModelAggregateInterfaceDoesNothingIfItIsNotASyliusPaymentInterface(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        /** @var ModelAggregateInterface&MockObject $requestMock */
        $requestMock = $this->createMock(ModelAggregateInterface::class);
        /** @var PayumPaymentInterface&MockObject $modelMock */
        $modelMock = $this->createMock(PayumPaymentInterface::class);
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects(self::atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects(self::atLeastOnce())->method('getModel')->willReturn($modelMock);
        $contextMock->expects(self::atLeastOnce())->method('getPrevious')->willReturn([]);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnPostExecuteWithExceptionDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
        $exception = new \Exception();
        $contextMock->expects(self::atLeastOnce())->method('getException')->willReturn($exception);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }
}
