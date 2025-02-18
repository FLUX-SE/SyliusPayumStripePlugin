<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusPayumStripePlugin\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use LogicException;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeCheckoutSessionMocker;
use Tests\FluxSE\SyliusPayumStripePlugin\Behat\Mocker\StripeJsMocker;
use Webmozart\Assert\Assert;

final readonly class CartContext implements Context
{
    public function __construct(private ApiClientInterface $shopClient, private ResponseCheckerInterface $responseChecker, private SharedStorageInterface $sharedStorage, private StripeCheckoutSessionMocker $stripeCheckoutSessionMocker, private StripeJsMocker $stripeJsMocker)
    {
    }

    /**
     * @When /^I see the payment configuration for Stripe Checkout Session$/
     */
    public function iSeeThePaymentConfigurationForStripeCheckoutSession(): void
    {
        $this->stripeCheckoutSessionMocker->mockCaptureOrAuthorize(function () {
            $this->showPaymentConfiguration();
        });
    }

    /**
     * @When /^I see the payment configuration for Stripe JS$/
     */
    public function iSeeThePaymentConfigurationForStripeJs(): void
    {
        $this->stripeJsMocker->mockCaptureOrAuthorize(function () {
            $this->showPaymentConfiguration();
        });
    }

    /**
     * @Then /^I should be able to get "([^"]+)" with value "([^"]+)"$/
     */
    public function iShouldBeAbleToGetWithValue(string $key, string $expectedValue): void
    {
        /** @var Response $response */
        $response = $this->sharedStorage->get('response');
        $value = $this->responseChecker->getValue($response, $key);
        Assert::eq($value, $expectedValue);
    }

    /**
     * @Then /^I should be able to get "([^"]+)" with a boolean value (1|0)$/
     */
    public function iShouldBeAbleToGetWithABooleanValue(string $key, bool $expectedValue): void
    {
        /** @var Response $response */
        $response = $this->sharedStorage->get('response');
        $value = $this->responseChecker->getValue($response, $key);
        Assert::eq($value, $expectedValue);
    }

    public function showPaymentConfiguration(): void
    {
        $tokenValue = $this->getCartTokenValue();

        $this->shopClient->show(
            Resources::ORDERS,
            sprintf(
                '%s/%s/%s/configuration',
                $tokenValue,
                Resources::PAYMENTS,
                $this->getCart()['payments'][0]['id'],
            ),
        );

        $this->sharedStorage->set('response', $this->shopClient->getLastResponse());
    }

    /**
     * @return array{payments: array<int, array{id: int}>}
     */
    private function getCart(): array
    {
        $cart = $this->shopClient->show(Resources::ORDERS, $this->getCartTokenValue());

        /** @var array{payments: array<int, array{id: int}>} $responseContent */
        $responseContent = $this->responseChecker->getResponseContent($cart);

        return $responseContent;
    }

    private function getCartTokenValue(): string
    {
        if ($this->sharedStorage->has('cart_token')) {
            /** @var string $cartToken */
            $cartToken = $this->sharedStorage->get('cart_token');

            return $cartToken;
        }

        throw new LogicException('Unable to find the cart_token inside the shared storage.');
    }
}
