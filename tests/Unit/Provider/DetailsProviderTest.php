<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\CustomerEmailProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\LineItemsProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\ModeProviderInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\PaymentMethodTypesProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Stripe\Checkout\Session;
use Sylius\Component\Core\Model\OrderInterface;

final class DetailsProviderTest extends \PHPUnit\Framework\TestCase
{
    private CustomerEmailProviderInterface&MockObject $customerEmailProviderMock;

    private LineItemsProviderInterface&MockObject $lineItemsProviderMock;

    private PaymentMethodTypesProviderInterface&MockObject $paymentMethodTypesProviderMock;

    private ModeProviderInterface&MockObject $modeProviderMock;

    private DetailsProvider $detailsProvider;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->customerEmailProviderMock = $this->createMock(CustomerEmailProviderInterface::class);
        $this->lineItemsProviderMock = $this->createMock(LineItemsProviderInterface::class);
        $this->paymentMethodTypesProviderMock = $this->createMock(PaymentMethodTypesProviderInterface::class);
        $this->modeProviderMock = $this->createMock(ModeProviderInterface::class);
        $this->detailsProvider = new DetailsProvider($this->customerEmailProviderMock, $this->lineItemsProviderMock, $this->paymentMethodTypesProviderMock, $this->modeProviderMock);
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(DetailsProvider::class, $this->detailsProvider);
        $this->assertInstanceOf(DetailsProviderInterface::class, $this->detailsProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetFullDetails(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->customerEmailProviderMock->expects(self::atLeastOnce())->method('getCustomerEmail')->with($orderMock)->willReturn('customer@domain.tld');
        $this->lineItemsProviderMock->expects(self::atLeastOnce())->method('getLineItems')->with($orderMock)->willReturn([]);
        $this->paymentMethodTypesProviderMock->expects(self::atLeastOnce())->method('getPaymentMethodTypes')->with($orderMock)->willReturn(['card']);
        $this->modeProviderMock->expects(self::atLeastOnce())->method('getMode')->with($orderMock)->willReturn(Session::MODE_PAYMENT);
        $this->assertSame([
            'customer_email' => 'customer@domain.tld',
            'mode' => Session::MODE_PAYMENT,
            'line_items' => [],
            'payment_method_types' => ['card'],
        ], $this->detailsProvider->getDetails($orderMock));
    }

    /**
     * @throws Exception
     */
    public function testGetMinimumDetails(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->customerEmailProviderMock->expects(self::atLeastOnce())->method('getCustomerEmail')->with($orderMock)->willReturn(null);
        $this->lineItemsProviderMock->expects(self::atLeastOnce())->method('getLineItems')->with($orderMock)->willReturn(null);
        $this->paymentMethodTypesProviderMock->expects(self::atLeastOnce())->method('getPaymentMethodTypes')->with($orderMock)->willReturn([]);
        $this->modeProviderMock->expects(self::atLeastOnce())->method('getMode')->with($orderMock)->willReturn(Session::MODE_PAYMENT);
        $this->assertSame([
            'mode' => Session::MODE_PAYMENT,
        ], $this->detailsProvider->getDetails($orderMock));
    }
}
