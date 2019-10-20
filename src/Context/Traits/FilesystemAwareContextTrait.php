<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context\Traits;

/**
 * Provides driver agnostic logic (helper methods) relating to a filesystem or files.
 */
trait FilesystemAwareContextTrait
{
    use BaseAwarenessTrait;

    /**
     * Delete specified file.
     *
     * @param string $abspath Absolute path to a file, to delete.
     */
    public function deleteFile(string $abspath)
    {
        $this->getDriver()->filesystem->deleteFile($abspath);
    }
}
