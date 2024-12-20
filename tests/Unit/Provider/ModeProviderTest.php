<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Unit\Provider;

use FluxSE\SyliusPayumStripePlugin\Provider\ModeProvider;
use FluxSE\SyliusPayumStripePlugin\Provider\ModeProviderInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stripe\Checkout\Session;
use Sylius\Component\Core\Model\OrderInterface;

final class ModeProviderTest extends TestCase
{
    private ModeProvider $modeProvider;

    protected function setUp(): void
    {
        $this->modeProvider = new ModeProvider();
    }

    public function testInitializable(): void
    {
        $this->assertInstanceOf(ModeProvider::class, $this->modeProvider);
        $this->assertInstanceOf(ModeProviderInterface::class, $this->modeProvider);
    }

    /**
     * @throws Exception
     */
    public function testGetPaymentMethodTypes(): void
    {
        /** @var OrderInterface&MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $this->assertSame(Session::MODE_PAYMENT, $this->modeProvider->getMode($orderMock));
    }
}
