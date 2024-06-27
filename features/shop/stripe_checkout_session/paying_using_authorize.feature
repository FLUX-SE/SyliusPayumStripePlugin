@paying_with_stripe_checkout_session_during_checkout
Feature: Paying with Stripe Checkout Session during checkout using authorized
  In order to buy products
  As a Customer
  I want to be able to pay with "Stripe Checkout Session" payment gateway

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "john@example.com" identified by "password123"
    And the store has a payment method "Stripe" with a code "stripe" and Stripe Checkout Session payment gateway using authorize
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "john@example.com"

  @ui
  Scenario: Successful payment in Stripe using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I get redirected to Stripe and complete my payment using authorize
    Then I should be notified that my payment has been authorized
    And I should see the thank you page

  @ui
  Scenario: Cancelling the payment using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    When I confirm my order with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again

  @ui
  Scenario: Retrying the payment with success using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And I get redirected to Stripe and complete my payment using authorize
    Then I should be notified that my payment has been authorized
    And I should see the thank you page

  @ui
  Scenario: Retrying the payment and failing using authorize
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Stripe" payment method
    And I have confirmed my order with Stripe payment
    But I have clicked on "go back" during my Stripe payment
    When I try to pay again with Stripe payment
    And I click on "go back" during my Stripe payment
    Then I should be able to pay again
