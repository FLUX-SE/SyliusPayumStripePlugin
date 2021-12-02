<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use FluxSE\PayumStripe\Request\Api\Resource\CancelPaymentIntent;
use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;

final class CancelPaymentIntentRequestFactory implements CancelPaymentIntentRequestFactoryInterface
{
    public function createNew(string $id): CustomCallInterface
    {
        return new CancelPaymentIntent($id);
    }
}
