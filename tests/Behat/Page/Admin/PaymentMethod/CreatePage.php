<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /** @var int */
    private $webhookSecretKeysListIndex = 0;

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'webhook_secret_keys_list_element' => '#sylius_payment_method_gatewayConfig_config_webhook_secret_keys_%index%',
            'use_authorize_info' => '#sylius_payment_method_gatewayConfig_config_use_authorize_info',
        ]);
    }

    public function setStripeSecretKey(string $secretKey): void
    {
        $this->getDocument()->fillField('Secret key', $secretKey);
    }

    public function setStripePublishableKey(string $publishableKey): void
    {
        $this->getDocument()->fillField('Publishable key', $publishableKey);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setStripeWebhookSecretKey(string $webhookSecretKey): void
    {
        $this->getDocument()->clickLink('Add');
        $this
            ->getElement('webhook_secret_keys_list_element', [
                '%index%' => $this->webhookSecretKeysListIndex,
            ])
            ->setValue($webhookSecretKey)
        ;
        ++$this->webhookSecretKeysListIndex;
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setStripeIsAuthorized(bool $isAuthorized): void
    {
        if ($isAuthorized) {
            $this->getDocument()->checkField('Use authorize');
        } else {
            $this->getDocument()->uncheckField('Use authorize');
        }
    }

    /**
     * @throws ElementNotFoundException
     */
    public function isUseAuthorizeWarningMessageDisplayed(): bool
    {
        return $this->getElement('use_authorize_info')->isVisible();
    }
}
