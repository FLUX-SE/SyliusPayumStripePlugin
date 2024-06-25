<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\PaymentConfiguration;

use FluxSE\SyliusPayumStripePlugin\Api\Payum\ProcessorInterface;
use Stripe\PaymentIntent;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StripeJsPaymentConfigProvider implements PaymentConfigurationProviderInterface
{
    use StripePaymentConfigProviderTrait {
        StripePaymentConfigProviderTrait::__construct as private __stripePaymentConfigProviderConstruct;
    }

    private ProcessorInterface $captureProcessor;

    public function __construct(
        ProcessorInterface $captureProcessor,
        string $factoryName
    ) {
        $this->captureProcessor = $captureProcessor;
        $this->__stripePaymentConfigProviderConstruct($factoryName);
    }

    public function provideConfiguration(PaymentInterface $payment): array
    {
        $config = $this->provideDefaultConfiguration($payment);

        $data = $this->captureProcessor->__invoke($payment, $config['use_authorize']);
        $paymentIntent = PaymentIntent::constructFrom($data['details']);
        $config['stripe_payment_intent_client_secret'] = $paymentIntent->client_secret;

        return $config;
    }
}
