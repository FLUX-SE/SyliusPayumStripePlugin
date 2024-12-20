<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;

final class ShippingLineItemProviderTest extends TestCase
{
    private ShippingLineItemNameProviderInterface&MockObject $shippingLineItemNameProviderMock;

    private ShippingLineItemProvider $shippingLineItemProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->shippingLineItemNameProviderMock = $this->createMock(ShippingLineItemNameProviderInterface::class);
        $this->shippingLineItemProvider = new ShippingLineItemProvider($this->shippingLineItemNameProviderMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(ShippingLineItemProvider::class, $this->shippingLineItemProvider);
        $this->assertInstanceOf(ShippingLineItemProviderInterface::class, $this->shippingLineItemProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetLineItem(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->shippingLineItemNameProviderMock->expects($this->atLeastOnce())->method('getItemName')->with($orderMock)->willReturn('My shipping method');
        $orderMock->expects($this->atLeastOnce())->method('getShippingTotal')->willReturn(1000);
        $orderMock->expects($this->atLeastOnce())->method('getCurrencyCode')->willReturn('USD');
        $this->assertSame([
            'price_data' => [
                'unit_amount' => 1000,
                'currency' => 'USD',
                'product_data' => [
                    'name' => 'My shipping method',
                ],
            ],
            'quantity' => 1,
        ], $this->shippingLineItemProvider->getLineItem($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testGetLineItemWhenThereIsNoShipping(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->expects($this->atLeastOnce())->method('getShippingTotal')->willReturn(0);
        $this->assertNull($this->shippingLineItemProvider->getLineItem($orderMock));
    }
}
