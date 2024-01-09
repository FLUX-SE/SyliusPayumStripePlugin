<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use PhpSpec\ObjectBehavior;
use SM\Event\TransitionEvent;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundOrderProcessorSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $commandBus): void {
        $this->beConstructedWith($commandBus);
    }

    public function it_is_invokable(
        PaymentInterface $payment,
        TransitionEvent $event,
        MessageBusInterface $commandBus
    ): void {
        $event->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $payment->getId()->willReturn(1);

        $command = new RefundPayment(1);
        $commandBus->dispatch($command)->willReturn(new Envelope($command));

        $this->__invoke($payment, $event);
    }

    public function it_do_nothing_when_it_is_authorized(
        PaymentInterface $payment,
        TransitionEvent $event
    ): void {
        $event->getState()->willReturn(PaymentInterface::STATE_AUTHORIZED);

        $this->__invoke($payment, $event);
    }
}
