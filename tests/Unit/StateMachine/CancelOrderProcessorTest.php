<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\CancelPayment;
use FluxSE\SyliusPayumStripePlugin\StateMachine\CancelOrderProcessor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CancelOrderProcessorTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBusMock;

    private CancelOrderProcessor $cancelOrderProcessor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->commandBusMock = $this->createMock(MessageBusInterface::class);
        $this->cancelOrderProcessor = new CancelOrderProcessor($this->commandBusMock);
    }

    /**
     * @throws Exception
     */
    public function testInvokableWhenItIsNew(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $command = new CancelPayment(1);
        $this->commandBusMock->expects($this->atLeastOnce())->method('dispatch')->with($command)->willReturn(new Envelope($command));
        $this->cancelOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_NEW);
    }

    /**
     * @throws Exception
     */
    public function testInvokableWhenIsAuthorized(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->expects($this->atLeastOnce())->method('getId')->willReturn(1);
        $command = new CancelPayment(1);
        $this->commandBusMock->expects($this->atLeastOnce())->method('dispatch')->with($command)->willReturn(new Envelope($command));
        $this->cancelOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_AUTHORIZED);
    }

    /**
     * @throws Exception
     */
    public function testDoNothingWhenItIsCompleted(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $this->cancelOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_COMPLETED);
    }

    /**
     * @throws Exception
     */
    public function testDoNothingWhenItIsRefunded(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $this->cancelOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_REFUNDED);
    }
}
