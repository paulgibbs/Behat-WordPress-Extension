<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for content (i.e. blog posts).
 */
class ContentElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     * @return int|string New object ID.
     */
    public function create($args)
    {
    }

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to fetch an object.
     *
     * @return mixed The item.
     */
    public function get($id, $args)
    {
    }

    /**
     * Update an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to update an object.
     */
    public function update($id, $args)
    {
    }

    /**
     * Delete an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to delete an object.
     */
    public function delete($id, $args)
    {
    }
}
