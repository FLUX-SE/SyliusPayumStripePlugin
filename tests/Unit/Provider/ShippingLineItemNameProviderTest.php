<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ShippingLineItemNameProviderTest extends TestCase
{
    private ShippingLineItemNameProvider $shippingLineItemNameProvider;

    protected function setUp(): void
    {
        $this->shippingLineItemNameProvider = new ShippingLineItemNameProvider();
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(ShippingLineItemNameProvider::class, $this->shippingLineItemNameProvider);
        $this->assertInstanceOf(ShippingLineItemNameProviderInterface::class, $this->shippingLineItemNameProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetItemName(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface&MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface&MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        $shippingMethodMock->expects($this->atLeastOnce())->method('getName')->willReturn('My shipping method');
        $shipmentMock->expects($this->atLeastOnce())->method('getMethod')->willReturn($shippingMethodMock);
        $shipments = new ArrayCollection([
            $shipmentMock,
        ]);
        $orderMock->expects($this->atLeastOnce())->method('getShipments')->willReturn($shipments);
        $this->assertSame('My shipping method', $this->shippingLineItemNameProvider->getItemName($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testGetItemNameWhenThereIsMultipleShipments(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface&MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface&MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShipmentInterface&MockObject $shipment2Mock */
        $shipment2Mock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface&MockObject $shippingMethod2Mock */
        $shippingMethod2Mock = $this->createMock(ShippingMethodInterface::class);
        $shippingMethodMock->expects($this->atLeastOnce())->method('getName')->willReturn('My shipping method #1');
        $shipmentMock->expects($this->atLeastOnce())->method('getMethod')->willReturn($shippingMethodMock);
        $shippingMethod2Mock->expects($this->atLeastOnce())->method('getName')->willReturn('My shipping method #2');
        $shipment2Mock->expects($this->atLeastOnce())->method('getMethod')->willReturn($shippingMethod2Mock);
        $shipments = new ArrayCollection([
            $shipmentMock,
            $shipment2Mock,
        ]);
        $orderMock->expects($this->atLeastOnce())->method('getShipments')->willReturn($shipments);
        $this->assertSame('My shipping method #1, My shipping method #2', $this->shippingLineItemNameProvider->getItemName($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testGetItemNameWhenThereIsNoShipments(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $shipments = new ArrayCollection();
        $orderMock->expects($this->atLeastOnce())->method('getShipments')->willReturn($shipments);
        $this->assertSame('', $this->shippingLineItemNameProvider->getItemName($orderMock));
    }
}
