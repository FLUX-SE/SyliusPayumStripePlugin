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
use Sylius\Component\Core\Model\PaymentInterface;
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
        $modelMock = $this->createMock(IdentityInterface::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($modelMock);
        $this->storageMock->expects($this->atLeastOnce())->method('find')->with($modelMock)->willReturn($paymentMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
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
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($modelMock);
        $modelMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
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
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($modelMock);
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
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $this->updatePaymentStateExtension->onPreExecute($contextMock);
    }

    /**
     * @throws Exception
     */
    public function testOnExecuteDoesNothing(): void
    {
        /** @var Context&MockObject $contextMock */
        $contextMock = $this->createMock(Context::class);
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($paymentMock);
        $paymentMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $contextMock->expects($this->atLeastOnce())->method('getPrevious')->willReturn([]);
        $contextMock->expects($this->atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $this->getStatusRequestFactoryMock->expects($this->atLeastOnce())->method('createNewWithModel')->with($paymentMock)->willReturn($statusMock);
        $gatewayMock->expects($this->atLeastOnce())->method('execute')->with($statusMock);
        $paymentMock->expects($this->atLeastOnce())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $statusMock->expects($this->atLeastOnce())->method('getValue')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->stateMachineMock->expects($this->atLeastOnce())->method('getTransitionToState')->with($paymentMock, PaymentTransitions::GRAPH, PaymentInterface::STATE_COMPLETED)->willReturn('complete');
        $this->stateMachineMock->expects($this->atLeastOnce())->method('apply')->with($paymentMock, PaymentTransitions::GRAPH, 'complete');
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn(null);
        $previousContextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($previousRequestMock);
        $previousRequestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($previousPaymentMock);
        $previousPaymentMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($paymentMock);
        $contextMock->expects($this->atLeastOnce())->method('getPrevious')->willReturn([]);
        $contextMock->expects($this->atLeastOnce())->method('getGateway')->willReturn($gatewayMock);
        $this->getStatusRequestFactoryMock->expects($this->atLeastOnce())->method('createNewWithModel')->with($previousPaymentMock)->willReturn($statusMock);
        $gatewayMock->expects($this->atLeastOnce())->method('execute')->with($statusMock);
        $previousPaymentMock->expects($this->atLeastOnce())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $statusMock->expects($this->atLeastOnce())->method('getValue')->willReturn(PaymentInterface::STATE_COMPLETED);
        $this->stateMachineMock->expects($this->atLeastOnce())->method('getTransitionToState')->with($previousPaymentMock, PaymentTransitions::GRAPH, PaymentInterface::STATE_COMPLETED)->willReturn('complete');
        $this->stateMachineMock->expects($this->atLeastOnce())->method('apply')->with($previousPaymentMock, PaymentTransitions::GRAPH, 'complete');
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $contextMock->expects($this->atLeastOnce())->method('getPrevious')->willReturn([1]);
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $contextMock->expects($this->atLeastOnce())->method('getPrevious')->willReturn([]);
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn(null);
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);
        $requestMock->expects($this->atLeastOnce())->method('getModel')->willReturn($modelMock);
        $contextMock->expects($this->atLeastOnce())->method('getPrevious')->willReturn([]);
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
        $contextMock->expects($this->atLeastOnce())->method('getException')->willReturn($exception);
        $this->updatePaymentStateExtension->onPostExecute($contextMock);
    }
}
