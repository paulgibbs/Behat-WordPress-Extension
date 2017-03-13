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
     */
    public function create();

    /**
     * Retrieve an item for this element.
     */
    public function get();

    /**
     * Update an item for this element.
     */
    public function update();

    /**
     * Delete an item for this element.
     */
    public function delete();
}
