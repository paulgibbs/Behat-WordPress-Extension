<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

use PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Connect Behat to WordPress using WP-CLI.
 */
class WpcliDriver extends BaseDriver
{
    /**
     * The name of a WP-CLI alias for tests requiring shell access.
     *
     * @var string
     */
    protected $alias = '';

    /**
     * Path to WordPress' files.
     *
     * @var string
     */
    protected $path = '';

    /**
     * WordPress site URL.
     *
     * @var string
     */
    protected $url = '';

    /**
     * Binary for WP-CLI.
     *
     * Defaults to wp, or wp.bat for Windows installs.
     *
     * @var string
     */
    protected $binary = 'wp';

    /**
     * Constructor.
     *
     * @param string      $alias  WP-CLI alias. This or $path must be not falsey.
     * @param string      $path   Path to WordPress site's files. This or $alias must be not falsey.
     * @param string      $url    WordPress site URL.
     * @param string|null $binary Path to the WP-CLI binary.
     */
    public function __construct($alias, $path, $url, $binary)
    {
        $this->alias  = ltrim($alias, '@');
        $this->path   = $path ? realpath($path) : '';
        $this->url    = rtrim(filter_var($url, FILTER_SANITIZE_URL), '/');
        $this->binary = $binary;

        echo 'setting constructor';
        echo $this->alias . PHP_EOL;
        echo $this->path . PHP_EOL;
        echo $this->url . PHP_EOL;
        echo $this->binary . PHP_EOL;

        // Path can be relative.
        if (! $this->path) {
            $this->path = $path;
        }
    }

    /**
     * Set up anything required for the driver.
     *
     * Called when the driver is used for the first time.
     * Checks `core is-installed`, and the version number.
     *
     * @throws \RuntimeException
     */
    public function bootstrap()
    {
        $version = '';

        preg_match('#^WP-CLI (.*)$#', $this->wpcli('cli', 'version')['stdout'], $match);
        if (! empty($match)) {
            $version = array_pop($match);
        }

        if (! version_compare($version, '1.3.0', '>=')) {
            throw new RuntimeException('Your WP-CLI is too old; version 1.3.0 or newer is required.');
        }

        $status = $this->wpcli('core', 'is-installed')['exit_code'];
        if ($status !== 0) {
            throw new RuntimeException('WordPress does not seem to be installed. Please install WordPress. If WordPress is installed, the WP-CLI driver cannot find WordPress. Please check the "path" and/or "alias" settings in behat.yml.');
        }

        putenv('WP_CLI_STRICT_ARGS_MODE=1');

        $this->is_bootstrapped = true;
    }

