@stripe_js_payment_configuration
Feature: Paying with Stripe JS during checkout
  In order to buy products
  As a Customer
  I want to be able to pay with "Stripe JS" payment gateway
  I want to be able to use the payment configuration endpoint

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "john@example.com" identified by "password123"
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "john@example.com"

  @api
  Scenario: Getting payment configuration
    Given the store has a payment method "Stripe" with a code "stripe" and Stripe JS payment gateway without using authorize
    And I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I see the payment configuration for Stripe JS
    Then I should be able to get "publishable_key" with value "pk_test_publishablekey"
    And I should be able to get "use_authorize" with a boolean value 0
    And I should be able to get "stripe_payment_intent_client_secret" with value "1234567890"

  @api
  Scenario: Getting payment configuration using authorize
    Given the store has a payment method "Stripe authorize" with a code "stripe_authorize" and Stripe JS payment gateway using authorize
    And I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe authorize" payment method
    When I see the payment configuration for Stripe JS
    Then I should be able to get "publishable_key" with value "pk_test_publishablekey"
    And I should be able to get "use_authorize" with a boolean value 1
    And I should be able to get "stripe_payment_intent_client_secret" with value "1234567890"
