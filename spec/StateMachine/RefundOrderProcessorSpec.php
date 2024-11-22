<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundOrderProcessorSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus, false);
    }

    public function it_is_invokable(
        PaymentInterface $payment,
        MessageBusInterface $commandBus
    ): void {
        $payment->getId()->willReturn(1);

        $command = new RefundPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment, PaymentInterface::STATE_COMPLETED);
    }

    public function it_do_nothing_when_it_is_disabled(
        PaymentInterface $payment,
        MessageBusInterface $commandBus
    ): void {
        $this->beConstructedWith($commandBus, true);

        $command = new RefundPayment(1);
        $commandBus->dispatch($command)->shouldNotBeCalled();

        $this->__invoke($payment, PaymentInterface::STATE_COMPLETED);
    }
}
