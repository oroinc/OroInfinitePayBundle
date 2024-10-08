@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroCheckoutBundle:Shipping.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml
@behat-test-env
Feature: Process order submission with InfinitePay payment method single page checkout
  In order to be able to purchase products
  As a Customer
  I want to be able to order products with payment via InfinitePay payment system using single page checkout

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | User  | second_session |

  Scenario: Create new InfinitePay Integration
    Given I proceed as the Admin
    And I login as administrator
    And I go to System/Integrations/Manage Integrations
    And I click "Create Integration"
    And I select "Infinite Pay" from "Type"
    And I fill "InfinitePay Form" with:
      | Name             | InfinitePay           |
      | Label            | InfinitePay           |
      | Short Label      | InfinitePay           |
      | Client Reference | test client reference |
      | Username         | test username         |
      | Password         | test password         |
      | Secret           | test secret           |
      | Auto-Capture     | true                  |
      | Auto-Activation  | true                  |
      | Test Mode        | true                  |
    When I save and close form
    Then I should see "Integration saved" flash message
    And I should see InfinitePay in grid
    And I create payment rule with "InfinitePay" payment method

  Scenario: Unsuccessful order payment with InfinitePay
    Given I activate "Single Page Checkout" workflow
    And I proceed as the User
    And There are products in the system available for order
    And I signed in as AmandaRCole@example.org on the store frontend
    When I open page with shopping list List 1
    And I click "Create Order"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Billing Address"
    And I select "ORO, Fifth avenue, 10115 Berlin, Germany" from "Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I fill "InfintePayEmailForm" with:
      | Email | email_for_failure_emulation@test.com |
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message
    And I click "Flash Message Close Button"

  Scenario: Successful order payment with InfinitePay
    Given I fill "InfintePayEmailForm" with:
      | Email | email_for_success_emulation@test.com |
    When I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    Then I should see Paid in full in grid
