<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use PaulGibbs\WordpressBehatExtension\PageObject\AdminPage;
use PaulGibbs\WordpressBehatExtension\PageObject\Dashboard;

/**
 * Provides step definitions that are specific to the WordPress dashboard (wp-admin).
 */
class DashboardContext extends RawWordpressContext
{

    /**
     * Non-specific admin page (wp-admin/) object.
     * @param AdminPage
     */
    protected $admin_page;

    /**
     * Dashboard (wp-admin/index.php) object.
     * @param Dashboard
     */
    protected $dashboard;

    /**
     * Constructor.
     *
     * @param AdminPage $admin_page AdminPage object.
     * @param Dashboard $dashboard  Dashboard object.
     */
    public function __construct(AdminPage $admin_page, Dashboard $dashboard)
    {
        parent::__construct();

        $this->admin_page = $admin_page;
        $this->dashboard = $dashboard;
    }

    /**
     * Click a link within the page header tag.
     *
     * Example: When I click on the "Add New" link in the header
     *
     * @When I click on the :link link in the header
     */
    public function iClickOnHeaderLink($link)
    {
        $this->admin_page->clickLinkInHeader($link);
    }

    /**
     * Assert the text in the page header tag matches the given string.
     *
     * Example: Then I should be on the "Posts" page
     *
     * @Then I should be on the :admin_page page
     */
    public function iShouldBeOnThePage($admin_page)
    {
        $this->admin_page->assertHasHeader($admin_page);
    }

    /**
     * Assert we are on the Dashboard page (wp-admin/index.php).
     *
     * @Given I am on the Dashboard
     */
    public function iAmOnDashboard()
    {
        $this->dashboard->open();
    }

    /**
     * Go to a given page on the admin menu.
     *
     * Example: Given I go to "Posts > Add New"
     * Example: Given I go to "Users"
     * Example: Given I go to "Settubgs > Reading"
     *
     * @Given I go to menu item :item
     */
    public function iGoToMenuItem($item)
    {
        $adminMenu = $this->admin_page->getMenu();
        $adminMenu->clickMenuItem($item);
    }

    /**
     * Check the specified notification is on-screen.
     *
     * Example: Then I should see a status message that says "Post published"
     *
     * @Then /^(?:I|they) should see an? (error|status) message that says "([^"]+)"$/
     *
     * @param string $type    Message type. Either "error" or "status".
     * @param string $message Text to search for.
     */
    public function iShouldSeeMessageThatSays($type, $message)
    {
        $selector = 'div.notice';

        if ($type === 'error') {
            $selector .= '.error';
        } else {
            $selector .= '.updated';
        }

        $this->assertSession()->elementTextContains('css', $selector, $message);
    }
}
