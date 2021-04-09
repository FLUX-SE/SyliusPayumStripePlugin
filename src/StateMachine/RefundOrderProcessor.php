<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\StateMachine;

use Payum\Core\Payum;
use Payum\Core\Request\Refund;
use SM\Event\TransitionEvent;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class RefundOrderProcessor
{
    public const HANDLEABLE_GATEWAYS = [
        'stripe_checkout_session',
        'stripe_js',
    ];

    /** @var Payum */
    private $payum;

    /** @var RouterInterface */
    private $router;

    public function __construct(
        Payum $payum,
        RouterInterface $router
    ) {
        $this->payum = $payum;
        $this->router = $router;
    }

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

        if (false === in_array($config['factory'], self::HANDLEABLE_GATEWAYS, true)) {
            return;
        }

        $gatewayName = $gatewayConfig->getGatewayName();
        $gateway = $this->payum->getGateway($gatewayName);

//        $route = $this->router->getRouteCollection()->get('sylius_shop_order_pay');
//        Assert::notNull($route);
//        /** @var array|null $routeSyliusParams */
//        $routeSyliusParams = $route->getDefault('_sylius');
//        Assert::notNull($routeSyliusParams);
//        /** @var array $redirectInfo */
//        $redirectInfo = $routeSyliusParams['redirect'] ?? [];
//        /** @var string|null $afterPath */
//        $afterPath = $redirectInfo['route'] ?? null;
//        /** @var array $afterPathParameters */
//        $afterPathParameters = $redirectInfo['parameters'] ?? [];

        $tokenFactory = $this->payum->getTokenFactory();
        $token = $tokenFactory->createRefundToken($gatewayName, $payment/*, $afterPath, $afterPathParameters*/);

        $request = new Refund($token);
        $gateway->execute($request);
    }
}
