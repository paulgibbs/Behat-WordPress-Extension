<?php
namespace PaulGibbs\WordpressBehatExtension\Driver;

use PaulGibbs\WordpressBehatExtension\Exception\UnsupportedDriverActionException;
use PaulGibbs\WordpressBehatExtension\Driver\Element\ElementInterface;

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
     * @return ElementInterface|null Return element object.
     */
    public function __get($name)
    {
        if (isset($this->elements[$name])) {
            return $this->elements[$name];
        }

        throw new UnsupportedDriverActionException(sprintf('use the %s element', static::class));
    }

    /**
     * Has the driver has been bootstrapped?
     *
     * @return bool
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

    /**
     * Register an element for the driver.
     *
     * @param string           $name    Driver name.
     * @param ElementInterface $element An instance of a ElementInterface.
     */
    public function registerElement($name, ElementInterface $element)
    {
        $this->elements[$name] = $element;
    }
}
