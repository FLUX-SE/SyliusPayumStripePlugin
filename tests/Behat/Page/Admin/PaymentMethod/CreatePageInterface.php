<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\PaymentMethod\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function setStripeSecretKey(string $secretKey): void;

    public function setStripePublishableKey(string $publishableKey): void;

    public function setStripeWebhookSecretKey(string $webhookSecretKey): void;

    public function setStripeIsAuthorized(bool $isAuthorized): void;

    public function isUseAuthorizeWarningMessageDisplayed(): bool;
}
