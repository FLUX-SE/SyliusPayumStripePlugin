<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;

interface CancelPaymentIntentRequestFactoryInterface
{
    public function createNew(string $id): CustomCallInterface;
}
