<?php
declare(strict_types=1);

namespace PaulGibbs\WordpressBehatExtension\PageObject;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;

/**
 * Page object representing the WordPress log-in page.
 *
 * This class houses methods for interacting with the log-in page and log-in form.
 */
class LoginPage extends Page
{
    /**
     * @var string $path
     */
    protected $path = 'wp-login.php';

    /**
     * Asserts the current screen is the log-in page.
     *
     * @throws ExpectationException
     */
    protected function verifyPage()
    {
        $session = $this->getSession();
        $url     = $session->getCurrentUrl();

        if (false === strrpos($url, $this->path)) {
            throw new ExpectationException(
                sprintf(
                    'Expected screen is the wp-login form, instead on "%1$s".',
                    $url
                ),
                $this->getDriver()
            );
        }

        $selector   = '#loginform';
        $login_form = $session->getPage()->find('css', $selector);

        if (null === $login_form) {
            throw new ExpectationException(
                sprintf(
                    'Expected to find the login form with the selector "%1$s" at the current URL "%2$s".',
                    $selector,
                    $url
                ),
                $this->getDriver()
            );
        }
    }

    /**
     * Fills the user_login field of the log-in form with a given username.
     *
     * @param string $username the username to fill into the log-in form
     *
     * @throws ExpectationException
     */
    public function setUserName(string $username)
    {
        $session = $this->getSession();
        $field   = $session->getPage()->find('css', '#user_login');

        try {
            $field->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        $field->setValue($username);

        try {
            $session->executeScript("document.getElementById('user_login').value='$username'");
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for drivers without JavaScript support.
        }
    }

    /**
     * Fills the user_pass field of the log-in form with a given password.
     *
     * @param string $password the password to fill into the log-in form
     *
     * @throws ExpectationException
     */
    public function setUserPassword(string $password)
    {
        $session = $this->getSession();
        $field   = $session->getPage()->find('css', '#user_pass');

        try {
            $field->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        $field->setValue($password);

        try {
            $session->executeScript("document.getElementById('user_pass').value='$password'");
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for drivers without JavaScript support
        }
    }

    /**
     * Mark the "remember me" input box on the log-in form.
     */
    public function setRememberMe()
    {
        $session = $this->getSession();
        $field   = $session->getPage()->find('css', '#rememberme');

        try {
            $field->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        $field->check();
    }

    /**
     * Submit the log-in form.
     */
    public function submitLoginForm()
    {
        $session = $this->getSession();
        $button  = $session->getPage()->find('css', '#wp-submit');

        try {
            $button->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        $button->click();
    }
}
