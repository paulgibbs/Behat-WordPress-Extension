<?php
namespace PaulGibbs\WordpressBehatExtension;

// This file is directly (and only) used by WP-CLI.
if (! defined('\WP_CLI') || ! \WP_CLI) {
    return;
}

/**
 * Get shell instructions to download custom logger for WP-CLI.
 *
 * @param string $download_path Where to store the custom logger.
 */
function get_download_script($download_path)
{
    // Have remote WP-CLI download the custom logger and verify checksum.
    return sprintf(
        'curl %1$s --create-dirs -o %2$s -s -S && CHECKSUM=$(cat %2$s | openssl dgst -sha1 | sed \'s/^.* //\') && [ "$CHECKSUM" = "37102b5be88d7ef85ead5c4cf4c74e06d89ac3e9" ] || { echo >&2 "WP-CLI driver SSH bad checksum."; exit 1; }',
        'https://raw.githubusercontent.com/paulgibbs/Shouty-Logger-for-WP-CLI/96d56f7f4c9a871638bd4ccaed6211b7ba138e85/wpcli-shouty-logger.php',
        $download_path
    );
}

// Always have local WP-CLI load the logger.
$bootstrap = '/tmp/wordhat-wpcli-bootstrap.php';
exec(get_download_script($bootstrap));
require_once($bootstrap);

// If connecting via SSH, have the remote WP-CLI configure itself to use a custom logger.
\WP_CLI::add_hook('before_ssh', function () use ($bootstrap) {
    $argv_count = count($GLOBALS['argv']);
    $option     = null;

    // Find the position of the --require=... arg.
    for ($i = 0; $i < $argv_count; $i++) {
        if (strpos($GLOBALS['argv'][$i], '--require=') !== false) {
            $option = $i;
            break;
        }
    }

    if ($option === null) {
        return;
    }

    // Have local WP-CLI pass script instructions to remote WP-CLI.
    $script = \PaulGibbs\WordpressBehatExtension\get_download_script($bootstrap);
    putenv("WP_CLI_SSH_PRE_CMD={$script}");

    // Modify `--require` arg to have remote WP-CLI load the custom logger.
    $GLOBALS['argv'][$option] = "--require={$bootstrap}";
});
