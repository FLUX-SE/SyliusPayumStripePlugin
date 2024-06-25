<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\PaymentConfiguration;

use FluxSE\SyliusPayumStripePlugin\Api\Payum\ProcessorInterface;
use Payum\Core\Reply\HttpRedirect;
use Sylius\Bundle\ApiBundle\Payment\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StripeCheckoutSessionPaymentConfigProvider implements PaymentConfigurationProviderInterface
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
        $reply = $data['reply'];

        if ($reply instanceof HttpRedirect) {
            $config['stripe_checkout_session_url'] = $reply->getUrl();
        }

        return $config;
    }
}
