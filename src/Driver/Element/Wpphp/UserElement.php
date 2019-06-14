<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpphp;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;
use UnexpectedValueException;

/**
 * WP-API driver element for managing user accounts.
 */
class UserElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return \WP_User The new item.
     */
    public function create($args)
    {
        $extra_roles = [];

        // Store multiple roles; WP can only assign one on user creation.
        if (! empty($args['role'])) {
            if (! is_array($args['role'])) {
                $args['role'] = array_map('trim', explode(',', $args['role']));
            }

            $extra_roles  = $args['role'];
            $args['role'] = array_shift($extra_roles);
        }

        $args    = wp_slash($args);
        $user_id = wp_insert_user($args);

        if (is_wp_error($user_id)) {
            throw new UnexpectedValueException(sprintf('[W615] Failed creating new user: %s', $user_id->get_error_message()));
        }

        $wp_user = get_user_by('ID', $user_id);

        // Assign any extra roles.
        foreach ($extra_roles as $role) {
            $wp_user->add_role($role);
        }

        return $this->get($user_id);
    }

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id Object ID.
     * @param array $args Optional data used to fetch an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return \WP_User The item.
     */
    public function get($id, $args = [])
    {
        if (is_numeric($id) || ! isset($args['by'])) {
            $type = 'ID';
        } else {
            $type = $args['by'];
        }

        $user = get_user_by($type, $id);

        if (! $user) {
            throw new UnexpectedValueException(sprintf('[W616] Could not find user with ID %d', $id));
        }

        return $user;
    }

    /**
     * Checks that the username and password are correct.
     *
     * @deprecated 3.1.0 Redundant. Now just a no-op.
     * @todo Remove in 4.0.0.
     *
     * @param string $username
     * @param string $password
     *
     * @return true
     */
    public function validateCredentials(string $username, string $password)
    {
        return true;
    }

    /**
     * Delete an item for this element.
     *
     * @param int $id Object ID.
     * @param array $args Optional data used to delete an object.
     *
     * @throws \UnexpectedValueException
     */
    public function delete($id, $args = [])
    {
        if (! function_exists('\wp_delete_user')) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
        }

        $result = wp_delete_user($id, $args);

        if (! $result) {
            throw new UnexpectedValueException('[W617] Failed deleting user');
        }
    }
}
