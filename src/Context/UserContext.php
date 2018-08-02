<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\ExpectationException;
use RuntimeException;

/**
 * Provides step definitions for all things relating to users.
 */
class UserContext extends RawWordpressContext
{
    use Traits\UserAwareContextTrait, Traits\CacheAwareContextTrait;

    /**
     * Add specified user accounts.
     *
     * Example: Given there are users:
     *     | user_login | user_pass | user_email        | role          |
     *     | admin      | admin     | admin@example.com | administrator |
     *
     * @Given /^(?:there are|there is a) users?:/
     *
     * @param TableNode $users
     */
    public function thereAreUsers(TableNode $users)
    {
        $params = $this->getWordpressParameters();

        foreach ($users->getHash() as $user) {
            $this->createUser($user['user_login'], $user['user_email'], $user);

            $params['users'][] = array(
                'roles'    => $this->getUserDataFromUsername('roles', $user['user_login']),
                'username' => $user['user_login'],
                'password' => $user['user_pass'],
            );
        }

        $this->setWordpressParameters($params);
    }

    /**
     * Delete the specified user account.
     *
     * Example: When I delete the "test" user account
     *
     * @When I delete the :user_login user account
     *
     * @param string $user_login
     */
    public function iDeleteTheUserAccount(string $user_login)
    {
        $this->deleteUser($this->getUserIdFromLogin($user_login));
    }

    /**
     * Go to a user's author archive page.
     *
     * Example: When I am viewing posts published by Paul
     *
     * @When /^(?:I am|they are) viewing posts published by (.+)$/
     *
     * @param string $username
     */
    public function iAmViewingAuthorArchive(string $username)
    {
        $found_user = null;
        $users      = $this->getWordpressParameter('users');

        foreach ($users as $user) {
            if ($username === $user['username']) {
                $found_user = $user;
                break;
            }
        }

        if ($found_user === null) {
            throw new RuntimeException("[W801] User not found for name \"{$username}\"");
        }

        $this->visitPath(sprintf(
            $this->getWordpressParameters()['permalinks']['author_archive'],
            $this->getUserDataFromUsername('user_nicename', $found_user['username'])
        ));
    }

    /**
     * Log user out.
     *
     * Example: Given I am an anonymous user
     * Example: When I log out
     *
     * @Given /^(?:I am|they are) an anonymous user/
     * @When I log out
     */
    public function iAmAnonymousUser()
    {
        $this->logOut();
    }

    /**
     * Log user in (with role name).
     *
     * Example: Given I am logged in as an contributor
     *
     * @Given /^(?:I am|they are) logged in as an? (.+)$/
     *
     * @param string $role
     *
     * @throws \RuntimeException
     */
    public function iAmLoggedInAsRole(string $role)
    {
        $found_user = null;
        $users      = $this->getWordpressParameter('users');

        foreach ($users as $user) {
            if (in_array($role, $user['roles'], true)) {
                $found_user = $user;
                break;
            }
        }

        if ($found_user === null) {
            throw new RuntimeException("[W801] User not found for role \"{$role}\"");
        }

        $this->logIn($found_user['username'], $found_user['password']);
    }

    /**
     * Log user in (with user name).
     *
     * Example: Given I am logged in as Mince
     *
     * @Given /^(?:I am|they are) logged in as (?!an? )(.+)$/
     *
     * @param string $username
     *
     * @throws \RuntimeException
     */
    public function iAmLoggedInAsUser(string $username)
    {
        $found_user = null;
        $users      = $this->getWordpressParameter('users');

        foreach ($users as $user) {
            if ($username === $user['username']) {
                $found_user = $user;
                break;
            }
        }

        if ($found_user === null) {
            throw new RuntimeException("[W801] User not found for name \"{$username}\"");
        }

        $this->logIn($found_user['username'], $found_user['password']);
    }

    /**
     * Try to log user in (with role name), but expect failure.
     *
     * Example: Then I should not be able to log in as an editor
     *
     * @Then /^(?:I|they) should not be able to log in as an? (.+)$/
     *
     * @param string $role
     *
     * @throws ExpectationException
     */
    public function iShouldNotBeAbleToLogInAsRole(string $role)
    {
        try {
            $this->iAmLoggedInAsRole($role);
        } catch (ExpectationException $e) {
            // Expectation fulfilled.
            return;
        } catch (RuntimeException $e) {
            // Expectation fulfilled.
            return;
        }

        throw new ExpectationException(
            sprintf(
                '[W802] A user with role "%s" was logged-in succesfully. This should not have happened.',
                $role
            ),
            $this->getSession()->getDriver()
        );
    }

    /**
     * Try to log user in (with username), but expect failure.
     *
     * Example: Then I should not be able to log in as Scotty
     *
     * @Then /^(?:I|they) should not be able to log in as (.+)$/
     *
     * @param string $username
     *
     * @throws ExpectationException
     */
    public function iShouldNotBeAbleToLogInAsUser(string $username)
    {
        try {
            $this->iAmLoggedInAsUser($username);
        } catch (ExpectationException $e) {
            // Expectation fulfilled.
            return;
        } catch (RuntimeException $e) {
            // Expectation fulfilled.
            return;
        }

        throw new ExpectationException(
            sprintf(
                '[W802] The user "%s" was logged-in succesfully. This should not have happened.',
                $username
            ),
            $this->getSession()->getDriver()
        );
    }
}
