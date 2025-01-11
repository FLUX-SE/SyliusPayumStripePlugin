<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;
use Webmozart\Assert\Assert;

class ManagingPaymentMethodsContext implements Context
{
    public function __construct(
        private readonly CreatePageInterface $createPage,
    ) {
    }

    /**
     * @When I configure it with test stripe gateway data :secretKey and :publishableKey
     */
    public function iConfigureItWithTestStripeGatewayData(string $secretKey, string $publishableKey): void
    {
        $this->createPage->setStripeSecretKey($secretKey);
        $this->createPage->setStripePublishableKey($publishableKey);
    }

    /**
     * @When I add a webhook secret key :webhookKey
     */
    public function iAddAWebhookSecretKey(string $webhookKey): void
    {
        $this->createPage->addStripeWebhookSecretKey($webhookKey);
    }

    /**
     * @When I use authorize
     */
    public function iUseAuthorize(): void
    {
        $this->createPage->setStripeIsAuthorized(true);
    }

    /**
     * @When I don't use authorize
     */
    public function iDontUseAuthorize(): void
    {
        $this->createPage->setStripeIsAuthorized(false);
    }

    /**
     * @Given /^I should see a warning message under the use authorize field$/
     */
    public function iShouldSeeAWarningMessageUnderTheUseAuthorizeField(): void
    {
        Assert::true($this->createPage->isUseAuthorizeWarningMessageDisplayed());
    }

    /**
     * @Given /^I should not see a warning message under the use authorize field$/
     */
    public function iShouldNotSeeAWarningMessageUnderTheUseAuthorizeField(): void
    {
        Assert::false($this->createPage->isUseAuthorizeWarningMessageDisplayed());
    }
}
