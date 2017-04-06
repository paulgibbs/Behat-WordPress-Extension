






    /**
     * Create a user.
     *
     * @param string $user_login User login name.
     * @param string $user_email User email address.
     * @param array  $args       Optional. Extra parameters to pass to WordPress.
     * @return array {
     *     @type int    $id   User ID.
     *     @type string $slug User slug (nicename).
     * }
     */
    public function createUser($user_login, $user_email, $args = [])
    {
        // User.
        $wpcli_args = [$user_login, $user_email, '--porcelain'];
        $whitelist  = array(
            'ID', 'user_pass', 'user_nicename', 'user_url', 'display_name', 'nickname', 'first_name', 'last_name',
            'description', 'rich_editing', 'comment_shortcuts', 'admin_color', 'use_ssl', 'user_registered',
            'show_admin_bar_front', 'role', 'locale',
        );
        foreach ($whitelist as $option) {
            if (isset($args[$option])) {
                $wpcli_args["--{$option}"] = $args[$option];
            }
        }
        $user_id = (int) $this->wpcli('user', 'create', $wpcli_args)['stdout'];
        // User slug (nicename).
        $wpcli_args = [$user_id, '--field=user_nicename'];
        $user_slug  = $this->wpcli('user', 'get', $wpcli_args)['stdout'];
        return array(
            'id'   => $user_id,
            'slug' => $user_slug,
        );
    }

    /**
     * Delete a user.
     *
     * @param int   $id   ID of user to delete.
     * @param array $args Optional. Extra parameters to pass to WordPress.
     */
    public function deleteUser($id, $args = [])
    {
        $wpcli_args = [$id, '--yes'];
        $whitelist  = ['network', 'reassign'];
        foreach ($whitelist as $option => $value) {
            if (isset($args[$option])) {
                if (is_int($option)) {
                    $wpcli_args[] = "--{$value}";
                } else {
                    $wpcli_args[] = sprintf('%s=%s', $option, escapeshellarg($value));
                }
            }
        }
        $this->wpcli('user', 'delete', $wpcli_args);
    }

