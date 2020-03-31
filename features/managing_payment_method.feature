@managing_payment_method
Feature: Adding a new Stripe payment method
  In order to allow payment for orders, using the Stripe gateway
  As an Administrator
  I want to add new payment methods to the system

  Background:
    Given the store operates on a channel named "US" in "USD" currency
    And I am logged in as an administrator

  @ui @javascript
  Scenario: Adding a new stripe payment method
    Given I want to create a new Stripe payment method
    When I name it "Stripe Checkout Session" in "English (United States)"
    And I specify its code as "stripe_sca_test"
    And I configure it with test stripe gateway data with a webhook secret key
    And I add it
    Then I should be notified that it has been successfully created
    And the payment method "Stripe Checkout Session" should appear in the registry