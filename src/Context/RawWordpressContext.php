<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Mink\Exception\UnsupportedDriverActionException;

use PaulGibbs\WordpressBehatExtension\WordpressDriverManager;
use PaulGibbs\WordpressBehatExtension\Util;

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAware;

/**
 * Base Behat context.
 *
 * Does not contain any step defintions.
 */
class RawWordpressContext extends RawMinkContext implements WordpressAwareInterface, SnippetAcceptingContext, PageObjectAware
{
    use PageObjectContextTrait;

    /**
     * WordPress driver manager.
     *
     * @var WordpressDriverManager
     */
    protected $wordpress;

    /**
     * WordPress parameters.
     *
     * @var array
     */
    protected $wordpress_parameters;


    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Build URL, based on provided path.
     *
     * @param string $path Relative or absolute URL.
     * @return string
     */
    public function locatePath($path)
    {
        if (strtolower(substr($path, 0, '4')) === 'http') {
            return $path;
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $url = $this->getMinkParameter('base_url');

        if (strpos($path, 'wp-admin') !== false || in_array($ext, ['htm', 'html', 'md', 'php', 'txt'], true)) {
            $url = $this->getWordpressParameter('site_url');
        }

        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Set WordPress instance.
     *
     * @param WordpressDriverManager $wordpress
     */
    public function setWordpress(WordpressDriverManager $wordpress)
    {
        $this->wordpress = $wordpress;
    }

    /**
     * Get WordPress instance.
     *
     * @return WordpressDriverManager
     */
    public function getWordpress()
    {
        return $this->wordpress;
    }

    /**
     * Set parameters provided for WordPress.
     *
     * @param array $parameters
     */
    public function setWordpressParameters($parameters)
    {
        $this->wordpress_parameters = $parameters;
    }

    /**
     * Get a specific WordPress parameter.
     *
     * @param string $name Parameter name.
     * @return mixed
     */
    public function getWordpressParameter($name)
    {
        return isset($this->wordpress_parameters[$name]) ? $this->wordpress_parameters[$name] : null;
    }

    /**
     * Get all WordPress parameters.
     *
     * @return array
     */
    public function getWordpressParameters()
    {
        return $this->wordpress_parameters;
    }

    /**
     * Get active WordPress Driver.
     *
     * @param string $name Optional. Name of specific driver to retrieve.
     * @return \PaulGibbs\WordpressBehatExtension\Driver\DriverInterface
     */
    public function getDriver($name = '')
    {
        return $this->getWordpress()->getDriver($name);
    }

    /**
     * Wrap a closure in a spin check.
     *
     * This is a technique to accommodate in-progress state changes in a web page (i.e. waiting for new data to load)
     * by retrying the action for a given number of attempts, each delayed by 1 second. The closure is expected to
     * throw an exception should the expected state not (yet) exist.
     *
     * To avoid doubt, you should only need to spin when waiting for an AJAX response, after initial page load.
     *
     * @deprecated Use PaulGibbs\WordpressBehatExtension\Util\spins
     *
     * @param callable $closure Action to execute.
     * @param int      $wait    Optional. How long to wait before giving up, in seconds.
     */
    public function spins(callable $closure, $wait = 60)
    {
        Util\spins($closure, $wait);
    }

    /**
     * Log in the user.
     *
     * @param string $username
     * @param string $password
     */
    public function logIn($username, $password)
    {
        if ($this->loggedIn()) {
            $this->logOut();
        }

        $this->visitPath('wp-login.php?redirect_to=' . urlencode($this->locatePath('/')));
        $page = $this->getSession()->getPage();

        $node = $page->findField('user_login');
        try {
            $node->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary
        }
        $node->setValue($username);

        $node = $page->findField('user_pass');
        try {
            $node->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary
        }
        $node->setValue($password);

        $page->findButton('wp-submit')->click();

        if (! $this->loggedIn()) {
            throw new ExpectationException('The user could not be logged-in.', $this->getSession()->getDriver());
        }
    }

    /**
     * Log the current user out.
     */
    public function logOut()
    {
        $this->getElement('Toolbar')->logOut();
    }

    /**
     * Determine if the current user is logged in or not.
     *
     * @return bool
     */
    public function loggedIn()
    {
        $page = $this->getSession()->getPage();

        // Look for a selector to determine if the user is logged in.
        try {
            return $page->has('css', 'body.logged-in');

        // This may fail if the user has not loaded any site yet.
        } catch (DriverException $e) {
        }

        return false;
    }

    /**
     * Clear object cache.
     */
    public function clearCache()
    {
        $this->getDriver()->cache->clear();
    }

    /**
     * Clear Mink's browser environment.
     */
    public function resetBrowser()
    {
        $this->getSession()->reset();
    }

    /**
     * Activate a plugin.
     *
     * @param string $plugin
     */
    public function activatePlugin($plugin)
    {
        $this->getDriver()->plugin->activate($plugin);
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $plugin
     */
    public function deactivatePlugin($plugin)
    {
        $this->getDriver()->plugin->deactivate($plugin);
    }

    /**
     * Switch active theme.
     *
     * @param string $theme
     */
    public function switchTheme($theme)
    {
        $this->getDriver()->theme->change($theme);
    }

    /**
     * Create a term in a taxonomy.
     *
     * @param string $term
     * @param string $taxonomy
     * @param array  $args     Optional. Set the values of the new term.
     * @return array {
     *     @type int    $id   Term ID.
     *     @type string $slug Term slug.
     * }
     */
    public function createTerm($term, $taxonomy, $args = [])
    {
        $args['taxonomy'] = $taxonomy;
        $args['term']     = $term;

        $term = $this->getDriver()->term->create($args);

        return array(
            'id'   => $term->term_id,
            'slug' => $term->slug,
        );
    }

    /**
     * Delete a term from a taxonomy.
     *
     * @param int    $term_id
     * @param string $taxonomy
     */
    public function deleteTerm($term_id, $taxonomy)
    {
        $this->getDriver()->term->delete($term_id, compact($taxonomy));
    }

    /**
     * Create content.
     *
     * @param array $args Set the values of the new content item.
     * @return array {
     *     @type int    $id   Content ID.
     *     @type string $slug Content slug.
     *     @type string $url  Content permalink.
     * }
     */
    public function createContent($args)
    {
        $content = $this->getDriver()->content->create($args);

        return array(
            'id'   => $content->ID,
            'slug' => $content->post_name,
            'url'  => $content->url,
        );
    }

    /**
     * Delete specified content.
     *
     * @param int   $id   ID of content to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteContent($id, $args = [])
    {
        $this->getDriver()->content->delete($id, $args);
    }

    /**
     * Create a comment.
     *
     * @param array $args Set the values of the new comment.
     * @return array {
     *     @type int $id Content ID.
     * }
     */
    public function createComment($args)
    {
        $comment = $this->getDriver()->comment->create($args);

        return array(
            'id' => $comment->comment_ID,
        );
    }

    /**
     * Delete specified comment.
     *
     * @param int   $id   ID of comment to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteComment($id, $args = [])
    {
        $this->getDriver()->comment->delete($id, $args);
    }

    /**
     * Export WordPress database.
     *
     * @return string Absolute path to database SQL file.
     */
    public function exportDatabase()
    {
        $this->getDriver()->database->export(0);
    }

    /**
     * Import WordPress database.
     *
     * @param string $import_file Relative path and filename of SQL file to import.
     */
    public function importDatabase($import_file)
    {
        $this->getDriver()->database->import($import_file);
    }

    /**
     * Create a user.
     *
     * @param string $user_login User login name.
     * @param string $user_email User email address.
     * @param array  $args       Optional. Extra parameters to pass to WordPress.
     * @return array {
     *     @type int    $id   User ID.
     *     @type string $slug User slug (nicename).
     * }
     */
    public function createUser($user_login, $user_email, $args = [])
    {
        $args['user_email'] = $user_email;
        $args['user_login'] = $user_login;

        $user = $this->getDriver()->user->create($args);

        return array(
            'id'   => $user->ID,
            'slug' => $user->user_nicename,
        );
    }

    /**
     * Delete a user.
     *
     * @param int   $id   ID of user to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteUser($id, $args = [])
    {
        $this->getDriver()->user->delete($id, $args);
    }

    /**
     * Start a database transaction.
     */
    public function startTransaction()
    {
        $this->getDriver()->database->startTransaction();
    }

    /**
     * End (rollback) a database transaction.
     */
    public function endTransaction()
    {
        $this->getDriver()->database->endTransaction();
    }
}
