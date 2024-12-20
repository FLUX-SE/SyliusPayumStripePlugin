<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemsProviderTest extends TestCase
{
    private LineItemProviderInterface&MockObject $lineItemProviderMock;

    private ShippingLineItemProviderInterface&MockObject $shippingLineItemProviderMock;

    private LineItemsProvider $lineItemsProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->lineItemProviderMock = $this->createMock(LineItemProviderInterface::class);
        $this->shippingLineItemProviderMock = $this->createMock(ShippingLineItemProviderInterface::class);
        $this->lineItemsProvider = new LineItemsProvider($this->lineItemProviderMock, $this->shippingLineItemProviderMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(LineItemsProvider::class, $this->lineItemsProvider);
        $this->assertInstanceOf(LineItemsProviderInterface::class, $this->lineItemsProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetLineItems(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $lineItem = [];
        $orderItems = new ArrayCollection([
            $orderItemMock,
        ]);
        $orderMock->expects($this->atLeastOnce())->method('getItems')->willReturn($orderItems);
        $this->lineItemProviderMock->expects($this->atLeastOnce())->method('getLineItem')->with($orderItemMock)->willReturn($lineItem);
        $this->shippingLineItemProviderMock->expects($this->atLeastOnce())->method('getLineItem')->with($orderMock)->willReturn(null);
        $this->assertSame([
            $lineItem,
        ], $this->lineItemsProvider->getLineItems($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testGetEmptyLineItems(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderItems = new ArrayCollection([]);
        $orderMock->expects($this->atLeastOnce())->method('getItems')->willReturn($orderItems);
        $this->shippingLineItemProviderMock->expects($this->atLeastOnce())->method('getLineItem')->with($orderMock)->willReturn(null);
        $this->assertSame([], $this->lineItemsProvider->getLineItems($orderMock));
    }
}
