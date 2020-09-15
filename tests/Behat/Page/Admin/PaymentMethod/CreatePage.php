<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    private $webhookSecretKeysListIndex = 0;

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'webhook_secret_keys_list_element' => '#sylius_payment_method_gatewayConfig_config_webhook_secret_keys_%index%',
        ]);
    }

    /**
     * {@inheritdoc}
     *
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
}
