<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Command\CancelPayment;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final readonly class CancelOrderProcessor implements PaymentStateProcessorInterface
{
    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    public function __invoke(PaymentInterface $payment, string $fromState): void
    {
        if (false === in_array($fromState, [
                PaymentInterface::STATE_NEW,
                PaymentInterface::STATE_AUTHORIZED,
            ], true)) {
            return;
        }

        /** @var int|null $paymentId */
        $paymentId = $payment->getId();
        Assert::notNull($paymentId, 'A payment ID was not provided on the payment object.');
        $this->commandBus->dispatch(new CancelPayment($paymentId));
    }
}
