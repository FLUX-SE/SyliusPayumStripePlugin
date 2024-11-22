<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\CancelPayment;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CancelOrderProcessorSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    public function it_is_invokable_when_it_is_new(
        PaymentInterface $payment,
        MessageBusInterface $commandBus
    ): void {
        $payment->getId()->willReturn(1);

        $command = new CancelPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment, PaymentInterface::STATE_NEW);
    }

    public function it_is_invokable_when_is_authorized(
        PaymentInterface $payment,
        MessageBusInterface $commandBus
    ): void {
        $payment->getId()->willReturn(1);

        $command = new CancelPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment, PaymentInterface::STATE_AUTHORIZED);
    }

    public function it_do_nothing_when_it_is_completed(
        PaymentInterface $payment
    ): void {
        $this->__invoke($payment, PaymentInterface::STATE_COMPLETED);
    }

    public function it_do_nothing_when_it_is_refunded(
        PaymentInterface $payment
    ): void {
        $this->__invoke($payment, PaymentInterface::STATE_REFUNDED);
    }
}
