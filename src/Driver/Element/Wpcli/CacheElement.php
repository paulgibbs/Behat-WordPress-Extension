<?php
    /**
     * Clear object cache.
     */
    public function clearCache()
    {
        $this->wpcli('cache', 'flush');
    }
