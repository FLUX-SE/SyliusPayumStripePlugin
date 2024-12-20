<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use RuntimeException;
use Stripe\Event;
use Stripe\PaymentIntent;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeJsMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Page\External\StripePage;

class StripeJsShopContext extends MinkContext implements Context
{
    public function __construct(private readonly StripeJsMocker $stripeJsMocker, private readonly CompletePageInterface $summaryPage, private readonly ShowPageInterface $orderDetails, private readonly StripePage $paymentPage)
    {
    }

    /**
     * @When The Stripe JS form is displayed and I complete the payment
     */
    public function theStripeJsFormIsDisplayedAndICompleteThePayment(): void
    {
        $this->stripeJsMocker->mockSuccessfulPayment(
            function () {
                $jsonEvent = [
                    'id' => 'evt_1',
                    'type' => Event::PAYMENT_INTENT_SUCCEEDED,
                    'object' => 'event',
                    'data' => [
                        'object' => [
                            'id' => 'pi_1',
                            'object' => PaymentIntent::OBJECT_NAME,
                            'capture_method' => PaymentIntent::CAPTURE_METHOD_AUTOMATIC,
                            'metadata' => [
                                'token_hash' => '%s',
                            ],
                        ],
                    ],
                ];
                $payload = json_encode($jsonEvent, \JSON_THROW_ON_ERROR);

                $this->paymentPage->notify($payload);
            },
            function () {
                $this->paymentPage->captureOrAuthorizeThenGoToAfterUrl();
            },
        );
    }

    /**
     * @When The Stripe JS form is displayed and I complete the payment using authorize
     */
    public function theStripeJsFormIsDisplayedAndICompleteThePaymentUsingAuthorize(): void
    {
        $this->stripeJsMocker->mockAuthorizePayment(
            function () {
                $jsonEvent = [
                    'id' => 'evt_1',
                    'type' => Event::PAYMENT_INTENT_SUCCEEDED,
                    'object' => 'event',
                    'data' => [
                        'object' => [
                            'id' => 'pi_1',
                            'object' => PaymentIntent::OBJECT_NAME,
                            'capture_method' => PaymentIntent::CAPTURE_METHOD_MANUAL,
                            'metadata' => [
                                'token_hash' => '%s',
                            ],
                        ],
                    ],
                ];
                $payload = json_encode($jsonEvent, \JSON_THROW_ON_ERROR);

                $this->paymentPage->notify($payload);
            },
            function () {
                $this->paymentPage->captureOrAuthorizeThenGoToAfterUrl();
            },
        );
    }

    /**
     * @When The Stripe JS form is displayed and I complete the payment without webhook
     */
    public function theStripeJsFormIsDisplayedAndICompleteThePaymentWithoutWebhooks(): void
    {
        $this->stripeJsMocker->mockSuccessfulPaymentWithoutWebhook(function () {
            $this->paymentPage->captureOrAuthorizeThenGoToAfterUrl();
        });
    }

    /**
     * @When The Stripe JS form is displayed and I complete the payment without webhook using authorize
     */
    public function theStripeJsFormIsDisplayedAndICompleteThePaymentWithoutWebhookUsingAuthorize(): void
    {
        $this->stripeJsMocker->mockSuccessfulPaymentWithoutWebhookUsingAuthorize(function () {
            $this->paymentPage->captureOrAuthorizeThenGoToAfterUrl();
        });
    }

    /**
     * @Given I have confirmed my order with Stripe payment
     * @When I confirm my order with Stripe payment
     */
    public function iConfirmMyOrderWithStripePayment(): void
    {
        $this->stripeJsMocker->mockCaptureOrAuthorize(function () {
            $this->summaryPage->confirmOrder();
        });
    }

    /**
     * @Given I have clicked on "go back" during my Stripe payment
     * @When I click on "go back" during my Stripe payment
     */
    public function iClickOnGoBackDuringMyStripePayment(): void
    {
        $this->stripeJsMocker->mockGoBackPayment(function () {
            $this->paymentPage->captureOrAuthorizeThenGoToAfterUrl();
        });
    }

    /**
     * @When I try to pay again with Stripe payment
     */
    public function iTryToPayAgainWithStripePayment(): void
    {
        $this->stripeJsMocker->mockCaptureOrAuthorize(function () {
            $this->orderDetails->pay();
        });
    }

    /**
     * @Then I should be notified that my payment has been authorized
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenAuthorized(): void
    {
        $this->assertNotification('Payment has been authorized.');
    }

    private function assertNotification(string $expectedNotification): void
    {
        $notifications = $this->orderDetails->getNotifications();
        $hasNotifications = '';

        foreach ($notifications as $notification) {
            $hasNotifications .= $notification;
            if ($notification === $expectedNotification) {
                return;
            }
        }

        throw new RuntimeException(sprintf('There is no notification with "%s". Got "%s"', $expectedNotification, $hasNotifications));
    }
}
