<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use RuntimeException;
use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for manipulating the filesystem.
 */
class FilesystemElement extends BaseElement
{
    /**
     * Delete specified file.
     *
     * @param string $abspath Absolute path to a file, to delete.
     */
    public function deleteFile(string $abspath)
    {
        if (empty($abspath)) {
            return;
        }

        $delete_cmd = sprintf('@unlink(%s);', escapeshellarg($abspath));
        $this->drivers->getDriver()->wpcli('eval', $delete_cmd, ['--skip-wordpress']);
    }
}

