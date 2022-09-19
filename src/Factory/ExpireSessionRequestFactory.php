<?php

declare(strict_types=1);

namespace FluxSE\SyliusPayumStripePlugin\Factory;

use FluxSE\PayumStripe\Request\Api\Resource\CustomCallInterface;
use FluxSE\PayumStripe\Request\Api\Resource\ExpireSession;

final class ExpireSessionRequestFactory implements ExpireSessionRequestFactoryInterface
{
    public function createNew(string $id): CustomCallInterface
    {
        return new ExpireSession($id);
    }
}
