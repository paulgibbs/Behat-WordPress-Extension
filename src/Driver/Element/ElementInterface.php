<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element;

/**
 * WordPress driver element interface.
 *
 * An element represents a distinct item that a driver promises to implement.
 * For example, functionality to do with content, site, or users, etc.
 * In some ways, an element is similar to a collection endpoint in a RESTful API.
 */
interface ElementInterface
{
    /**
     * Create a thing for the relevant Context for a specific driver.
     */
    public function create();

    /**
     * Retrieve a thing for the relevant Context for a specific driver.
     */
    public function get();

    /**
     * Update a thing for the relevant Context for a specific driver.
     */
    public function update();

    /**
     * Delete a thing for the relevant Context for a specific driver.
     */
    public function delete();
}
