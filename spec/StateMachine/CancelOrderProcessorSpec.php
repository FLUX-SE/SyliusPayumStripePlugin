<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\CancelPayment;
use PhpSpec\ObjectBehavior;
use SM\Event\TransitionEvent;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CancelOrderProcessorSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $commandBus): void {
        $this->beConstructedWith($commandBus);
    }

    public function it_is_invokable_when_it_is_new(
        PaymentInterface $payment,
        TransitionEvent $event,
        MessageBusInterface $commandBus,
    ): void {
        $event->getState()->willReturn(PaymentInterface::STATE_NEW);

        $payment->getId()->willReturn(1);

        $command = new CancelPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment);
    }

    public function it_is_invokable_when_is_authorized(
        PaymentInterface $payment,
        TransitionEvent $event,
        MessageBusInterface $commandBus
    ): void {

        $event->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $payment->getId()->willReturn(1);

        $command = new CancelPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment);
    }

    public function it_do_nothing_when_it_is_completed(
        PaymentInterface $payment,
        TransitionEvent $event
    ): void {
        $event->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $this->__invoke($payment);
    }

    public function it_do_nothing_when_it_is_refunded(
        PaymentInterface $payment,
        TransitionEvent $event
    ): void {
        $event->getState()->willReturn(PaymentInterface::STATE_REFUNDED);

        $this->__invoke($payment);
    }
}
