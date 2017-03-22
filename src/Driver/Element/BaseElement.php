<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element;

use PaulGibbs\WordpressBehatExtension\WordpressDriverManager;

/**
 * Common base class for WordPress driver elements.
 *
 * An element represents a distinct item that a driver promises to implement.
 */
abstract class BaseElement implements ElementInterface
{
    /**
     * WordPress driver manager.
     *
     * @var WordpressDriverManager
     */
    protected $wordpress;

    /**
     * Constructor.
     *
     * @param WordpressDriverManager $wordpress
     */
    public function __construct(WordpressDriverManager $wordpress)
    {
        $this->wordpress = $wordpress;
    }
}
