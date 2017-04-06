<?php
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;

/**
 * WP-CLI driver element for post comments.
 */
class CommentElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     * @return int New object ID.
     */
    public function create($args)
    {
        $wpcli_args = ['--porcelain'];
        $whitelist  = array(
            'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_date',
            'comment_date_gmt', 'comment_parent', 'comment_post_ID', 'user_id', 'comment_agent', 'comment_author_IP',
        );

        foreach ($whitelist as $option) {
            if (isset($args[$option])) {
                $wpcli_args["--{$option}"] = $args[$option];
            }
        }

        return (int) $this->drivers->getDriver()->wpcli('comment', 'create', $wpcli_args)['stdout'];
    }

    /**
     * Delete an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Data used to delete an object.
     */
    public function delete($id, $args)
    {
        $wpcli_args = [$id];
        $whitelist  = ['force'];

        foreach ($whitelist as $option) {
            if (isset($args[$option])) {
                $wpcli_args[] = "--{$option}";
            }
        }

        $this->drivers->getDriver()->wpcli('comment', 'delete', $wpcli_args);
    }
}
