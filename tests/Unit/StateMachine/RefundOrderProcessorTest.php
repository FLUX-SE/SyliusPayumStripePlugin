<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use FluxSE\SyliusPayumStripePlugin\StateMachine\RefundOrderProcessor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundOrderProcessorTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBusMock;

    private RefundOrderProcessor $refundOrderProcessor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->commandBusMock = $this->createMock(MessageBusInterface::class);
        $this->refundOrderProcessor = new RefundOrderProcessor($this->commandBusMock, false);
    }

    /**
     * @throws Exception
     */
    public function testInvokable(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->expects($this->once())->method('getId')->willReturn(1);
        $command = new RefundPayment(1);
        $this->commandBusMock->expects($this->once())->method('dispatch')->with($command)->willReturn(new Envelope($command));
        $this->refundOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_COMPLETED);
    }

    /**
     * @throws Exception
     */
    public function testDoNothingWhenItIsDisabled(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $this->refundOrderProcessor = new RefundOrderProcessor($this->commandBusMock, true);
        $command = new RefundPayment(1);
        $this->commandBusMock->expects($this->never())->method('dispatch')->with($command);
        $this->refundOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_COMPLETED);
    }
}
