<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\Driver\Element\Wpcli;

use PaulGibbs\WordpressBehatExtension\Driver\Element\BaseElement;
use UnexpectedValueException;
use function PaulGibbs\WordpressBehatExtension\Util\buildCLIArgs;

/**
 * WP-CLI driver element for content (i.e. blog posts).
 */
class ContentElement extends BaseElement
{
    /**
     * Create an item for this element.
     *
     * @param array $args Data used to create an object.
     *
     * @return mixed The new item.
     */
    public function create($args)
    {
        $meta_args = [];
        $tax_args  = [];

        /*
         * Array of taxonomy terms keyed by their taxonomy name, which WP-CLI can't handle.
         *
         * Values may be delimited by a comma.
         *
         * See https://github.com/wp-cli/entity-command/issues/44#issuecomment-325957179
         */
        if (isset($args['tax_input'])) {
            $tax_args = $args['tax_input'];
            unset($args['tax_input']);
        }

        /*
         * Array of post meta values keyed by their post meta key, which WP-CLI can't handle.
         *
         * See https://github.com/wp-cli/entity-command/issues/44#issuecomment-325957179
         */
        if (isset($args['meta_input'])) {
            $meta_args = $args['meta_input'];
            unset($args['meta_input']);
        }

        $wpcli_args = buildCLIArgs(
            array(
                'ID', 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_content_filtered', 'post_title',
                'post_excerpt', 'post_status', 'post_type', 'comment_status', 'ping_status', 'post_password', 'post_name',
                'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'post_mime_type',
                'guid', 'post_category', 'page_template',
            ),
            $args
        );

        array_unshift($wpcli_args, '--porcelain');
        $post_id = (int) $this->drivers->getDriver()->wpcli('post', 'create', $wpcli_args)['stdout'];

        // Apply taxonomy values.
        if ($tax_args) {
            foreach ($tax_args as $taxonomy => $terms) {
                $split_terms = str_getcsv($terms);

                foreach ($split_terms as $split_term) {
                    $args = [
                        $post_id,
                        $taxonomy,
                        escapeshellarg($split_term),
                    ];

                    $this->drivers->getDriver()->wpcli('post', 'term add', $args);
                }
            }
        }

        // Apply post meta values.
        if ($meta_args) {
            foreach ($meta_args as $meta_key => $meta_value) {
                $args = [
                    $post_id,
                    $meta_key,
                    escapeshellarg($meta_value),
                ];

                $this->drivers->getDriver()->wpcli('post', 'meta update', $args)['stdout'];
            }
        }

        return $this->get($post_id);
    }

    /**
     * Retrieve an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Optional data used to fetch an object.
     *
     * @throws \UnexpectedValueException
     *
     * @return mixed The item.
     */
    public function get($id, $args = [])
    {
        $url = '';

        // Support fetching via arbitrary field.
        if (! is_numeric($id)) {
            $wpcli_args = ['--fields=ID,url', "--{$args['by']}=" . escapeshellarg($id), '--post_type=any', '--format=json', '--ignore_sticky_posts=true'];
            $result     = json_decode($this->drivers->getDriver()->wpcli('post', 'list', $wpcli_args)['stdout']);

            if (empty($result)) {
                throw new UnexpectedValueException(sprintf('[W501] Could not find post with ID %d', $id));
            }

            $id  = (int) $result[0]->ID;
            $url = $result[0]->url;
        }

        // Fetch by ID.
        $wpcli_args = buildCLIArgs(
            array(
                'field',
                'fields',
            ),
            $args
        );

        array_unshift($wpcli_args, $id, '--format=json');
        $post = $this->drivers->getDriver()->wpcli('post', 'get', $wpcli_args)['stdout'];
        $post = json_decode($post);

        if (! $post) {
            throw new UnexpectedValueException(sprintf('[W501] Could not find post with ID %d', $id));
        }

        if (! $url) {
            $wpcli_args = ['--post__in=' . $post->ID, '--fields=url', '--post_type=any', '--format=json'];
            $result     = json_decode($this->drivers->getDriver()->wpcli('post', 'list', $wpcli_args)['stdout']);
            $url        = $result[0]->url;
        }

        $post->url = $url;

        return $post;
    }

    /**
     * Delete an item for this element.
     *
     * @param int|string $id   Object ID.
     * @param array      $args Optional data used to delete an object.
     */
    public function delete($id, $args = [])
    {
        $wpcli_args = buildCLIArgs(
            ['force', 'defer-term-counting'],
            $args
        );

        array_unshift($wpcli_args, $id);

        $this->drivers->getDriver()->wpcli('post', 'delete', $wpcli_args);
    }
}
