<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use InvalidArgumentException;
use Behat\Gherkin\Node\TableNode;
use function PaulGibbs\WordpressBehatExtension\Util\isWordpressError;
use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;
use Behat\Mink\Exception\ElementTextException;

/**
 * Provides step definitions for a range of common tasks. Recommended for all test suites.
 */
class WordpressContext extends RawWordpressContext implements PageObjectAware
{
    use PageObjectContextTrait;

    /**
     * Clear object cache.
     *
     * @AfterScenario
     */
    public function clearCache()
    {
        parent::clearCache();
    }

    /**
     * Clear Mink's browser environment.
     *
     * @AfterScenario
     */
    public function resetBrowser()
    {
        parent::resetBrowser();
    }

    /**
     * When using the Selenium driver, position the admin bar to the top of the page, not fixed to the screen.
     * Otherwise the admin toolbar can hide clickable elements.
     *
     * @BeforeStep
     */
    public function fixToolbar()
    {
        $driver = $this->getSession()->getDriver();
        if ($driver instanceof \Behat\Mink\Driver\Selenium2Driver && $driver->getWebDriverSession()) {
            $this->getSession()->getDriver()->executeScript(
                'if (document.getElementById("wpadminbar")){
                    document.getElementById("wpadminbar").style.position="absolute";
					if ( document.getElementsByTagName("body")[0].className.match(/wp-admin/) ) {
						document.getElementById("wpadminbar").style.top="-32px";
					}
				};'
            );
        }
    }

    /**
     * Open the dashboard.
     *
     * Example: Given I am on the dashboard
     * Example: Given I am in wp-admin
     * Example: When I go to the dashboard
     * Example: When I go to wp-admin
     *
     * @Given /^(?:I am|they are) on the dashboard/
     * @Given /^(?:I am|they are) in wp-admin/
     * @When /^(?:I|they) go to the dashboard/
     * @When /^(?:I|they) go to wp-admin/
     */
    public function iAmOnDashboard()
    {
        $this->visitPath('wp-admin/');
    }

    /**
     * Searches for a term using the toolbar search field
     *
     * Example: When I search for "Hello World" in the toolbar
     *
     * @When I search for :search in the toolbar
     */
    public function iSearchUsingTheToolbar($search)
    {
        $this->getElement('Toolbar')->search($search);
    }

    /**
     * Clicks the specified link in the toolbar.
     *
     * Example: Then I should see "Howdy, admin" in the toolbar
     *
     * @Then I should see :text in the toolbar
     */
    public function iShouldSeeTextInToolbar($text)
    {
        $toolbar = $this->getElement('Toolbar');
        $actual = $toolbar->getText();
        $regex = '/' . preg_quote($text, '/') . '/ui';

        if (! preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" was not found in the toolbar', $text);
            throw new ElementTextException($message, $this->getSession()->getDriver(), $toolbar);
        }
    }

    /**
     * Clicks the specified link in the toolbar.
     *
     * Example: When I follow the toolbar link "New > Page"
     * Example: When I follow the toolbar link "Updates"
     * Example: When I follow the toolbar link "Howdy, admin > Edit My Profile"
     *
     * @When I follow the toolbar link :link
     */
    public function iFollowTheToolbarLink($link)
    {
        $this->getElement('Toolbar')->clickToolbarLink($link);
    }

    /**
     * A site_url-aware version of Mink's "I am on..." step.
     *
     * Example: Given I am on "readme.txt" from the site root.
     *
     * @Given I am on :url from the site root
     */
    public function iAmOnURLFromSiteRoot($url)
    {
        $this->visitPath($url);
    }
}
