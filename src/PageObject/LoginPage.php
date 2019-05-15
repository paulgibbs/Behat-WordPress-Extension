<?php
declare(strict_types=1);

namespace PaulGibbs\WordpressBehatExtension\PageObject;

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use PaulGibbs\WordpressBehatExtension\Context\RawWordpressContext;

/**
 * Page object representing the WordPress login page.
 *
 * This class houses methods for interacting with the login page and login form.
 */
class LoginPage extends Page
{
    /**
     * @var string $path
     */
    protected $path = 'wp-login.php';

    /**
     * Asserts the current screen is the login page.
     *
     * @throws ExpectationException
     */
    protected function verifyLoginPage()
    {
        $session = $this->verifySession();
        $page    = $session->getPage();
        $url     = $session->getCurrentUrl();

        if (false === strrpos($url, $this->path)) {
            // If the login path isn't in the current URL, we aren't on the login screen.
            throw new ExpectationException(
                sprintf(
                    'Expected screen is the wp-login form, instead on "%1$s".',
                    $url
                ),
                $this->getDriver()
            );
        }

        $selector   = '#loginform';
        $login_form = $page->find('css', $selector);

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
     * Fills the user_login field of the login form with a given username.
     *
     * @param string $username the username to fill into the login form
     *
     * @throws ExpectationException
     */
    public function setUserName(string $username)
    {
        $session = $this->verifySession();
        $page    = $session->getPage();

        $this->verifyLoginPage();

        $user_login_field = $page->find('css', '#user_login');

        try {
            $user_login_field->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        // Set the value of $username in the user_login field.
        // The field can be stubborn, so we use fillField also.
        $user_login_field->setValue($username);
        $page->fillField('user_login', $username);

        try {
            $session->executeScript(
                "document.getElementById('user_login').value='$username'"
            );
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for drivers without JavaScript support
        }

        $username_actual = $user_login_field->getValue();

        if ($username_actual !== $username) {
            throw new ExpectationException(
                sprintf(
                    'Expected the username field to be "%1$s", found "%2$s".',
                    $username,
                    $$username_actual
                ),
                $this->getDriver()
            );
        }
    }

    /**
     * Fills the user_pass field of the login form with a given password.
     *
     * @param string $password the password to fill into the login form
     *
     * @throws ExpectationException
     */
    public function setUserPassword(string $password)
    {
        $session = $this->verifySession();
        $page    = $session->getPage();

        $this->verifyLoginPage();

        $user_pass_field = $page->find('css', '#user_pass');

        try {
            $user_pass_field->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        // Set the value of $password in the user_pass field.
        // The field can be stubborn, so we use fillField also.
        $user_pass_field->setValue($password);
        $page->fillField('user_pass', $password);

        try {
            $session->executeScript(
                "document.getElementById('user_pass').value='$password'"
            );
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for drivers without JavaScript support
        }

        $password_actual = $user_pass_field->getValue();

        if ($password_actual !== $password) {
            throw new ExpectationException(
                sprintf(
                    'Expected the password field to be "%1$s", found "%2$s".',
                    $password,
                    $$password_actual
                ),
                $this->getDriver()
            );
        }
    }

    /**
     * Submit the WordPress login form
     */
    public function submitLoginForm()
    {
        $session = $this->verifySession();
        $page    = $session->getPage();

        $this->verifyLoginPage();

        $submit_button = $page->find('css', '#wp-submit');

        try {
            $submit_button->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }

        $submit_button->click();
    }

     /**
     * Verify and return a properly started Mink session
     *
     * @return \Behat\Mink\Session Mink session.
     */
    protected function verifySession()
    {
        $session = $this->getSession();

        if (! $session->isStarted()) {
            $session->start();
        }

        // Check we are on some web page.
        if ('about:blank' === $session->getCurrentUrl()) {
            $session->visit('/');
        }

        return $session;
    }
}
