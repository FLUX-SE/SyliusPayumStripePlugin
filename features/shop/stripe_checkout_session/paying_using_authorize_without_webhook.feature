@paying_with_stripe_checkout_session_during_checkout
Feature: Paying with Stripe Checkout Session during checkout
  In order to buy products
  As a Customer
  I want to be able to pay with "Stripe Checkout Session" payment gateway without webhooks

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "john@example.com" identified by "password123"
    And the store has a payment method "Stripe" with a code "stripe" and Stripe Checkout Session payment gateway using authorize
    And the store has also a payment method "Offline" with a code "offline"
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "john@example.com"

  @ui
  Scenario: Successful payment in Stripe without webhooks using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I get redirected to Stripe and complete my payment without webhook using authorize
    Then I should be notified that my payment has been authorized
    And I should see the thank you page

  @ui
  Scenario: Cancelling the payment without webhooks using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again

  @ui
  Scenario: Retrying the payment with success without webhooks using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And I get redirected to Stripe and complete my payment without webhook using authorize
    Then I should be notified that my payment has been authorized
    And I should see the thank you page

  @ui
  Scenario: Retrying the payment and failing without webhooks using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again
