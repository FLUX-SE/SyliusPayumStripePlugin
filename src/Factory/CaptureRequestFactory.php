<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

final class CaptureRequestFactory implements ModelAggregateFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): ModelAggregateInterface
    {
        return new Capture($token);
    }
}
