Feature: Accessing WordPress site
  As a WordPress developer
  In order to know this Apache is serving static HTML
  I'd like to check the WordPress readme.html is visible

  Scenario: Visiting the homepage
    Given I am on "readme.html" from the site root
    Then I should see "WordPress is a very special project to me"
