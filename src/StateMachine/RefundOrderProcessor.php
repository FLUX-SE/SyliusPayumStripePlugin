<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use SM\Event\TransitionEvent;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class RefundOrderProcessor
{
    /** @var MessageBusInterface */
    private $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(PaymentInterface $payment, TransitionEvent $event): void
    {
        if (PaymentInterface::STATE_COMPLETED !== $event->getState()) {
            return;
        }

        /** @var int|null $paymentId */
        $paymentId = $payment->getId();
        Assert::notNull($paymentId);
        $this->commandBus->dispatch(new RefundPayment($paymentId));
    }
}
