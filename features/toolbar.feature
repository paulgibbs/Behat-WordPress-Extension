Feature: Toolbar

  Background:
    Given I am logged in as an admin
    And I am on "/"

  @javascript @insulated
  Scenario: I can go to the support forums
    When I follow the toolbar link "WordPress > Support Forums"
    Then I should be on "https://wordpress.org/support/"

  @javascript @insulated
  Scenario: I can add a new page
    When I follow the toolbar link "New > Page"
    Then I should be on the "Add New Page" page

  @javascript @insulated
  Scenario: I can select a site
    When I follow the toolbar link "wordpress.dev > Widgets"
    Then I should be on the "Widgets" page

  @javascript @insulated
  Scenario: I can go to comments
    When I follow the toolbar link "Comments"
    Then I should be on the "Comments" page

  @javascript @insulated
  Scenario: I can go to edit my profile
    When I follow the toolbar link "Howdy, admin > Edit My Profile"
    Then I should be on the "Profile" page

  @javascript @insulated
  Scenario: I can search using the toolbar
    When I search for "Hello World" in the toolbar
    Then I should see "Search results"

  @javascript @insulated
  Scenario: I can a greeting in the toolbar
    Then I should see "Howdy, admin" in the toolbar
