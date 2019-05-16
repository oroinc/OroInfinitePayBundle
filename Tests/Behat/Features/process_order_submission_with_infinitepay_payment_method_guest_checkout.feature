@regression
@fixture-OroFlatRateShippingBundle:FlatRateIntegration.yml
@fixture-OroPaymentBundle:ProductsAndShoppingListsForPayments.yml
@skip
# will be implemented in BB-14782
Feature: Process order submission with InfinitePay payment method guest checkout
  In order to be able to purchase products
  As a Guest
  I want to be able to order products with payment via InfinitePay payment system without registration

  Scenario: Feature Background
    Given sessions active:
      | Admin | first_session  |
      | Guest | second_session |

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

  Scenario: Enable guest shopping list setting
    Given I go to System/ Configuration
    And I follow "Commerce/Sales/Shopping List" on configuration sidebar
    And uncheck "Use default" for "Enable guest shopping list" field
    And I check "Enable guest shopping list"
    When I save form
    Then I should see "Configuration saved" flash message
    And the "Enable guest shopping list" checkbox should be checked

  Scenario: Enable guest checkout setting
    Given I follow "Commerce/Sales/Checkout" on configuration sidebar
    And uncheck "Use default" for "Enable Guest Checkout" field
    And I check "Enable Guest Checkout"
    When I save form
    Then the "Enable Guest Checkout" checkbox should be checked

  Scenario: Create Shopping List as unauthorized user
    Given I proceed as the Guest
    And There are products in the system available for order
    And I am on homepage
    When I type "SKU123" in "search"
    And I click "Search Button"
    And I click "product1"
    And I click "Add to Shopping List"
    And I should see "Product has been added to" flash message
    And I click "Shopping List"
    Then I should see "product1"

  Scenario: Unsuccessful order payment with InfinitePay
    Given I click "Create Order"
    And I click "Continue as a Guest"
    And I fill form with:
      | First Name      | Tester1         |
      | Last Name       | Testerson       |
      | Email           | tester@test.com |
      | Street          | Fifth avenue    |
      | City            | Berlin          |
      | Country         | Germany         |
      | State           | Berlin          |
      | Zip/Postal Code | 10115           |
    And I click "Ship to This Address"
    And I click "Continue"
    And I check "Flat Rate" on the "Shipping Method" checkout step and press Continue
    And I fill "InfintePayEmailForm" with:
      | Email | email_for_failure_emulation@test.com |
    And I click "Continue"
    And I click "Submit Order"
    Then I should see "We were unable to process your payment. Please verify your payment information and try again." flash message
    And I click "Flash Message Close Button"

  Scenario: Successful order payment with InfinitePay
    And I fill "InfintePayEmailForm" with:
      | Email | email_for_success_emulation@test.com |
    And I click "Continue"
    And I click "Submit Order"
    Then I see the "Thank You" page with "Thank You For Your Purchase!" title
    When I proceed as the Admin
    And I go to Sales/Orders
    Then I should see Paid in full in grid
