@managing_orders
Feature: Capturing the authorization of an order with Stripe Checkout Session
  In order to complete a payment
  As an Administrator
  I want to be able to capture the authorization of a Stripe paid order

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Green Arrow"
    And the store ships everywhere for free
    And the store has a payment method "Stripe" with a code "stripe" and Stripe Checkout Session payment gateway using authorize
    And there is a customer "oliver@teamarrow.com" that placed an order "#00000001"
    And the customer bought a single "Green Arrow"
    And the customer chose "Free" shipping method to "United States" with "Stripe" payment
    And this order is already authorized as "pi_123" Stripe payment intent
    And I am logged in as an administrator

  @ui
  Scenario: Initializing the Stripe refund
    Given I am viewing the summary of this order
    And I am prepared to capture authorization of this order
    When I mark this order as paid
    Then I should be notified that the order's payment has been successfully completed
    And it should have payment with state completed
    And it should have payment state completed
