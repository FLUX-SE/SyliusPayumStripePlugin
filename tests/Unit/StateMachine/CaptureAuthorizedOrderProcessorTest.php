<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\CaptureAuthorizedPayment;
use FluxSE\SyliusPayumStripePlugin\StateMachine\CaptureAuthorizedOrderProcessor;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CaptureAuthorizedOrderProcessorTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBusMock;

    private CaptureAuthorizedOrderProcessor $captureAuthorizedOrderProcessor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->commandBusMock = $this->createMock(MessageBusInterface::class);
        $this->captureAuthorizedOrderProcessor = new CaptureAuthorizedOrderProcessor($this->commandBusMock);
    }

    /**
     * @throws Exception
     */
    public function testInvokable(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $paymentMock->expects($this->once())->method('getId')->willReturn(1);
        $command = new CaptureAuthorizedPayment(1);
        $this->commandBusMock->expects($this->once())->method('dispatch')->with($command)->willReturn(new Envelope($command));
        $this->captureAuthorizedOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_AUTHORIZED);
    }

    /**
     * @throws Exception
     */
    public function testDoNothingWhenItIsCompleted(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $this->captureAuthorizedOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_COMPLETED);
    }

    /**
     * @throws Exception
     */
    public function testDoNothingWhenItIsRefunded(): void
    {
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        $this->captureAuthorizedOrderProcessor->__invoke($paymentMock, PaymentInterface::STATE_REFUNDED);
    }
}
