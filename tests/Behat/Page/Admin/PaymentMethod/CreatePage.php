<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_webhook_secret_key' => '[data-test-add-webhook-secret-key]',
            'webhook_secret_key' => '[data-test-webhook-secret-key]:contains("%name%")',
            'webhook_secret_key_added' => '[data-test-webhook-secret-key]:last-child input:empty',
            'webhook_secret_key_delete' => '[data-test-webhook-secret-key]:contains("%name%") button[data-test-delete-webhook-secret-key]',
            'webhook_secret_key_last' => '[data-test-webhook-secret-key]:last-child',
            'use_authorize_info' => '[data-test-use-authorize-info]',
            'use_authorize' => '[data-test-use-authorize]',
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
    public function addStripeWebhookSecretKey(string $webhookSecretKey): void
    {
        $this->getElement('add_webhook_secret_key')->click();

        $this->waitForElement(5, 'webhook_secret_key_added');

        $input = $this->getElement('webhook_secret_key_last')->find('css', 'input');

        if (null === $input) {
            throw new ElementNotFoundException($this->getSession(), 'input', 'css', 'input');
        }

        $input->setValue($webhookSecretKey);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function setStripeIsAuthorized(bool $isAuthorized): void
    {
        if ($isAuthorized) {
            $this->getElement('use_authorize')->check();
        } else {
            $this->getElement('use_authorize')->uncheck();
        }

        sleep(1);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function isUseAuthorizeWarningMessageDisplayed(): bool
    {
        return $this->getElement('use_authorize_info')->isVisible();
    }

    /**
     * @param array<mixed> $parameters
     */
    private function waitForElement(
        int $timeout,
        string $elementName,
        array $parameters = [],
        bool $shouldExist = true,
    ): void {
        $this->getDocument()->waitFor(
            $timeout,
            fn (): bool => $shouldExist && $this->hasElement($elementName, $parameters),
        );
    }
}