    /**
     * Execute a WP-CLI command.
     *
     * @param string   $command       Command name.
     * @param string   $subcommand    Subcommand name.
     * @param string[] $raw_arguments Optional. Associative array of arguments for the command.
     *
     * @throws \UnexpectedValueException
     *
     * @return array {
     *     WP-CLI command results.
     *
     *     @type string $stdout Response text from WP-CLI.
     *     @type int $exit_code Returned status code of the executed command.
     * }
     */
    public function wpcli($command, $subcommand, $raw_arguments = [])
    {
        $arguments = implode(' ', $raw_arguments);
        $config    = sprintf('--path=%s --url=%s', escapeshellarg($this->path), escapeshellarg($this->url));

        // Support WP-CLI environment aliases.
        if ($this->alias) {
            $config = "@{$this->alias}";
        }

        echo "{$this->binary} {$config} --no-color {$command} {$subcommand} {$arguments}";
        // Query WP-CLI.
        $proc = proc_open(
            "{$this->binary} {$config} --no-color {$command} {$subcommand} {$arguments}",
            array(
                1 => ['pipe', 'w'],
            ),
            $pipes
        );

        $stdout = trim(stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        $exit_code = proc_close($proc);

        if ($exit_code || strpos($stdout, 'Warning: ') === 0 || strpos($stdout, 'Error: ') === 0) {
            throw new UnexpectedValueException(
                sprintf(
                    "WP-CLI driver failure in method %1\$s(): \n\t%2\$s\n(%3\$s)",
                    debug_backtrace()[1]['function'],
                    $stdout,
                    $exit_code
                )
            );
        }

        return compact('stdout', 'exit_code');
    }


    /*
     * Backwards compatibility.
     */

    /**
     * Clear object cache.
     *
     * This method will be removed in release 1.0.0.
     */
    public function clearCache()
    {
        $this->cache->clear();
    }

    /**
     * Activate a plugin.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $plugin
     */
    public function activatePlugin($plugin)
    {
        $this->plugin->activate($plugin);
    }

    /**
     * Deactivate a plugin.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $plugin
     */
    public function deactivatePlugin($plugin)
    {
        $this->plugin->deactivate($plugin);
    }

    /**
     * Switch active theme.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $theme
     */
    public function switchTheme($theme)
    {
        $this->theme->change($theme);
    }

    /**
     * Create a term in a taxonomy.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $term
     * @param string $taxonomy
     * @param array  $args     Optional. Set the values of the new term.
     *
     * @return array {
     *     @type int    $id   Term ID.
     *     @type string $slug Term slug.
     * }
     */
    public function createTerm($term, $taxonomy, $args = [])
    {
        $args['taxonomy'] = $taxonomy;
        $args['term']     = $term;

        $term = $this->term->create($args);

        return array(
            'id'   => $term->term_id,
            'slug' => $term->slug,
        );
    }

    /**
     * Delete a term from a taxonomy.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int    $term_id
     * @param string $taxonomy
     */
    public function deleteTerm($term_id, $taxonomy)
    {
        $this->term->delete($term_id, ['taxonomy' => $taxonomy]);
    }

    /**
     * Create content.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param array $args Set the values of the new content item.
     *
     * @return array {
     *     @type int    $id   Content ID.
     *     @type string $slug Content slug.
     *     @type string $url  Content permalink.
     * }
     */
    public function createContent($args)
    {
        $post = $this->content->create($args);
        $alt  = $this->wpcli('post', 'list', ['--post__in=' . $post->ID, '--fields=url', '--post_type=any', '--format=json'])['stdout'];
        $alt  = json_decode($alt);

        return array(
            'id'   => (int) $post->ID,
            'slug' => $post->post_name,
            'url'  => $alt[0]->url,
        );
    }

    /**
     * Delete specified content.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of content to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteContent($id, $args = [])
    {
        $this->content->delete($id, $args);
    }

    /**
     * Get content from its title.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $title     The title of the content to get.
     * @param string $post_type Post type(s) to consider when searching for the content.
     *
     * @throws \UnexpectedValueException If post does not exist
     *
     * @return array {
     *     @type int    $id   Content ID.
     *     @type string $slug Content slug.
     *     @type string $url Content url.
     * }
     */
    public function getContentFromTitle($title, $post_type = '')
    {
        $post = $this->content->get($title, ['by' => 'title']);
        $alt  = $this->wpcli('post', 'list', ['--post__in=' . $post->ID, '--fields=url', '--format=json'])['stdout'];
        $alt  = json_decode($alt);

        return array(
            'id'   => (int) $post->ID,
            'slug' => $post->post_name,
            'url'  => $alt[0]->url,
        );
    }

    /**
     * Create a comment.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param array $args Set the values of the new comment.
     *
     * @return array {
     *     @type int $id Content ID.
     * }
     */
    public function createComment($args)
    {
        $comment = $this->comment->create($args);

        return array(
            'id' => $comment->comment_ID,
        );
    }

    /**
     * Delete specified comment.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of comment to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteComment($id, $args = [])
    {
        $this->comment->delete($id, $args);
    }

    /**
     * Create a user.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $user_login User login name.
     * @param string $user_email User email address.
     * @param array  $args       Optional. Extra parameters to pass to WordPress.
     *
     * @return array {
     *     @type int    $id   User ID.
     *     @type string $slug User slug (nicename).
     * }
     */
    public function createUser($user_login, $user_email, $args = [])
    {
        $args['user_login'] = $user_login;
        $args['user_email'] = $user_email;

        $user = $this->user->create($args);

        return array(
            'id'   => $user->ID,
            'slug' => $user->user_nicename,
        );
    }

    /**
     * Delete a user.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param int   $id   ID of user to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteUser($id, $args = [])
    {
        $this->user->delete($id, $args);
    }

    /**
     * Get a User's ID from their username.
     *
     * This method will be removed in release 1.0.0.
     *
     * @param string $username The username of the user to get the ID of.
     *
     * @throws \UnexpectedValueException If provided data is invalid
     *
     * @return int ID of the user.
     */
    public function getUserIdFromLogin($username)
    {
        return $this->user->get($username, ['by' => 'login'])->ID;
    }

    /**
     * Start a database transaction.
     *
     * This method will be removed in release 1.0.0.
     *
     * @throws \PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException
     */
    public function startTransaction()
    {
        throw new UnsupportedDriverActionException('start a database transaction in ' . static::class);
    }

    /**
     * End (rollback) a database transaction.
     *
     * This method will be removed in release 1.0.0.
     *
     * @throws \PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException
     */
    public function endTransaction()
    {
        throw new UnsupportedDriverActionException('rollback a database transaction in ' . static::class);
    }
}
