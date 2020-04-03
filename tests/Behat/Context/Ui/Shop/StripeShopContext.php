<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Mocker\StripeSessionCheckoutMocker;
use Tests\Prometee\SyliusPayumStripeCheckoutSessionPlugin\Behat\Page\External\StripeCheckoutPage;

class StripeShopContext extends MinkContext implements Context
{
    /** @var StripeSessionCheckoutMocker */
    private $stripeSessionCheckoutMocker;

    /** @var CompletePageInterface */
    private $summaryPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    /** @var StripeCheckoutPage */
    private $paymentPage;

    public function __construct(
        StripeSessionCheckoutMocker $stripeSessionCheckoutMocker,
        CompletePageInterface $summaryPage,
        ShowPageInterface $orderDetails,
        StripeCheckoutPage $paymentPage
    ) {
        $this->stripeSessionCheckoutMocker = $stripeSessionCheckoutMocker;
        $this->summaryPage = $summaryPage;
        $this->orderDetails = $orderDetails;
        $this->paymentPage = $paymentPage;
    }

    /**
     * @When /^I confirm my order with Stripe payment$/
     */
    public function iConfirmMyOrderWithStripePayment()
    {
        $this->stripeSessionCheckoutMocker->mockCreatePayment(function () {
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @When I get redirected to Stripe and complete my payment
     */
    public function iGetRedirectedToStripe(): void
    {
        $this->stripeSessionCheckoutMocker->mockSuccessfulPayment(function () {
            $jsonEvent = [
                'id' => 'evt_00000000000000',
                'type' => 'checkout.session.completed',
                'object' => 'event',
                'data' => [
                    'object' => [
                        'id' => 'cs_00000000000000',
                        'object' => 'checkout.session',
                        'payment_intent' => 'pi_00000000000000',
                        'metadata' => [
                            'token_hash' => '%s',
                        ],
                    ],
                ],
            ];
            $payload = json_encode($jsonEvent);

            $this->paymentPage->notify($payload);
        });

        $this->stripeSessionCheckoutMocker->mockSuccessfulPayment(function () {
            $this->paymentPage->capture();
        });
    }
}
