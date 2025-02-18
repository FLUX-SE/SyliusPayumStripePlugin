<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\CommandHandler;

use FluxSE\SyliusPayumStripePlugin\Command\CaptureAuthorizedPayment;
use FluxSE\SyliusPayumStripePlugin\Factory\ModelAggregateFactoryInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\ReplyInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Webmozart\Assert\Assert;

final class CaptureAuthorizedPaymentHandler extends AbstractPayumPaymentHandler
{
    /**
     * @param string[] $supportedGateways
     */
    public function __construct(
        private readonly ModelAggregateFactoryInterface $captureRequestFactory,
        PaymentRepositoryInterface $paymentRepository,
        Payum $payum,
        array $supportedGateways,
    ) {
        parent::__construct($paymentRepository, $payum, $supportedGateways);
    }

    public function __invoke(CaptureAuthorizedPayment $command): void
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

        $request = $this->captureRequestFactory->createNewWithToken($token);
        $reply = $gateway->execute($request);

        // No reply must be done by this Capture request, if there is it means that a normal Capture has been done.
        Assert::notInstanceOf($reply, ReplyInterface::class);
    }
}
