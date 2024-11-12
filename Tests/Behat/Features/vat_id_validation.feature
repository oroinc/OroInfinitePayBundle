@regression
@fixture-OroCustomerBundle:CustomerUserFixture.yml
Feature: VAT Id Validation
  In order to manage Customer Addresses
  As an Administrator
  I should have validation for the existence of the VAT Id value for the assigned Customer

  Scenario: Customer Address validation on backend for EU countries
    Given I login as administrator
    When I go to Customers / Customers
    And I click "Create Customer"
    And I fill form with:
      | Name            | My new customer |
      | Organization    | My organization |
      | Country         | Germany         |
      | State           | Berlin          |
      | City            | Berlin          |
      | Street          | Some street     |
      | Zip/Postal Code | 555             |
      | Types           | Billing         |
    And I save form
    Then I should see "VAT Id is required to define an EU billing address" error message
    When I fill form with:
      | VAT Id | MYVATID |
    And I save form
    Then I should see "Customer has been saved" flash message

  Scenario: Customer Address validation on frontend for EU countries
    Given I signed in as AmandaRCole@example.org on the store frontend
    And I click "Account Dropdown"
    And click "Address Book"
    And click "New Company Address"
    And I fill form with:
      | Country         | Germany     |
      | State           | Berlin      |
      | City            | Berlin      |
      | Street          | Some street |
      | Zip/Postal Code | 555         |
      | Billing         | 1           |
    And I save form
    Then I should see "Customer Address has been saved" flash message
    And I should not see "VAT Id is required to define an EU billing address"
