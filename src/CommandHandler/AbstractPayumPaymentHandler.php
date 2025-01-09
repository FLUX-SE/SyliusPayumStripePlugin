<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\CommandHandler;

use FluxSE\SyliusPayumStripePlugin\Command\PaymentIdAwareCommandInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;

abstract class AbstractPayumPaymentHandler
{
    /**
     * @param string[] $supportedGateways
     */
    public function __construct(
        private readonly PaymentRepositoryInterface $paymentRepository,
        protected Payum $payum,
        protected array $supportedGateways,
    ) {
    }

    protected function retrievePayment(PaymentIdAwareCommandInterface $command): ?PaymentInterface
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($command->getPaymentId());

        return $payment;
    }

    protected function getGatewayNameFromPayment(PaymentInterface $payment): ?string
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        if (null === $paymentMethod) {
            return null;
        }

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            return null;
        }

        $config = $gatewayConfig->getConfig();
        $factory = $config['factory'] ?? $gatewayConfig->getFactoryName();

        if (false === in_array($factory, $this->supportedGateways, true)) {
            return null;
        }

        return $gatewayConfig->getGatewayName();
    }

    protected function buildToken(string $gatewayName, PaymentInterface $payment): TokenInterface
    {
        /** @var TokenFactoryInterface $tokenFactory */
        $tokenFactory = $this->payum->getTokenFactory();

        return $tokenFactory->createToken($gatewayName, $payment, 'payum_notify_do');
    }
}
