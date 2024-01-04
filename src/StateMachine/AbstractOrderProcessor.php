<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use Payum\Core\Payum;
use Payum\Core\Security\TokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use SM\Event\TransitionEvent;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

abstract class AbstractOrderProcessor
{
    public const HANDLEABLE_GATEWAYS = [
        'stripe_checkout_session',
        'stripe_js',
    ];

    /** @var Payum */
    protected $payum;

    public function __construct(Payum $payum)
    {
        $this->payum = $payum;
    }

    abstract public function __invoke(PaymentInterface $payment, TransitionEvent $event): void;

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

        if (false === in_array($factory, self::HANDLEABLE_GATEWAYS, true)) {
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
