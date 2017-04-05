<?php
namespace PaulGibbs\WordpressBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;

/**
 * Provides step definitions for creating content: post types, comments, and terms.
 */
class ContentContext extends RawWordpressContext
{
    /**
     * Create content of the given type.
     *
     * Example: Given there are posts:
     *     | post_type | post_title | post_content | post_status |
     *     | page      | Test Post   | Hello World  | publish     |
     *
     * @Given /^(?:there are|there is a) posts?:/
     *
     * @param TableNode $posts
     */
    public function thereArePosts(TableNode $posts)
    {
        foreach ($posts->getHash() as $post) {
            $this->createContent($this->parseArgs($post));
        }
    }

    /**
     * Create content, and go to it in the browser.
     *
     * Example: Given I am viewing a post:
     *     | post_type | post_title | post_content | post_status |
     *     | page      | Test Post   | Hello World  | publish     |
     *
     * @Given /^(?:I am|they are) viewing a(?: blog)? post(?: "([^"]+)"|:)/
     *
     * @param TableNode|string $post_data_or_title
     */
    public function iAmViewingBlogPost($post_data_or_title)
    {
        // Retrieve the first row only
        if ($post_data_or_title instanceof TableNode) {
            $post_data_hash = $post_data_or_title->getHash();
            if (count($post_data_hash) > 1) {
                throw new \UnexpectedValueException('"Given I am viewing a post:" step must only contain one post');
            }
            $post = $this->createContent($this->parseArgs($post_data_hash[0]));
        } else {
            $post = $this->getDriver()->getContentFromTitle($post_data_or_title);
        }
        $this->visitPath($post['url']);
    }

    /**
     * Converts data from TableNode into a format understood by Driver\DriverInterface;
     * i.e. converts public identifiers (such as slugs, log-ins) to internal identifiers
     * (such as database IDs).
     * @param $postData array
     * @return array
     * @throws \UnexpectedValueException If provided data is invalid
     */
    private function parseArgs($postData)
    {
        if (isset($postData['post_author'])) {
            $userId = $this->getDriver()->getUserIdFromLogin($postData['post_author']);
            $postData['post_author'] = (int) $userId;
        }
        return $postData;
    }
}
