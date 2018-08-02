<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context\Traits;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use UnexpectedValueException;

/**
 * Provides driver agnostic logic (helper methods) relating to users.
 */
trait UserAwareContextTrait
{
    use BaseAwarenessTrait;

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

        $this->visitPath('wp-login.php?redirect_to=' . urlencode($this->locatePath($redirect_to)));
        $page = $this->getSession()->getPage();

        $node = $page->findField('user_login');
        try {
            $node->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }
        // This is to make sure value is set properly.
        $node->setValue('');
        $node->setValue($username);
        $node->setValue($username);

        $node = $page->findField('user_pass');
        try {
            $node->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary.
        }
        // This is to make sure value is set properly.
        $node->setValue('');
        $node->setValue($password);
        $node->setValue($password);

        $page->findButton('wp-submit')->click();

        if (! $this->loggedIn()) {
            throw new ExpectationException('[W803] The user could not be logged-in.', $this->getSession()->getDriver());
        }
    }

    /**
     * Log the current user out.
     */
    public function logOut()
    {
        $this->getElement('Toolbar')->logOut();
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
        }

        $page = $session->getPage();

        // Look for a selector to determine if the user is logged in.
        try {
            return $page->has('css', 'body.logged-in');
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
                try {
                    if (! $this->getDriver()->user->validateCredentials($args['user_login'], $value)) {
                        throw new UnexpectedValueException('[W804] User with login: ' . $user->user_login . ' exists but password is incorrect');
                    }
                } catch (UnsupportedDriverActionException $exception) {
                    // WPCLI can't do this yet.
                }
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
            // 'user_pass', - exclude the password for the moment - need special logic for it
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
