Feature: Miscellanous/unsorted wp-admin tests.
  In order to have confidence that WordHat is reliable for developers
  As a WordHat maintainer
  I want to test interacting with the dashboard

  Scenario: I can navigate to the Add New Users screen in the WordPress dashboard.
    Given I am logged in as an administrator
    And I am on the dashboard
    And I go to the "Users" menu
    When I click on the "Add New" link in the header
    Then I should be on the "Add New User" screen
