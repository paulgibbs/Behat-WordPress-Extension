<?php
namespace PaulGibbs\WordpressBehatExtension\Util;

/**
 * Wrap a closure in a spin check.
 *
 * This is a technique to accommodate in-progress state changes in a web page (i.e. waiting for new data to load)
 * by retrying the action for a given number of attempts, each delayed by 1/4 second. The closure is expected to
 * throw an exception should the expected state not (yet) exist.
 *
 * To avoid doubt, you should only need to spin when waiting for an AJAX response, after initial page load.
 *
 * @param callable $closure Action to execute.
 * @param int      $wait    Optional. How long to wait before giving up, in seconds.
 * @param int      $step    Optional. How long to wait between attempts, in micro seconds.
 * @throws \Exception       Rethrows the exception thrown by the $closure if the expectation has not been met after $wait seconds.
 */
function spins(callable $closure, $wait = 60, $step = 250000)
{
    $error     = null;
    $stop_time = time() + $wait;

    while (time() < $stop_time) {
        try {
            call_user_func($closure);
            return;
        } catch (\Exception $e) {
            $error = $e;
        }

        usleep($step);
    }

    throw $error;
}

/**
 * Extracts 'top level' text from HTML.
 *
 * All HTML tags, and their contents are removed.
 *
 * e.g. Some <span>HTML and</span>text  -->  Some text
 *
 * @param string $html Raw HTML
 * @return string Extracted text. e.g. Some <span>HTML and</span>text  -->  Some text
 */
function stripTagsAndContent($html)
{
    if (trim($html) === '') {
        return $html;
    }

    $doc = new \DOMDocument();
    $doc->loadHTML("<div>{$html}</div>");

    $container = $doc->getElementsByTagName('div')->item(0);

    // Remove nodes while iterating over them does not work
    // @link http://php.net/manual/en/domnode.removechild.php#90292
    $remove_queue = array();
    foreach ($container->childNodes as $child_node) {
        if ($child_node->nodeType !== XML_TEXT_NODE) {
            $remove_queue[] = $child_node;
        }
    }

    foreach ($remove_queue as $node) {
        $container->removeChild($node);
    }

    return trim($container->textContent);
}

/**
 * Is the specified item's class a WordPress error object?
 *
 * @param object $item
 * @return bool
 */
function is_wordpress_error($item)
{
    return (is_object($item) && get_class($item) === 'WP_Error');
}
