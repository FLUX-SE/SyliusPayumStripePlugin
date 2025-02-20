<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\CommandHandler;

use FluxSE\SyliusPayumStripePlugin\Command\CancelPayment;
use FluxSE\SyliusPayumStripePlugin\Factory\CancelRequestFactoryInterface;
use Payum\Core\Payum;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

final class CancelPaymentHandler extends AbstractPayumPaymentHandler
{
    /** @var CancelRequestFactoryInterface */
    private $cancelRequestFactory;

    /**
     * @param string[] $supportedGateways
     */
    public function __construct(
        CancelRequestFactoryInterface $cancelRequestFactory,
        PaymentRepositoryInterface $paymentRepository,
        Payum $payum,
        array $supportedGateways
    ) {
        $this->cancelRequestFactory = $cancelRequestFactory;

        parent::__construct($paymentRepository, $payum, $supportedGateways);
    }

    public function __invoke(CancelPayment $command): void
    {
        $payment = $this->retrievePayment($command);
        if (null === $payment) {
            return;
        }

        if (0 === count($payment->getDetails())) {
            return;
        }

        $details = $payment->getDetails();
        if (isset($details['expires_at']) && $details['expires_at'] <= (new \DateTime())->getTimestamp()) {
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
