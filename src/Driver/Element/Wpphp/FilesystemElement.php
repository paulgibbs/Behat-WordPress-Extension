<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpphp;

use RuntimeException;
use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-API driver element for manipulating the filesystem.
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
        if (empty($abspath) || ! is_file($abspath) || ! is_readable($abspath)) {
            return;
        }

        wp_delete_file($abspath);
    }
}
