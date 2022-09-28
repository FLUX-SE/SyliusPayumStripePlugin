<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\ApiPlatform\Sylius;

use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Stripe\PaymentIntent;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class StripeJsPayment implements PaymentConfigurationProviderInterface
{
    /** @var RegistryInterface */
    private $payum;

    public function __construct(RegistryInterface $payum)
    {
        $this->payum = $payum;
    }

    public function supports(PaymentMethodInterface $paymentMethod): bool
    {
        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $config = $gatewayConfig->getConfig();
        $factory = $config['factory'] ?? $gatewayConfig->getFactoryName();

        return $factory === 'stripe_js';
    }

    public function provideConfiguration(PaymentInterface $payment): array
    {
        $gatewayConfig = $this->getGatewayConfig($payment);

        $config = $gatewayConfig->getConfig();

        $paymentIntent = $this->capturePayment($payment);

        return [
            'publishable_key' => $config['publishable_key'],
            'client_secret' => $paymentIntent->client_secret,
        ];
    }

    protected function capturePayment(PaymentInterface $payment): PaymentIntent
    {
        /** @var GenericTokenFactoryInterface $tokenFactory */
        $tokenFactory = $this->payum->getTokenFactory();
        $gatewayConfig = $this->getGatewayConfig($payment);

        $gatewayName = $gatewayConfig->getGatewayName();
        $token = $tokenFactory->createCaptureToken(
            $gatewayName,
            $payment,
            null//'show_home_page'
        );

        $request = new Capture($token);
        $this->payum->getGateway($gatewayName)->execute($request);

        $details = $request->getModel();
        return PaymentIntent::constructFrom($details);
    }

    protected function getGatewayConfig(PaymentInterface $payment): GatewayConfigInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod);

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        return $gatewayConfig;
    }
}
