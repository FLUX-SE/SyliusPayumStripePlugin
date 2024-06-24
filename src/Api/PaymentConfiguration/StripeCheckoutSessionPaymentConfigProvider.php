<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\PaymentConfiguration;

use FluxSE\PayumStripe\Token\TokenHashKeysInterface;
use FluxSE\SyliusPayumStripePlugin\Api\Payum\CaptureProcessorInterface;
use Payum\Core\Payum;
use Stripe\Checkout\Session;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StripeCheckoutSessionPaymentConfigProvider implements PaymentConfigurationProviderInterface
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
        $session = Session::constructFrom($details);
        $config['stripe_checkout_session_url'] = $session->url;

        return $config;
    }
}
