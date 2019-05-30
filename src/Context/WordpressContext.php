<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Event;
use Behat\Mink\Driver\Selenium2Driver;
use PaulGibbs\WordpressBehatExtension\Context\Traits\CacheAwareContextTrait;
use PaulGibbs\WordpressBehatExtension\Context\Traits\DatabaseAwareContextTrait;
use PaulGibbs\WordpressBehatExtension\Context\Traits\PageObjectAwareContextTrait;

/**
 * Provides step definitions for a range of common tasks. Recommended for all test suites.
 */
class WordpressContext extends RawWordpressContext
{
    use PageObjectAwareContextTrait, CacheAwareContextTrait, DatabaseAwareContextTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * If database.restore_after_test is set, and a scenario is tagged "db", create a database backup.
     *
     * The database will be restored from this backup via maybeRestoreDatabase().
     *
     * @BeforeScenario @db
     *
     * @param BeforeScenarioScope $scope
     */
    public function maybeBackupDatabase(BeforeScenarioScope $scope)
    {
        $db = $this->getWordpressParameter('database');
        if (! $db['restore_after_test']) {
            return;
        }

        /*
         * We need the logic of this method to operate only once, and have access to the Context.
         * Otherwise, we would use the (static) BeforeSuiteScope hook.
         */
        $backup_file = $this->getWordpress()->getSetting('database_backup_file');
        if ($backup_file) {
            return;
        }

        // Get the file to use as a backup.
        $file = ! empty($db['backup_path']) ? $db['backup_path'] : '';

        // If the path specified is not a file, use it as the preferred folder to store a new backup.
        if (! $file || ! is_file($file) || ! is_readable($file)) {
            $file = $this->exportDatabase(['path' => $file]);
            $this->getWordpress()->setSetting('built_database_backup', true);
        }

        // Note: $file may be either an absolute OR relative file path.
        $this->getWordpress()->setSetting('database_backup_file', $file);
    }

    /**
     * When using the Selenium driver, position the admin bar to the top of the page, not fixed to the screen.
     * Otherwise the admin toolbar can hide clickable elements.
     *
     * @BeforeStep
     */
    public function fixToolbar()
    {
        $driver = $this->getSession()->getDriver();
        if (! $driver instanceof Selenium2Driver || ! $driver->getWebDriverSession()) {
            return;
        }

        try {
            $this->getSession()->getDriver()->executeScript(
                'if (document.getElementById("wpadminbar")) {
                    document.getElementById("wpadminbar").style.position="absolute";
                    if (document.getElementsByTagName("body")[0].className.match(/wp-admin/)) {
                        document.getElementById("wpadminbar").style.top="-32px";
                    }
                };'
            );
        } catch (\Exception $e) {
            /*
             * If a browser is not open, then Selenium2Driver::executeScript() will throw an exception.
             * In this case, our toolbar workaround obviously isn't required, so fail quietly.
             */
        }
    }

    /**
     * Clear Mink's browser environment.
     *
     * @AfterScenario
     */
    public function resetBrowser()
    {
        parent::resetBrowser();
    }

    /**
     * If database.restore_after_test is set, and scenario is tagged "db", restore the database from a backup.
     *
     * The database will be restored from a backup made via maybeBackupDatabase().
     *
     * @AfterScenario @db
     *
     * @param AfterScenarioScope $scope
     */
    public function maybeRestoreDatabase(AfterScenarioScope $scope)
    {
        $db = $this->getWordpressParameter('database');
        if (! $db['restore_after_test']) {
            return;
        }

        $file = $this->getWordpress()->getSetting('database_backup_file');
        if (! $file) {
            return;
        }

        $this->importDatabase(['path' => $file]);
    }

    /**
     * If a database dump exists, delete it after the test suite has completed.
     *
     * @AfterSuite @db
     *
     * @param Event\SuiteEvent $scope
     */
    public function maybeRemoveDatabaseDump(Event\SuiteEvent $event)
    {
        $delete_backup = $this->getWordpress()->getSetting('built_database_backup');
        if ($delete_backup === null) {
            return;
        }

        $file = $this->getWordpress()->getSetting('database_backup_file');
        if (! $file) {
            return;
        }

        $this->getWordpress()->setSetting('database_backup_file', false);

        // TODO: how to delete $file? Could be remote.
        //$this->importDatabase(['path' => $file]);
    }
}
