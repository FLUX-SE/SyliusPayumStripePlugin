<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use Payum\Core\Payum;
use Payum\Core\Request\Refund;
use SM\Event\TransitionEvent;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class RefundOrderProcessor
{
    public const HANDLEABLE_GATEWAYS = [
        'stripe_checkout_session',
        'stripe_js',
    ];

    /** @var Payum */
    private $payum;

    public function __invoke(PaymentInterface $payment, TransitionEvent $event): void
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        if (null === $paymentMethod) {
            return;
        }

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        if (null === $gatewayConfig) {
            return;
        }

        $config = $gatewayConfig->getConfig();
        if (false == isset($config['factory'])) {
            return;
        }

        if (false === in_array($config['factory'], self::HANDLEABLE_GATEWAYS)) {
            return;
        }

        $gateway = $this->payum->getGateway($gatewayConfig->getGatewayName());

        $request = new Refund($token);
        $gateway->execute($request);
    }
}