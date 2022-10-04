<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\ApiPlatform\Sylius;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Payum;
use Payum\Core\Request\Capture;
use Stripe\PaymentIntent;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class StripeJsPayment implements PaymentConfigurationProviderInterface
{
    /** @var Payum */
    private $payum;

    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    public function supports(PaymentMethodInterface $paymentMethod): bool
    {
        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig);

        $config = $gatewayConfig->getConfig();
        /** @var string $factory */
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

    private function capturePayment(PaymentInterface $payment): PaymentIntent
    {
        $tokenFactory = $this->payum->getTokenFactory();
        $gatewayConfig = $this->getGatewayConfig($payment);

        $gatewayName = $gatewayConfig->getGatewayName();
        $token = $tokenFactory->createCaptureToken(
            $gatewayName,
            $payment,
            'sylius_shop_homepage'
        );

        $request = new Capture($token);
        $gateway = $this->payum->getGateway($gatewayName);

        // Execute with catchReply to avoid displaying something here
        /*$reply = */$gateway->execute($request, true);
        // Reply contain a Payum\Core\Reply\HttpResponse with the rendered template

        /** @var ArrayObject $details */
        $details = $request->getModel();

        return PaymentIntent::constructFrom($details->getArrayCopy());
    }

    private function getGatewayConfig(PaymentInterface $payment): GatewayConfigInterface
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
