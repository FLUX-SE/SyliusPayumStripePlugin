<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CustomerEmailProviderTest extends TestCase
{
    private CustomerEmailProvider $customerEmailProvider;

    protected function setUp(): void
    {
        $this->customerEmailProvider = new CustomerEmailProvider();
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(CustomerEmailProvider::class, $this->customerEmailProvider);
        $this->assertInstanceOf(CustomerEmailProviderInterface::class, $this->customerEmailProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetCustomerEmailFromACustomer(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface&MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $orderMock->expects($this->once())->method('getCustomer')->willReturn($customerMock);
        $customerMock->expects($this->once())->method('getEmail')->willReturn('customer@domain.tld');
        $this->assertSame('customer@domain.tld', $this->customerEmailProvider->getCustomerEmail($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testCouldReturnNull(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $orderMock->expects($this->once())->method('getCustomer')->willReturn(null);
        $this->assertNull($this->customerEmailProvider->getCustomerEmail($orderMock));
    }
}
