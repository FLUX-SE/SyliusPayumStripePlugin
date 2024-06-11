@managing_orders
Feature: Canceling an authorized order
  In order to cancel an order already authorized
  As an Administrator
  I want to be able to cancel a Stripe authorized order

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Green Arrow"
    And the store ships everywhere for free
    And the store has a payment method "Stripe" with a code "stripe" and Stripe payment gateway using authorize
    And there is a customer "oliver@teamarrow.com" that placed an order "#00000001"
    And the customer bought a single "Green Arrow"
    And the customer chose "Free" shipping method to "United States" with "Stripe" payment
    And this order is already authorized as "pi_123" Stripe payment intent
    And I am logged in as an administrator

  @ui
  Scenario: Cancelling the order with an authorized payment
    Given I am viewing the summary of this order
    And I am prepared to cancel this order
    When I cancel this order
    Then I should be notified that it has been successfully updated
    And it should have payment with state cancelled
    And it should have payment state cancelled
