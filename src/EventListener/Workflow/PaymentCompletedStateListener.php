<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\EventListener\Workflow;

use FluxSE\SyliusPayumStripePlugin\StateMachine\PaymentStateProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final readonly class PaymentCompletedStateListener
{
    public function __construct(private PaymentStateProcessorInterface $paymentStateProcessor)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var PaymentInterface $payment */
        $payment = $event->getSubject();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $transition = $event->getTransition();
        Assert::notNull($transition);
        // state machine transition from list always contains 1 element
        $fromState = $transition->getFroms()[0];
        Assert::notNull($fromState);

        $this->paymentStateProcessor->__invoke($payment, $fromState);
    }
}
