<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for manipulating the database directly.
 */
class DatabaseElement extends BaseElement
{
    /**
     * Export site database.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to fetch an object.
     *
     * @return string Path to the export file.
     */
    public function get($id, $args)
    {
        while (true) {
            $filename = uniqid('database-', true) . '.sql';
            if (! file_exists(getcwd() . "/{$filename}")) {
                break;
            }
        }

        // Protect against WP-CLI changing the filename.
        $filename = $this->drivers->getDriver()->wpcli('db', 'export', [$filename, '--porcelain'])['stdout'];

        return getcwd() . "/{$filename}";
    }

    /**
     * Import site database from file.
     *
     * If $id begins with a directory separator or ~ it is treated as an absolute path.
     * Otherwise, it is treated as relative to the current working directory.
     *
     * @param string $id   Relative or absolute path and filename of SQL file to import.
     * @param array  $args Data used to update an object.
     */
    public function update($id, $args)
    {
        if (! in_array($id[0], [DIRECTORY_SEPARATOR, '~'], true)) {
            $id = getcwd() . "/{$id}";
        }

        $this->drivers->getDriver()->wpcli('db', 'import', [$id]);
    }

    // djpaultodo: helper functions? import/export?
}
