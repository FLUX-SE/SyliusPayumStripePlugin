<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\CommandHandler;

use FluxSE\SyliusPayumStripePlugin\Command\RefundPayment;
use FluxSE\SyliusPayumStripePlugin\Factory\RefundRequestFactoryInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class RefundPaymentHandler extends AbstractPayumPaymentHandler
{
    /** @var RefundRequestFactoryInterface */
    private $refundRequestFactory;

    /**
     * @param string[] $supportedGateways
     */
    public function __construct(
        RefundRequestFactoryInterface $refundRequestFactory,
        PaymentRepositoryInterface $paymentRepository,
        Payum $payum,
        array $supportedGateways
    ) {
        $this->refundRequestFactory = $refundRequestFactory;

        parent::__construct($paymentRepository, $payum, $supportedGateways);
    }

    public function __invoke(RefundPayment $command): void
    {
        $payment = $this->retrievePayment($command);
        if (null === $payment) {
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
