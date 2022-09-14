<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\RefundRequestFactoryInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Model\PaymentInterface;

final class RefundOrderProcessor extends AbstractOrderProcessor
{
    /** @var RefundRequestFactoryInterface */
    private $refundRequestFactory;

    public function __construct(
        RefundRequestFactoryInterface $refundRequestFactory,
        Payum $payum,
    ) {
        $this->refundRequestFactory = $refundRequestFactory;
        parent:: __construct($payum);
    }

    public function __invoke(PaymentInterface $payment): void
    {
        if (PaymentInterface::STATE_COMPLETED !== $payment->getState()) {
            return;
        }

        $gatewayName = $this->getGatewayNameFromPayment($payment);

        if (null === $gatewayName) {
            return;
        }

        $gateway = $this->payum->getGateway($gatewayName);
        $token = $this->buildToken($gatewayName, $payment);

        $refundRequest = $this->refundRequestFactory->createNewWithToken($token);
        $gateway->execute($refundRequest);
    }
}
