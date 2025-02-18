<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final readonly class RefundOrderProcessor implements PaymentStateProcessorInterface
{
    public function __construct(private MessageBusInterface $commandBus, private bool $disabled)
    {
    }

    public function __invoke(PaymentInterface $payment, string $fromState): void
    {
        if ($this->disabled) {
            return;
        }

        /** @var int|null $paymentId */
        $paymentId = $payment->getId();
        Assert::notNull($paymentId, 'A payment ID was not provided on the payment object.');
        $this->commandBus->dispatch(new RefundPayment($paymentId));
    }
}
