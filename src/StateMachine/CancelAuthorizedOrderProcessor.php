<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use Payum\Core\Request\Refund;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelAuthorizedOrderProcessor extends AbstractOrderProcessor
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

        $request = new Refund($token);
        $gateway->execute($request);
    }
}
