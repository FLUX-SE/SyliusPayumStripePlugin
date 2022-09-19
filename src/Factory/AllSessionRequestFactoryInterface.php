<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use FluxSE\PayumStripe\Request\Api\Resource\AllInterface;

interface AllSessionRequestFactoryInterface
{
    public function createNew(): AllInterface;
}
