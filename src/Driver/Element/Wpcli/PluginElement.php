

    /**
     * Activate a plugin.
     *
     * @param string $plugin
     */
    public function activatePlugin($plugin)
    {
        $this->wpcli('plugin', 'activate', [$plugin]);
    }
    /**
     * Deactivate a plugin.
     *
     * @param string $plugin
     */
    public function deactivatePlugin($plugin)
    {
        $this->wpcli('plugin', 'deactivate', [$plugin]);
    }


