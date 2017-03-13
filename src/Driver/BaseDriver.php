<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

use PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException;

/**
 * Common base class for WordPress drivers.
 *
 * A driver represents and manages the connection between the Behat environment and a WordPress site.
 */
abstract class BaseDriver implements DriverInterface
{
    /**
     * Track driver bootstrapping.
     *
     * @var bool
     */
    protected $is_bootstrapped = false;

    /**
     * Registered driver elements.
     *
     * @var ElementInterface[]
     */
    protected $elements = [];

    /**
     * Expose $elements as public properties.
     *
     * @param string $name Element name.
     * @return ElementInterface|null Return object, or null if it doesn't exist.
     */
    public function __get($name)
    {
        if (isset($this->elements[$name])) {
            return $this->elements[$name];
        }

        return null;
    }

    /**
     * Has the driver has been bootstrapped?
     */
    public function isBootstrapped()
    {
        return $this->is_bootstrapped;
    }

    /**
     * Set up anything required for the driver.
     *
     * Called when the driver is used for the first time.
     */
    public function bootstrap()
    {
        $this->is_bootstrapped = true;
    }
}
