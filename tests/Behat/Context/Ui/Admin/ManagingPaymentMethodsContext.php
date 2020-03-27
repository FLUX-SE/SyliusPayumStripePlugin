<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\Admin\PaymentMethod\CreatePageInterface;

class ManagingPaymentMethodsContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    public function __construct(CreatePageInterface $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @Given /^I want to create a new Stripe payment method$/
     *
     * @throws UnexpectedPageException
     */
    public function iWantToCreateANewStripePaymentMethod(): void
    {
        $this->createPage->open(['factory' => 'stripe_checkout_session']);
    }

    /**
     * @When I configure it with test stripe gateway data with a webhook secret key
     */
    public function iConfigureItWithTestStripeGatewayDataWithAWebhookSecretKey()
    {
        $this->createPage->setStripeSecretKey('TEST');
        $this->createPage->setStripePublishableKey('TEST');
        $this->createPage->setStripeWebhookSecretKey('TEST');
    }
}
