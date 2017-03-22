Feature: Viewing posts

    Background:
        Given there are posts:
            | post_title      | post_content              | post_status | post_author |
            | Just my article | The content of my article | publish     | admin       |
            | My draft        | This is just a draft      | draft       | admin       |

    Scenario: List my blog posts
        Given I am on the homepage
        Then I should see "Just my article"
        And I should not see "My draft"

    Scenario: Read a blog post
        Given I am on the homepage
        When I follow "Just my article"
        Then I should see "Just my article"
        And I should see "The content of my article"

    Scenario: Viewing a single page
        Given I am viewing a post:
           | post_type | post_title      | post_content    | post_status |
           | page      | My about page   | About this site | publish     |
        Then I should see "My about page"
        And I should see "About this site"

    Scenario: Viewing an existing post
        Given I am viewing a post "Just my article"
        Then I should see "Just my article"
        And I should see "The content of my article"
