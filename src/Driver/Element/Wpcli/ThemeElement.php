<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for themes.
 */
class ThemeElement extends BaseElement
{
    /**
     * Switch active theme.
     *
     * @param string $id   Theme name to switch to.
     * @param array  $args Data used to update an object.
     */
    public function update($id, $args)
    {
        $this->drivers->getDriver()->wpcli('theme', 'activate', [$id]);
    }

    // djpaultodo: helper functions? activate?
}
