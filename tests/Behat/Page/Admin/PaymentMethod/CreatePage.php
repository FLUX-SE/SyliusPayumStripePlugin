<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'webhook_secret_key_add' => '#sylius_payment_method_gatewayConfig_config_webhook_secret_keys a[data-form-collection="add"]',
            'webhook_secret_key_0' => '#sylius_payment_method_gatewayConfig_config_webhook_secret_keys_0',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ElementNotFoundException
     */
    public function setStripeWebhookSecretKey(string $webhookSecretKey): void
    {
        $this->getDocument()->clickLink('webhook_secret_key_add');
        $this->getDocument()->fillField('webhook_secret_key_0', $webhookSecretKey);
    }
}
