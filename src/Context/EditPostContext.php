<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context;

use PaulGibbs\WordpressBehatExtension\PageObject\PostsEditPage;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;
use PaulGibbs\WordpressBehatExtension\Context\Traits\ContentAwareContextTrait;

/**
 * Provides step definitions relating to editing content in wp-admin.
 */
class EditPostContext extends RawWordpressContext
{
    use ContentAwareContextTrait;
    /**
     * Edit post/page/post-type page (/wp-admin/post.php?post=<id>&action=edit) object.
     *
     * TODO: this needs to not be public!
     *
     * @var PostsEditPage
     */
    public $edit_post_page;

    /**
     * Constructor.
     *
     * @param PostsEditPage $edit_post_page The page object representing the edit post page.
     */
    public function __construct(PostsEditPage $edit_post_page)
    {
        parent::__construct();

        $this->edit_post_page = $edit_post_page;
    }

    /**
     * Go to the edit post admin page for the referenced post.
     *
     * This step allows you to specify the post type to consider.
     *
     * Example: Given I am on the edit post screen for "Hello World"
     * Example: Given I am on the edit event screen for "Some Event"
     *
     * @Given /^I am on the edit ([a-zA-z_-]+) screen for "([^"]*)"$/
     *
     * @param string $post_type The post type of the referenced 'post' being edited.
     * @param string $title     The name of the 'post' being edited.
     */
    public function iGoToEditScreenForPostType(string $post_type, string $title)
    {
        $post = $this->getContentFromTitle($title, $post_type);
        $this->edit_post_page->open(array(
            'id' => $post['id'],
        ));
    }

    /**
     * Go to the edit post admin page for the referenced post.
     *
     * This step works for all post types.
     *
     * Example: Given I am on the edit screen for "Hello World"
     * Example: Given I am on the edit screen for "Some Event"
     *
     * @Given /^I am on the edit screen for "(?P<title>[^"]*)"$/
     *
     * @param string $title The name of the 'post' being edited.
     */
    public function iGoToEditScreenFor(string $title)
    {
        $post = $this->getContentFromTitle($title);
        $this->edit_post_page->open(array(
            'id' => $post['id'],
        ));
    }

    /**
     * Change the title on the edit post screen.
     *
     * Example: When I change the title to "Hello World"
     *
     * @When /^I change the title to "(?P<title>[^"]*)"$/
     *
     * @param string $title The title to enter in the title field on the edit post page.
     */
    public function iChangeTitleTo(string $title)
    {
        $this->edit_post_page->setContentTitle($title);
    }

