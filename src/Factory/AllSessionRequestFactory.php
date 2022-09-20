<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use FluxSE\PayumStripe\Request\Api\Resource\AllInterface;
use FluxSE\PayumStripe\Request\Api\Resource\AllSession;

final class AllSessionRequestFactory implements AllSessionRequestFactoryInterface
{
    public function createNew(): AllInterface
    {
        return new AllSession();
    }
}
