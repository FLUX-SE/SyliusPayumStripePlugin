<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;
use Webmozart\Assert\Assert;

class ManagingPaymentMethodsContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    public function __construct(CreatePageInterface $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @Given /^I want to create a new Stripe Checkout Session payment method$/
     *
     * @throws UnexpectedPageException
     */
    public function iWantToCreateANewStripeCheckoutSessionPaymentMethod(): void
    {
        $this->createPage->open(['factory' => 'stripe_checkout_session']);
    }

    /**
     * @Given /^I want to create a new Stripe JS payment method$/
     *
     * @throws UnexpectedPageException
     */
    public function iWantToCreateANewStripeJsPaymentMethod(): void
    {
        $this->createPage->open(['factory' => 'stripe_js']);
    }

    /**
     * @When I configure it with test stripe gateway data :secretKey, :publishableKey
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
        $this->createPage->setStripeWebhookSecretKey($webhookKey);
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
     * @Given /^I shouldn't see a warning message under the use authorize field$/
     */
    public function iShouldntSeeAWarningMessageUnderTheUseAuthorizeField(): void
    {
        Assert::false($this->createPage->isUseAuthorizeWarningMessageDisplayed());
    }
}
