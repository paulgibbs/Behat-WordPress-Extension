Feature: Managing themes
  In order to have confidence that WordHat is reliable for developers
  As a WordHat maintainer
  I want to test managing themes

  Background:
    Given I am logged in as an administrator

  @db
  Scenario: Activate a plugin
    When I switch the theme to "twentysixteen"
    And I am on the dashboard
    Then I should see "Twenty Sixteen" in the "#wp-version a" element
