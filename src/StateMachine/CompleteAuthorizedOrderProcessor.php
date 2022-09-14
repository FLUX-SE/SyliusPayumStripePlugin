<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use FluxSE\SyliusPayumStripePlugin\Factory\CaptureRequestFactoryInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\ReplyInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class CompleteAuthorizedOrderProcessor extends AbstractOrderProcessor
{
    /** @var CaptureRequestFactoryInterface */
    private $captureRequestFactory;

    public function __construct(
        CaptureRequestFactoryInterface $captureRequestFactory,
        Payum $payum,
    ) {
        $this->captureRequestFactory = $captureRequestFactory;
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

        $request = $this->captureRequestFactory->createNewWithToken($token);
        $reply = $gateway->execute($request);

        Assert::notInstanceOf($reply, ReplyInterface::class);
    }
}
