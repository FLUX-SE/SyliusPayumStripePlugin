<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\External\StripeCheckoutPage;

class StripeShopContext extends MinkContext implements Context
{
    /** @var CompletePageInterface */
    private $summaryPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    /** @var StripeCheckoutPage */
    private $paymentPage;

    public function __construct(
        CompletePageInterface $summaryPage,
        ShowPageInterface $orderDetails,
        StripeCheckoutPage $paymentPage
    ) {
        $this->summaryPage = $summaryPage;
        $this->orderDetails = $orderDetails;
        $this->paymentPage = $paymentPage;
    }

    /**
     * @When /^I confirm my order with Stripe payment$/
     */
    public function iConfirmMyOrderWithStripePayment()
    {
        $this->summaryPage->confirmOrder();
    }

    /**
     * @When I get redirected to Stripe and complete my payment
     */
    public function iGetRedirectedToStripe(): void
    {
        $data = [
        ];

        $this->paymentPage->notify($data);
        $this->paymentPage->pay($data);
    }
}
