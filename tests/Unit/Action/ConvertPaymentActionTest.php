<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Action;

use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentAction;
use FluxSE\SyliusPayumStripePlugin\Action\ConvertPaymentActionInterface;
use FluxSE\SyliusPayumStripePlugin\Provider\DetailsProviderInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\Convert;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentActionTest extends TestCase
{
    private MockObject&DetailsProviderInterface $detailsProviderMock;

    private ConvertPaymentAction $convertPaymentAction;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->detailsProviderMock = $this->createMock(DetailsProviderInterface::class);
        $this->convertPaymentAction = new ConvertPaymentAction($this->detailsProviderMock);
    }

    public function testImplementsActionInterface(): void
    {
        $this->assertInstanceOf(ActionInterface::class, $this->convertPaymentAction);
        $this->assertInstanceOf(ConvertPaymentActionInterface::class, $this->convertPaymentAction);
    }

    /**
     * @throws Exception
     */
    public function testExecutes(): void
    {
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);

        $details = [];
        $paymentMock
            ->expects($this->atLeastOnce())
            ->method('getOrder')
            ->willReturn($orderMock)
        ;

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('getSource')
            ->willReturn($paymentMock)
        ;

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('getTo')
            ->willReturn('array')
        ;

        $this->detailsProviderMock
            ->expects($this->atLeastOnce())
            ->method('getDetails')
            ->with($orderMock)
            ->willReturn($details)
        ;

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('setResult')
            ->with($details)
        ;

        $this->convertPaymentAction->execute($requestMock);
    }

    /**
     * @throws Exception
     */
    public function testSupportsOnlyConvertRequestPaymentSourceAndArrayTo(): void
    {
        /** @var Convert&MockObject $requestMock */
        $requestMock = $this->createMock(Convert::class);
        /** @var PaymentInterface&MockObject $paymentMock */
        $paymentMock = $this->createMock(PaymentInterface::class);

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('getSource')
            ->willReturn($paymentMock)
        ;

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('getTo')
            ->willReturn('array')
        ;

        $this->assertTrue($this->convertPaymentAction->supports($requestMock));
    }
}
