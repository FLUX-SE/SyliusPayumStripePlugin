@paying_with_stripe_js_during_checkout
Feature: Paying with Stripe JS during checkout without webhook
  In order to buy products
  As a Customer
  I want to be able to pay with "Stripe JS" payment gateway without webhooks

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "john@example.com" identified by "password123"
    And the store has a payment method "Stripe" with a code "stripe" and Stripe JS payment gateway
    And the store has also a payment method "Offline" with a code "offline"
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "john@example.com"

  @ui
  Scenario: Successful payment in Stripe without webhooks
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And The Stripe JS form is displayed and I complete the payment without webhook
    Then I should be notified that my payment has been completed
    And I should see the thank you page

  @ui
  Scenario: Cancelling the payment without webhooks
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again

  @ui
  Scenario: Retrying the payment with success without webhooks
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And The Stripe JS form is displayed and I complete the payment without webhook
    Then I should be notified that my payment has been completed
    And I should see the thank you page

  @ui
  Scenario: Retrying the payment and failing without webhooks
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again
