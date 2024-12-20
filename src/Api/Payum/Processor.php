<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Api\Payum;

use FluxSE\SyliusPayumStripePlugin\Factory\ModelAggregateFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final readonly class Processor implements ProcessorInterface
{
    public function __construct(private Payum $payum, private ModelAggregateFactoryInterface $captureRequestFactory, private ModelAggregateFactoryInterface $authorizeRequestFactory, private AfterUrlProviderInterface $afterUrlProvider)
    {
    }

    public function __invoke(PaymentInterface $payment, bool $useAuthorize): array
    {
        $tokenFactory = $this->payum->getTokenFactory();
        $gatewayName = $this->getGatewayConfig($payment)->getGatewayName();

        $gateway = $this->payum->getGateway($gatewayName);

        if ($useAuthorize) {
            $token = $tokenFactory->createAuthorizeToken(
                $gatewayName,
                $payment,
                $this->afterUrlProvider->getAfterPath($payment),
                $this->afterUrlProvider->getAfterParameters($payment),
            );
            $request = $this->authorizeRequestFactory->createNewWithToken($token);
        } else {
            $token = $tokenFactory->createCaptureToken(
                $gatewayName,
                $payment,
                $this->afterUrlProvider->getAfterPath($payment),
                $this->afterUrlProvider->getAfterParameters($payment),
            );
            $request = $this->captureRequestFactory->createNewWithToken($token);
        }

        $reply = $gateway->execute($request, true);

        /** @var ArrayObject $details */
        $details = $request->getModel();

        return [
            'reply' => $reply,
            'details' => $details->getArrayCopy(),
        ];
    }

    private function getGatewayConfig(PaymentInterface $payment): GatewayConfigInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod, 'Unable to found a PaymentMethod on this Payment.');

        /** @var GatewayConfigInterface|null $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Unable to found a GatewayConfig on this PaymentMethod.');

        return $gatewayConfig;
    }
}
