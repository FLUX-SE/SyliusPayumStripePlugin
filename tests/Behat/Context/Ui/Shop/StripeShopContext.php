<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Stripe\Checkout\Session;
use Stripe\Event;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeCheckoutSessionMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\External\StripeCheckoutPage;

class StripeShopContext extends MinkContext implements Context
{
    /** @var StripeCheckoutSessionMocker */
    private $stripeSessionCheckoutMocker;

    /** @var CompletePageInterface */
    private $summaryPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    /** @var StripeCheckoutPage */
    private $paymentPage;

    public function __construct(
        StripeCheckoutSessionMocker $stripeSessionCheckoutMocker,
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
     * @When I confirm my order with Stripe payment
     * @Given I have confirmed my order with Stripe payment
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
        $this->stripeSessionCheckoutMocker->mockSuccessfulPayment(
            function () {
                $jsonEvent = [
                    'id' => 'evt_00000000000000',
                    'type' => Event::CHECKOUT_SESSION_COMPLETED,
                    'object' => 'event',
                    'data' => [
                        'object' => [
                            'id' => 'cs_00000000000000',
                            'object' => Session::OBJECT_NAME,
                            'payment_intent' => 'pi_00000000000000',
                            'metadata' => [
                                'token_hash' => '%s',
                            ],
                        ],
                    ],
                ];
                $payload = json_encode($jsonEvent);

                $this->paymentPage->notify($payload);
            },
            function () {
                $this->paymentPage->captureAndAfterPay();
            }
        );
    }

    /**
     * @When I get redirected to Stripe and complete my payment without webhooks
     */
    public function iGetRedirectedToStripeWithoutWebhooks(): void
    {
        $this->stripeSessionCheckoutMocker->mockSuccessfulPaymentWithoutWebhooks(function () {
            $this->paymentPage->captureAndAfterPay();
        });
    }

    /**
     * @Given I have clicked on "go back" during my Stripe payment
     * @When I click on "go back" during my Stripe payment
     */
    public function iClickOnGoBackDuringMyStripePayment()
    {
        $this->stripeSessionCheckoutMocker->mockGoBackPayment(function () {
            $this->paymentPage->captureAndAfterPay();
        });
    }

    /**
     * @When I try to pay again with Stripe payment
     */
    public function iTryToPayAgainWithStripePayment(): void
    {
        $this->stripeSessionCheckoutMocker->mockCreatePayment(function () {
            $this->orderDetails->pay();
        });
    }
}
