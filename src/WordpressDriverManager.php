<?php
namespace PaulGibbs\WordpressBehatExtension;

use PaulGibbs\WordpressBehatExtension\Driver\DriverInterface;
use PaulGibbs\WordpressBehatExtension\Driver\Element\ElementInterface;

use InvalidArgumentException;

/**
 * Manages driver registration.
 *
 * A driver represents and manages the connection between the Behat and WordPress environments.
 */
class WordpressDriverManager
{
    /**
     * The name of the default driver.
     *
     * @var string $default_driver
     */
    protected $default_driver = '';

    /**
     * All registered drivers.
     *
     * @var DriverInterface[]
     */
    protected $drivers = [];


    /**
     * Initialise the driver manager.
     *
     * @param DriverInterface[] $drivers An array of drivers to register.
     */
    public function __construct($drivers = [])
    {
        foreach ($drivers as $name => $driver) {
            $this->registerDriver($name, $driver);
        }
    }

    /**
     * Register a new driver.
     *
     * @param string          $name   Driver name.
     * @param DriverInterface $driver An instance of a DriverInterface.
     */
    public function registerDriver($name, DriverInterface $driver)
    {
        $name = strtolower($name);  // maybe this should make string safe for array key?
        $this->drivers[$name] = $driver;
    }

    /**
     * Register a new driver element.
     *
     * @param string           $name    Register the element with this driver.
     * @param ElementInterface $element An instance of a ElementInterface.
     */
    public function registerDriverElement($name, ElementInterface $element)
    {
        $this->getDriver($name)->addElement($element);
    }
    //registerElements? or in the BaseDriver?

    /**
     * Return a registered driver by name (defaults to the default driver).
     *
     * @param string $name Optional. The name of the driver to return. If omitted, the default driver is returned.
     * @return DriverInterface The requested driver.
     */
    public function getDriver($name = '')
    {
        $name = strtolower($name) ?: $this->default_driver;

        if (! isset($this->drivers[$name])) {
            throw new InvalidArgumentException("Driver '{$name}' is not registered.");
        }

        $driver = $this->drivers[$name];

        // Bootstrap driver if needed.
        if (! $driver->isBootstrapped()) {
            $driver->bootstrap();
        }

        return $driver;
    }

    /**
     * Returns all registered drivers.
     *
     * @return DriverInterface[] An array of drivers.
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Set the default driver name.
     *
     * @param string $name Default driver name to set.
     */
    public function setDefaultDriverName($name)
    {
        $name = strtolower($name);

        if (! isset($this->drivers[$name])) {
            throw new InvalidArgumentException("Driver '{$name}' is not registered.");
        }

        $this->default_driver = $name;
    }
}
