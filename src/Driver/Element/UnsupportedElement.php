<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element;

use PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException;

/**
 * Used when a driver does not offer support for a particular element.
 */
class UnsupportedElement extends BaseElement
{
    /**
     * Create an item for this element.
     */
    public function create()
    {
        throw new UnsupportedDriverActionException('clear cache in ' . get_class($this->wordpress->get_driver()));
    }

    /**
     * Retrieve an item for this element.
     */
    public function get()
    {
        throw new UnsupportedDriverActionException('clear cache in ' . get_class($this->wordpress->get_driver()));
    }

    /**
     * Update an item for this element.
     */
    public function update()
    {
        throw new UnsupportedDriverActionException('clear cache in ' . get_class($this->wordpress->get_driver()));
    }

    /**
     * Delete an item for this element.
     */
    public function delete()
    {
        throw new UnsupportedDriverActionException('clear cache in ' . get_class($this->wordpress->get_driver()));
    }
}
