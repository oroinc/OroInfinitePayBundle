@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroAuthorizeNetBundle:AuthorizeNetFixture.yml
Feature: Process order submission with InfinitePay payment method single page checkout
  In order to be able to purchase products
  As a Customer
  I want to be able to order products with payment via InfinitePay payment system using single page checkout

  Scenario: Create different window session
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
      | Name              | InfinitePay           |
      | Label             | InfinitePay           |
      | Short Label       | InfinitePay           |
      | Client Reference  | test client reference |
      | Username          | test username         |
      | Password          | test password         |
      | Secret            | test secret           |
      | Auto-Capture      | true                  |
      | Auto-Activation   | true                  |
      | Test Mode         | true                  |
    And I save and close form
    Then I should see "Integration saved" flash message
    And I should see InfinitePay in grid

  Scenario: Create new Payment Rule for InfinitePay integration
    Given I go to System/Payment Rules
    And I click "Create Payment Rule"
    And I fill form with:
      | Name       | InfinitePay  |
      | Enabled    | true         |
      | Sort Order | 1            |
      | Method     | InfinitePay  |
    And I press "Add Method Button"
    When I save and close form
    Then I should see "Payment rule has been saved" flash message

  Scenario: Enable SinglePage checkout
    Given go to System/Workflows
    When I click "Activate" on row "Single Page Checkout" in grid
    And I click "Activate"
    Then I should see "Workflow activated" flash message

  Scenario: Unsuccessful order payment with InfinitePay
    Given I proceed as the User
    And There are products in the system available for order
    And I signed in as AmandaRCole@example.org on the store frontend
    When I open page with shopping list List 1
    And I click "Create Order"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Billing Address"
    And I select "Fifth avenue, 10115 Berlin, Germany" from "Select Shipping Address"
    And I check "Flat Rate" on the checkout page
    And I fill "InfintePayEmailForm" with:
      | Email   | email_for_failure_emulation@test.com |
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message
    And I click "Flash Message Close Button"

  Scenario: Successful order payment with InfinitePay
    And I fill "InfintePayEmailForm" with:
      | Email   | email_for_success_emulation@test.com |
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    Then I should see Paid in full in grid
