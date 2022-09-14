<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\CancelRequestFactoryInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Model\PaymentInterface;

final class CancelAuthorizedOrderProcessor extends AbstractOrderProcessor
{
    /** @var CancelRequestFactoryInterface */
    private $cancelRequestFactory;

    public function __construct(
        CancelRequestFactoryInterface $cancelRequestFactory,
        Payum $payum,
    ) {
        $this->cancelRequestFactory = $cancelRequestFactory;
        parent::__construct($payum);
    }

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

        $cancelRequest = $this->cancelRequestFactory->createNewWithToken($token);
        $gateway->execute($cancelRequest);
    }
}
