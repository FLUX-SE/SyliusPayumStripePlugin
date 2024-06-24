<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use FluxSE\SyliusPayumStripePlugin\Factory\CaptureRequestFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class CaptureProcessor implements CaptureProcessorInterface
{
    private Payum $payum;

    private CaptureRequestFactoryInterface $captureRequestFactory;

    private AfterUrlProviderInterface $afterUrlProvider;

    public function __construct(
        Payum $payum,
        CaptureRequestFactoryInterface $captureRequestFactory,
        AfterUrlProviderInterface $afterUrlProvider
    ) {
        $this->payum = $payum;
        $this->captureRequestFactory = $captureRequestFactory;
        $this->afterUrlProvider = $afterUrlProvider;
    }

    public function __invoke(PaymentInterface $payment): array
    {
        $tokenFactory = $this->payum->getTokenFactory();
        $gatewayName = $this->getGatewayConfig($payment)->getGatewayName();

        $token = $tokenFactory->createCaptureToken(
            $gatewayName,
            $payment,
            $this->afterUrlProvider->getAfterPath($payment),
            $this->afterUrlProvider->getAfterParameters($payment)
        );
        $gateway = $this->payum->getGateway($gatewayName);

        $captureRequest = $this->captureRequestFactory->createNewWithToken($token);
        $gateway->execute($captureRequest, true);

        /** @var ArrayObject $details */
        $details = $captureRequest->getModel();

        return $details->getArrayCopy();
    }

    private function getGatewayConfig(PaymentInterface $payment): GatewayConfigInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod, 'Unable to found a PaymentMethod on this Payment.');

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Unable to found a GatewayConfig on this PaymentMethod.');

        return $gatewayConfig;
    }
}
