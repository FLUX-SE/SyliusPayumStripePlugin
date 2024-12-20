<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\LinetItemNameProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class LinetItemNameProviderTest extends TestCase
{
    private LinetItemNameProvider $linetItemNameProvider;

    protected function setUp(): void
    {
        $this->linetItemNameProvider = new LinetItemNameProvider();
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(LinetItemNameProvider::class, $this->linetItemNameProvider);
        $this->assertInstanceOf(LinetItemNameProviderInterface::class, $this->linetItemNameProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetProductAndVariantNameWhenProductHasOptions(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $orderItemMock->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItemMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $orderItemMock->expects($this->once())->method('getProductName')->willReturn('My Product');
        $orderItemMock->expects($this->once())->method('getVariantName')->willReturn('variant');
        $productMock->expects($this->once())->method('hasOptions')->willReturn(true);
        $this->assertSame('1x - My Product variant', $this->linetItemNameProvider->getItemName($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetProductAndVariantNameWhenProductHasNoOptions(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $orderItemMock->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItemMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $orderItemMock->expects($this->once())->method('getProductName')->willReturn('My Product');
        $orderItemMock->expects($this->once())->method('getVariantName')->willReturn('variant');
        $productMock->expects($this->once())->method('hasOptions')->willReturn(false);
        $this->assertSame('1x - variant', $this->linetItemNameProvider->getItemName($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetItemNameWithVariantName(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductInterface&MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        $orderItemMock->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItemMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $orderItemMock->expects($this->once())->method('getProductName')->willReturn(null);
        $orderItemMock->expects($this->once())->method('getVariantName')->willReturn('My variant name');
        $productMock->expects($this->once())->method('hasOptions')->willReturn(false);
        $this->assertSame('1x - My variant name', $this->linetItemNameProvider->getItemName($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetItemNameWithProductName(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $orderItemMock->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItemMock->expects($this->once())->method('getProductName')->willReturn('My variant name');
        $orderItemMock->expects($this->once())->method('getVariantName')->willReturn(null);
        $this->assertSame('1x - My variant name', $this->linetItemNameProvider->getItemName($orderItemMock));
    }

    /**
     * @throws Exception
     */
    public function testGetItemNameWithoutVariantNameOrProductName(): void
    {
        /** @var OrderItemInterface&MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        $orderItemMock->expects($this->once())->method('getQuantity')->willReturn(1);
        $orderItemMock->expects($this->once())->method('getProductName')->willReturn(null);
        $orderItemMock->expects($this->once())->method('getVariantName')->willReturn(null);
        $this->assertSame('1x - ', $this->linetItemNameProvider->getItemName($orderItemMock));
    }
}
