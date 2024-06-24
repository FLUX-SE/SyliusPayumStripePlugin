<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\PaymentConfiguration;

use FluxSE\SyliusPayumStripePlugin\Api\Payum\CaptureProcessorInterface;
use Stripe\PaymentIntent;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StripeJsPaymentConfigProvider implements PaymentConfigurationProviderInterface
{
    use StripePaymentConfigProviderTrait {
        StripePaymentConfigProviderTrait::__construct as private __stripePaymentConfigProviderConstruct;
    }
    private CaptureProcessorInterface $captureProcessor;

    public function __construct(
        CaptureProcessorInterface $captureProcessor,
        string $factoryName
    ) {
        $this->captureProcessor = $captureProcessor;
        $this->__stripePaymentConfigProviderConstruct($factoryName);
    }

    public function provideConfiguration(PaymentInterface $payment): array
    {
        $config = $this->provideDefaultConfiguration($payment);

        $details = $this->captureProcessor->__invoke($payment);
        $paymentIntent = PaymentIntent::constructFrom($details);
        $config['stripe_payment_intent_client_secret'] = $paymentIntent->client_secret;

        return $config;
    }
}
