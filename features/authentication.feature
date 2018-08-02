Feature: User authentication
  In order to have confidence that WordHat is reliable for developers
  As a WordHat maintainer
  I want to test user authentication

  @javascript
  Scenario: I can log-in and out with javascript
    Given I am logged in as an administrator
    And I am on the dashboard
    Then I should see "Howdy"
    When I log out
    Then I should not see "Howdy"

  Scenario: I can log-in and out without javascript
    Given I am logged in as an administrator
    And I am on the dashboard
    Then I should see "Howdy"
    When I log out
    Then I should not see "Howdy"
