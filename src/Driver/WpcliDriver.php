<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

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
     * Binary for WP-CLi
     * Defaults to wp, or wp.bat for Windows installs
     */
    protected $binary = 'wp';

    /**
     * Constructor.
     *
     * @param string      $alias  WP-CLI alias. This or $path must be not falsey.
     * @param string      $path   Absolute path to WordPress site's files. This or $alias must be not falsey.
     * @param string      $url    WordPress site URL.
     * @param string|null $binary Path to the WP-CLI binary.
     */
    public function __construct($alias, $path, $url, $binary)
    {
        $this->alias = ltrim($alias, '@');
        $this->path  = realpath($path);
        $this->url   = rtrim(filter_var($url, FILTER_SANITIZE_URL), '/');

        // Support Windows.
        if ($binary === null && DIRECTORY_SEPARATOR === '\\') {
            $this->binary = 'wp.bat';
        } elseif ($binary !== null) {
            $this->binary = $binary;
        }
    }

    /**
     * Set up anything required for the driver.
     *
     * Called when the driver is used for the first time.
     * Checks `core is-installed`, and the version number.
     */
    public function bootstrap()
    {
        $version = '';

        preg_match('#^WP-CLI (\d\.\d\.\d)$#', $this->wpcli('cli', 'version')['stdout'], $match);
        if (! empty($match)) {
            $version = array_pop($match);
        }

        if (! version_compare($version, '0.24.0', '>=')) {
            throw new RuntimeException('Your WP-CLI is too old; version 0.24.0 or newer is required.');
        }

        $status = $this->wpcli('core', 'is-installed')['exit_code'];
        if ($status !== 0) {
            throw new RuntimeException('WP-CLI driver cannot find WordPress. Check "path" and/or "alias" settings.');
        }

        $this->is_bootstrapped = true;
    }

    /**
     * Execute a WP-CLI command.
     *
     * @param string $command       Command name.
     * @param string $subcommand    Subcommand name.
     * @param array  $raw_arguments Optional. Associative array of arguments for the command.
     * @return array {
     *     WP-CLI command results.
     *
     *     @type string $stdout    Response text from WP-CLI.
     *     @type int    $exit_code Returned status code of the executed command.
     * }
     */
    public function wpcli($command, $subcommand, $raw_arguments = [])
    {
        $arguments = '';

        // Build parameter list.
        foreach ($raw_arguments as $name => $value) {
            if (is_numeric($name)) {
                $arguments .= "{$value} ";
            } else {
                $arguments .= sprintf('%s=%s ', $name, escapeshellarg($value));
            }
        }

        // TODO: review best practice with escapeshellcmd() here, and impact on metacharacters.
        $config = sprintf('--path=%s --url=%s', escapeshellarg($this->path), escapeshellarg($this->url));

        // Support WP-CLI environment aliases.
        if ($this->alias) {
            $config = "@{$this->alias}";
        }


        // Query WP-CLI.
        $proc = proc_open(
            "{$this->binary} {$config} --no-color {$command} {$subcommand} {$arguments}",
            array(
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ),
            $pipes
        );

        $stdout = trim(stream_get_contents($pipes[1]));
        $stderr = trim(stream_get_contents($pipes[2]));
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit_code = proc_close($proc);

        if ($exit_code || $stderr || strpos($stdout, 'Warning: ') === 0 || strpos($stdout, 'Error: ') === 0) {
            throw new UnexpectedValueException(
                sprintf(
                    "WP-CLI driver failure in method %1\$s(): \n\t%2\$s\n(%3\$s)",
                    debug_backtrace()[1]['function'],
                    $stderr ?: $stdout,
                    $exit_code
                )
            );
        }

        return compact('stdout', 'exit_code');
    }
}
