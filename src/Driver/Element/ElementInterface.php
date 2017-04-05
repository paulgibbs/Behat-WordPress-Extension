<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element;

/**
 * WordPress driver element interface.
 *
 * An element represents a distinct item that a driver promises to implement.
 * An element must be created for each driver that the item is supported in.
 *
 * For example, functionality to do with content, site, or users.
 * In some ways, an element is similar to a collection endpoint in a RESTful API.
 */
interface ElementInterface
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     * @return int|string New object ID.
     */
    public function create($args);

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to fetch an object.
     */
    public function get($id, $args);

    /**
     * Update an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to update an object.
     */
    public function update($id, $args);

    /**
     * Delete an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to delete an object.
     */
    public function delete($id, $args);
}