    /**
     * Switch the mode of the post content editor.
     *
     * Example: When I switch to the post content editor's Visual mode
     * Example: When I switch to the post content editor's Text mode
     *
     * @When /^I switch to the post content editor's (visual|text) mode$/i
     *
     * @param string $mode The mode (visual or text) to switch to in the editor.
     */
    public function iSelectPostContentEditorMode(string $mode)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        $content_editor->setMode(strtoupper($mode));
    }

    /**
     * Enter the content into the content editor.
     *
     * Example: When I enter the following content into the post content editor:
     *   """
     *   Welcome to WordPress. This is your first post. Edit or delete it, then start writing!
     *   """
     *
     * @When I enter the following content into the post content editor:
     *
     * @param PyStringNode $content The content to enter into the editor.
     */
    public function iEnterContentIntoPostContentEditor(PyStringNode $content)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        $content_editor->setContent($content->getRaw());
    }

    /**
     * Assert the mode that the content editor is in.
     *
     * Example: Then The post content editor is in Visual mode
     * Example: Then The post content editor is in Text mode
     *
     * @Then /^the post content editor is in (visual|text) mode$/i
     *
     * @param string $mode Assert that the editor is the specified mode (visual or text).
     *
     * @throws ExpectationException
     */
    public function postContentEditorIsInMode(string $mode)
    {
        $content_editor = $this->edit_post_page->getContentEditor();
        if (strtoupper($mode) !== $content_editor->getMode()) {
            throw new ExpectationException(
                sprintf('[W808] Content editor is in "%1$s" mode. Expected "%2$s".', $content_editor->getMode(), $mode),
                $this->getSession()->getDriver()
            );
        }
    }

    /**
     * Press the update/publish button.
     *
     * Example: When I publish the post
     * Example: When I publish the change
     * Example: When I publish the changes
     *
     * @When /^I publish the (post|changes?)$/
     */
    public function iPressThePublishButton()
    {
        $this->edit_post_page->pressUpdate();
    }

    /**
     * Assert that the edit screen for the given post and post type is displayed
     *
     * Example: Then I should be on the edit event screen for "Some Event"
     * Example: Then I should be on the edit post screen for "Hello World"
     *
     * @Then I should be on the edit :post_type screen for :post_title
     *
     * @param string $post_type  The post type of the referenced content being edited.
     * @param string $post_title The name of the content being edited.
     */
    public function iAmOnEditScreenForPostType(string $post_type, string $post_title)
    {
        $post = $this->getContentFromTitle($post_title, $post_type);
        $this->edit_post_page->isOpen(array(
            'id' => $post['id'],
        ));
    }

    /**
     * Assert that the referenced metabox is visible.
     *
     * The metabox may still be collapsed.
     *
     * Example: Then I should see the "Comments" metabox
     *
     * @Then I should see the :title metabox
     *
     * @param string $title The title of the metabox being checked
     */
    public function iShouldSeeTheMetabox(string $title)
    {
        $this->edit_post_page->getMetaBox($title);
    }

    /**
     * Assert that the referenced metabox is not visible.
     *
     * The metabox may still be collapsed.
     *
     * Example: Then I should not see the "Featured Image" metabox
     *
     * @Then I should not see the :title metabox
     *
     * @param string $title The title of the metabox being checked
     *
     * @throws ExpectationException
     */
    public function iShouldNotSeeTheMetabox(string $title)
    {
        try {
            $this->edit_post_page->getMetaBox($title);
        } catch (ExpectationException $e) {
            // Expectation fulfilled
            return;
        }

        throw new ExpectationException(
            sprintf(
                '[W809] Metabox "%s" was found on the page, but it should not be there.',
                $title
            ),
            $this->getSession()->getDriver()
        );
    }

    /**
     * Go to the edit post admin page for the latest post of a specific post status and post type.
     *
     * This step allows you to specify the post type to consider.
     * This step allows you to specify the post status to consider.
     *
     * Example: Given I am editing the latest published post
     * Example: Given I am editing the latest draft event
     * Example: Given I am editing the latest scheduled post
     *
     * @Given /^I am editing the latest ([a-z_-]+) ([a-z_-]+)$/
     *
     * @param string $post_status The post status of the referenced 'post' being edited.
     * @param string $post_type The post type of the referenced 'post' being edited.
     */
    public function iEditTheLatestPostOfPostTypeAndStatus(string $post_status='publish', string $post_type)
    {

        /**
         * Removed "ed" from the end of $post_status if it is present.
         * This allows words like "published" to be translated into the proper post status.
         */
        $post_status_ending = substr($post_status, -2);
        if ( 'ed' == $post_status_ending ) {
            $post_status = substr($post_status, 0, -2);
        }

        $args = array (
            "--post_type=$post_type",
            '--field=ID',
            '--orderby=date',
            '--order=DESC',
            "--post_status=$post_status",
            '--posts_per_page=1',
            '--ignore_sticky_posts=1',
        );
        
        $wpcli_output = $this->getDriver()->wpcli('post', 'list', $args);

        // Throw an exception if the result from wp-cli is empty or is not number.
        if( 
            empty( $wpcli_output['stdout'] ) || 
            1 !== preg_match( '/^[0-9]+$/', $wpcli_output['stdout'] ) 
        ){
            throw new \Exception("No posts with the post type '$post_type' and post status '$post_status' found");
        }

        $post_id = (int) $wpcli_output['stdout'];
        
        $this->edit_post_page->open(array(
            'id' => $post_id,
        ));
    }
}
