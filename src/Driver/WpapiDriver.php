<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

use RuntimeException;
use UnexpectedValueException;
use function PaulGibbs\WordpressBehatExtension\Util\is_wordpress_error;

/**
 * Connect Behat to WordPress by loading WordPress directly into the global scope.
 */
class WpapiDriver extends BaseDriver
{
    /**
     * Path to WordPress' files.
     *
     * @var string
     */
    protected $path = '';

    /**
     * WordPres database object.
     *
     * @var \wpdb
     */
    protected $wpdb = null;

    /**
     * Constructor.
     *
     * @param string $path Absolute path to WordPress site's files.
     */
    public function __construct($path)
    {
        $this->path = realpath($path);
    }

    /**
     * Set up anything required for the driver.
     *
     * Called when the driver is used for the first time.
     */
    public function bootstrap()
    {
        if (! defined('ABSPATH')) {
            define('ABSPATH', "{$this->path}/");
        }

        $_SERVER['DOCUMENT_ROOT']   = $this->path;
        $_SERVER['HTTP_HOST']       = '';
        $_SERVER['REQUEST_METHOD']  = 'GET';
        $_SERVER['REQUEST_URI']     = '/';
        $_SERVER['SERVER_NAME']     = '';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';

        if (! file_exists("{$this->path}/index.php")) {
            throw new RuntimeException(sprintf('WordPress API driver cannot find WordPress at %s.', $this->path));
        }

        // "Cry 'Havoc!' and let slip the dogs of war".
        require_once "{$this->path}/wp-blog-header.php";

        if (! function_exists('activate_plugin')) {
            require_once "{$this->path}/wp-admin/includes/plugin.php";
            require_once "{$this->path}/wp-admin/includes/plugin-install.php";
        }

        $this->wpdb            = $GLOBALS['wpdb'];
        $this->is_bootstrapped = true;
    }


    /*
     * Internal helpers.
     */

    /**
     * Get information about a plugin.
     *
     * @param string $name
     * @return string Plugin filename and path.
     */
    protected function getPlugin($name)
    {
        foreach (get_plugins() as $file => $_) {
            // Logic taken from WP-CLI.
            if ($file === "{$name}.php" || ($name && $file === $name) || (dirname($file) === $name && $name !== '.')) {
                return $file;
            }
        }

        return '';
    }
}
