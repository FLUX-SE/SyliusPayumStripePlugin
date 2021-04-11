<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class CompleteAuthorizedOrderProcessor extends AbstractOrderProcessor
{
    public function __invoke(PaymentInterface $payment): void
    {
        if (PaymentInterface::STATE_AUTHORIZED !== $payment->getState()) {
            return;
        }

        $gatewayName = $this->getGatewayNameFromPayment($payment);

        if (null === $gatewayName) {
            return;
        }

        $gateway = $this->payum->getGateway($gatewayName);
        $token = $this->buildToken($gatewayName, $payment);

        $request = new Capture($token);
        $reply = $gateway->execute($request);

        Assert::notInstanceOf($reply, HttpResponse::class);
    }
}
