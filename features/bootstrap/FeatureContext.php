<?php
use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;

use Behat\Behat\Context\SnippetAcceptingContext,
    Behat\Behat\Hook\Scope\AfterScenarioScope,
    Behat\Testwork\Tester\Result\TestResult;
/**
 * Define application features from the specific context.
 */
class FeatureContext extends RawWordpressContext implements SnippetAcceptingContext {

    /**
     * Initialise context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the context constructor through behat.yml.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @AfterScenario
     */
    public function printPageMarkUpAfterFailedStep(AfterScenarioScope $scope)
    {
        if (TestResult::FAILED === $scope->getTestResult()->getResultCode()) {
            echo $this->getSession()->getPage()->getHtml();
        }
    }

}
