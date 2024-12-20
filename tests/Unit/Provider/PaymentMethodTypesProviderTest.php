<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\PaymentMethodTypesProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\PaymentMethodTypesProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;

final class PaymentMethodTypesProviderTest extends TestCase
{
    private PaymentMethodTypesProvider $paymentMethodTypesProvider;

    protected function setUp(): void
    {
        $this->paymentMethodTypesProvider = new PaymentMethodTypesProvider([
            'card',
        ]);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(PaymentMethodTypesProvider::class, $this->paymentMethodTypesProvider);
        $this->assertInstanceOf(PaymentMethodTypesProviderInterface::class, $this->paymentMethodTypesProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetPaymentMethodTypes(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->assertSame([
            'card',
        ], $this->paymentMethodTypesProvider->getPaymentMethodTypes($orderMock));
    }
}
