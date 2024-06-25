@managing_payment_methods
Feature: Adding a new Stripe JS payment method
  In order to allow payment for orders, using the Stripe gateway
  As an Administrator
  I want to add new payment methods to the system

  Background:
    Given the store operates on a single channel in "United States"
    And I am logged in as an administrator

  @ui @javascript
  Scenario: Adding a new stripe payment method using authorize
    Given I want to create a new Stripe JS payment method
    When I name it "Stripe JS" in "English (United States)"
    And I specify its code as "stripe_sca_test"
    And I configure it with test stripe gateway data "TEST", "TEST"
    And I add a webhook secret key "TEST"
    And I use authorize
    And I add it
    Then I should be notified that it has been successfully created
    And I should see a warning message under the use authorize field
    And the payment method "Stripe JS" should appear in the registry

  @ui @javascript
  Scenario: Adding a new stripe payment method not using authorize
    Given I want to create a new Stripe JS payment method
    When I name it "Stripe JS" in "English (United States)"
    And I specify its code as "stripe_sca_test"
    And I configure it with test stripe gateway data "TEST", "TEST"
    And I add a webhook secret key "TEST"
    And I don't use authorize
    And I add it
    Then I should be notified that it has been successfully created
    And I shouldn't see a warning message under the use authorize field
    And the payment method "Stripe JS" should appear in the registry
