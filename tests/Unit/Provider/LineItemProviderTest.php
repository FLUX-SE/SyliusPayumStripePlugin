<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LineItemImagesProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class LineItemProviderTest extends TestCase
{
    private LineItemImagesProviderInterface&MockObject $lineItemImagesProviderMock;

    private LinetItemNameProviderInterface&MockObject $lineItemNameProviderMock;

    private LineItemProvider $lineItemProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->lineItemImagesProviderMock = $this->createMock(LineItemImagesProviderInterface::class);
        $this->lineItemNameProviderMock = $this->createMock(LinetItemNameProviderInterface::class);
        $this->lineItemProvider = new LineItemProvider($this->lineItemImagesProviderMock, $this->lineItemNameProviderMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(LineItemProvider::class, $this->lineItemProvider);
        $this->assertInstanceOf(LineItemProviderInterface::class, $this->lineItemProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetLineItem(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderItemMock->expects(self::atLeastOnce())->method('getOrder')->willReturn($orderMock);
        $orderItemMock->expects(self::atLeastOnce())->method('getTotal')->willReturn(1000);
        $orderMock->expects(self::atLeastOnce())->method('getCurrencyCode')->willReturn('USD');
        $this->lineItemImagesProviderMock->expects(self::atLeastOnce())->method('getImageUrls')->with($orderItemMock)->willReturn(['/path/image.jpg']);
        $this->lineItemNameProviderMock->expects(self::atLeastOnce())->method('getItemName')->with($orderItemMock)->willReturn('1x - My item name');
        $this->assertSame([
            'price_data' => [
                'unit_amount' => 1000,
                'currency' => 'USD',
                'product_data' => [
                    'name' => '1x - My item name',
                    'images' => [
                        '/path/image.jpg',
                    ],
                ],
            ],
            'quantity' => 1,
        ], $this->lineItemProvider->getLineItem($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetLineItemWithNoImages(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderItemMock->expects(self::atLeastOnce())->method('getOrder')->willReturn($orderMock);
        $orderItemMock->expects(self::atLeastOnce())->method('getTotal')->willReturn(1000);
        $orderMock->expects(self::atLeastOnce())->method('getCurrencyCode')->willReturn('USD');
        $this->lineItemImagesProviderMock->expects(self::atLeastOnce())->method('getImageUrls')->with($orderItemMock)->willReturn([]);
        $this->lineItemNameProviderMock->expects(self::atLeastOnce())->method('getItemName')->with($orderItemMock)->willReturn('1x - My item name');
        $this->assertSame([
            'price_data' => [
                'unit_amount' => 1000,
                'currency' => 'USD',
                'product_data' => [
                    'name' => '1x - My item name',
                    'images' => [],
                ],
            ],
            'quantity' => 1,
        ], $this->lineItemProvider->getLineItem($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetLineItemWhenQuantityIsGreaterThan1(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderItemMock->expects(self::atLeastOnce())->method('getOrder')->willReturn($orderMock);
        $orderItemMock->expects(self::atLeastOnce())->method('getTotal')->willReturn(1000);
        $orderMock->expects(self::atLeastOnce())->method('getCurrencyCode')->willReturn('USD');
        $this->lineItemImagesProviderMock->expects(self::atLeastOnce())->method('getImageUrls')->with($orderItemMock)->willReturn([]);
        $this->lineItemNameProviderMock->expects(self::atLeastOnce())->method('getItemName')->with($orderItemMock)->willReturn('2x - My item name');
        $this->assertSame([
            'price_data' => [
                'unit_amount' => 1000,
                'currency' => 'USD',
                'product_data' => [
                    'name' => '2x - My item name',
                    'images' => [],
                ],
            ],
            'quantity' => 1,
        ], $this->lineItemProvider->getLineItem($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetLineItemWithNullOrder(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $orderItemMock->expects(self::atLeastOnce())->method('getOrder')->willReturn(null);
        $this->assertNull($this->lineItemProvider->getLineItem($orderItemMock));
    }
}
