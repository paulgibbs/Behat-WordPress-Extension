<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context\Traits;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PaulGibbs\WordpressBehatExtension\PageObject\LoginPage;
use UnexpectedValueException;

/**
 * Provides driver agnostic logic (helper methods) relating to users.
 */
trait UserAwareContextTrait
{
    use BaseAwarenessTrait;

    /**
     * Login form page object.
     *
     * @var LoginPage
     */
    public $login_page;

    /**
     * Constructor.
     *
     * @param LoginPage $login_page The page object representing the login page.
     */
    public function __construct(LoginPage $login_page)
    {
        parent::__construct();
        $this->login_page = $login_page;
    }

    /**
     * Log in the user.
     *
     * @param string $username
     * @param string $password
     * @param string $redirect_to Optional. After succesful log in, redirect browser to this path. Default = "/".
     *
     * @throws ExpectationException
     */
    public function logIn(string $username, string $password, string $redirect_to = '/')
    {
        if ($this->loggedIn()) {
            $this->logOut();
        }

        $session = $this->getSession();

        /*
         * Ensure log-in page is not the first loaded page in the session. We believe
         * this to be a cause of form field manipulation intermittently failng, causing
         * the log-in step to fail -- as "Given I am logged in as a user" is often the
         * the first line in a scenario!
         *
         * See https://github.com/Behat/MinkExtension/issues/286
         */
        $this->visitPath('/');
        $this->visitPath('wp-login.php?redirect_to=' . urlencode($this->locatePath($redirect_to)));

        $this->login_page->setUserName($username);
        $this->login_page->setUserPassword($password);

        // Submit the login form.
        $this->login_page->submitLoginForm();

        if (! $this->loggedIn()) {
            throw new ExpectationException('[W803] The user could not be logged-in.', $this->getSession()->getDriver());
        }
    }

    /**
     * Log the current user out.
     */
    public function logOut()
    {
        if (! $this->loggedIn()) {
            return;
        }

        try {
            $this->getElement('Toolbar')->logOut();
        } catch (DriverException $e) {
            // This may fail if the user has not loaded any site yet.
        }
    }

    /**
     * Determine if the current user is logged in or not.
     *
     * @return bool
     */
    public function loggedIn(): bool
    {
        $session = $this->getSession();
        if (! $session->isStarted()) {
            $session->start();
            // If there isn't a session started the user can't be logged in.
            return false;
        }

        $page = $session->getPage();

        // Look for a selector to determine if the user is logged in.
        try {
            // Search for the body element.
            $body_element = $page->find('css', 'body');

            // If the page doesn't have a body element the user is not logged in.
            if (null === $body_element) {
                return false;
            }

            // The user is logged in if:
            $is_logged_in = (
                // The body has a logged-in class (front-end).
                $body_element->hasClass('logged-in') ||
                // The body has a wp-admin class (dashboard)
                $body_element->hasClass('wp-admin')
            );
            return $is_logged_in;
        } catch (DriverException $e) {
            // This may fail if the user has not loaded any site yet.
        }

        return false;
    }

    /**
     * Create a user.
     *
     * If the user already exists, the existing user will be
     * compared with the user which was asked to be created. If all matches
     * no fault with be thrown. If it does not match then UnexpectedValueException
     * will be thrown.
     *
     * @param string $user_login User login name.
     * @param string $user_email User email address.
     * @param array  $args       Optional. Extra parameters to pass to WordPress.
     *
     * @return array {
     *             @type int $id User ID.
     *             @type string $slug User slug (nicename).
     *         }
     */
    public function createUser(string $user_login, string $user_email, array $args = []): array
    {
        $args['user_email'] = $user_email;
        $args['user_login'] = $user_login;

        try {
            $user = $this->getDriver()->user->create($args);
        } catch (UnexpectedValueException $exception) {
            $user = $this->getExistingMatchingUser($args);
        }

        $return_array = array(
            'id'   => $user->ID,
            'slug' => $user->user_nicename
        );

        return $return_array;
    }

    /**
     * Get a user which matches all parameters.
     *
     * Fetches a user if all the passed parameters match
     * if none is found then UnexpectedValueException is thrown.
     *
     * @param array $args Keyed array of parameters.
     *
     * @throws \UnexpectedValueException
     *
     * @return object $user
     */
    protected function getExistingMatchingUser(array $args)
    {
        $user_id = $this->getUserIdFromLogin($args['user_login']);
        $user    = $this->getDriver()->user->get($user_id);

        /* users can have more than one role so needs to be a special case */
        if (array_key_exists('role', $args)) {
            $this->checkUserHasRole($user, $args['role']);
        }

        /*
         * Loop through each of the passed arguements.
         * if they are arguments which apply to users
         * then check that that which exist matches that which was specified.
         */
        foreach ($args as $parameter => $value) {
            if ($parameter === 'password') {
                continue;
            }

            if ($this->isValidUserParameter($parameter) && $user->$parameter !== $args[$parameter]) {
                throw new UnexpectedValueException('[W804] User with login: ' . $user->user_login . 'exists, but ' . $parameter . ' is ' . $user->$parameter . ' not ' . $args[$parameter] . 'which was specified');
            }
        }

        return $user;
    }

    /**
     * Checks to see if the user has an assigned role or not.
     *
     * @param object $user
     * @param string $role
     *
     * @throws \UnexpectedValueException
     *
     * @return boolean $retval True if the role does apply to the user.
     */
    protected function checkUserHasRole($user, string $role): bool
    {
        if (! in_array($role, $user->roles, true)) {
            $message = sprintf(
                '[W804] User with login: %s exists, but role %s is not in the list of applied roles: %s',
                $user->user_login,
                $role,
                $user->roles
            );
            throw new \UnexpectedValueException($message);
        }

        return true;
    }

    /**
     * Checks to see if the passed in parameter applies to a user or not.
     *
     * @param string $user_parameter the parameter to be checked.
     *
     * @return boolean $retval True if the parameter does apply to a user.
     */
    protected function isValidUserParameter(string $user_parameter): bool
    {
        $validUserParameters = array(
            'id',
            'user_login',
            'display_name',
            'user_email',
            'user_registered',
            'roles',
            'user_nicename',
            'user_url',
            'user_activation_key',
            'user_status',
            'url'
        );
        return in_array(strtolower($user_parameter), $validUserParameters, true);
    }

    /**
     * Get a user's ID from their username.
     *
     * @param string $username The username of the user to get the ID of.
     *
     * @return int ID of the user.
     */
    public function getUserIdFromLogin(string $username): int
    {
        return (int) $this->getDriver()->user->get($username, ['by' => 'login'])->ID;
    }

    /**
     * Delete a user.
     *
     * @param int $user_id ID of user to delete.
     * @param array $args  Optional. Extra parameters to pass to WordPress.
     */
    public function deleteUser(int $user_id, array $args = [])
    {
        $this->getDriver()->user->delete($user_id, $args);
    }

    /**
     * Get a piece of user data from their username.
     *
     * @param string $data     The user data to return (the name of a column from the WP_Users table).
     * @param string $username The username of the user to fetch a property from.
     *
     * @return mixed The specified user data.
     */
    public function getUserDataFromUsername(string $data, string $username)
    {
        return $this->getDriver()->user->get($username, ['by' => 'login'])->{$data};
    }
}
