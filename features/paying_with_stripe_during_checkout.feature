@paying_with_stripe_during_checkout
Feature: Paying with Stripe during checkout
  In order to buy products
  As a Customer
  I want to be able to pay with "Stripe Checkout Session" payment gateway

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "john@example.com" identified by "password123"
    And the store has a payment method "Stripe" with a code "stripe" and Stripe payment gateway
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "john@example.com"

  @ui
  Scenario: Successful payment in Stripe
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I get redirected to Stripe and complete my payment
    Then I should be notified that my payment has been completed