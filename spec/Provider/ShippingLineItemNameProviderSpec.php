<?php

declare(strict_types=1);

namespace spec\FluxSE\SyliusPayumStripePlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\ShippingLineItemNameProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ShippingLineItemNameProviderSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ShippingLineItemNameProvider::class);
        $this->shouldHaveType(ShippingLineItemNameProviderInterface::class);
    }

    public function it_get_item_name(
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod
    ): void {
        $shippingMethod->getName()->willReturn('My shipping method');
        $shipment->getMethod()->willReturn($shippingMethod);
        $shipments = new ArrayCollection([
            $shipment->getWrappedObject(),
        ]);
        $order->getShipments()->willReturn($shipments);

        $this->getItemName($order)->shouldReturn('My shipping method');
    }

    public function it_get_item_name_when_there_is_multiple_shipments(
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ShipmentInterface $shipment2,
        ShippingMethodInterface $shippingMethod2
    ): void {
        $shippingMethod->getName()->willReturn('My shipping method #1');
        $shipment->getMethod()->willReturn($shippingMethod);
        $shippingMethod2->getName()->willReturn('My shipping method #2');
        $shipment2->getMethod()->willReturn($shippingMethod2);
        $shipments = new ArrayCollection([
            $shipment->getWrappedObject(),
            $shipment2->getWrappedObject(),
        ]);
        $order->getShipments()->willReturn($shipments);

        $this->getItemName($order)->shouldReturn('My shipping method #1, My shipping method #2');
    }

    public function it_get_item_name_when_there_is_no_shipments(
        OrderInterface $order
    ): void {
        $shipments = new ArrayCollection();
        $order->getShipments()->willReturn($shipments);

        $this->getItemName($order)->shouldReturn('');
    }
}
