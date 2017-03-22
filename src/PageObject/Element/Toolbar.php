<?php
namespace PaulGibbs\WordpressBehatExtension\PageObject\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Exception\ExpectationException;
use PaulGibbs\WordpressBehatExtension\Util;

/**
 * An Element representing the admin menu.
 */
class Toolbar extends Element
{
    /**
     * @var array|string $selector
     */
    protected $selector = '#wpadminbar';

    /**
     * Click a specific item in the toolbar.
     *
     * Top-level items are identified by their link text (e.g. 'Comments' ).
     * Second-level items are identified by their parent text and link text,
     * delimited by a right angle bracket. E.g. New > Post.
     *
     * @param string $link The toolbar item to click.
     * @throws ExpectationException If the menu item does not exist
     */
    public function clickToolbarLink($link)
    {
        $link_parts = array_map('trim', preg_split('/(?<!\\\\)>/', $link));
        $click_node = false;

        $first_level_items = $this->findAll('css', '.ab-top-menu > li');

        foreach ($first_level_items as $first_level_item) {
            if ($this->elementIsTargetLink($first_level_item, $link_parts[0])) {
                if (count($link_parts) > 1) {
                    $click_node = $this->getSubmenuLinkNode($first_level_item, $link_parts[1]);
                } else {
                    // We are clicking a top-level item:
                    $click_node = $first_level_item->find('css', 'a');
                }
                break;
            }
        }

        if (false === $click_node) {
            throw new ExpectationException(
                sprintf('Toolbar link "%s" could not be found', $link),
                $this->getDriver()
            );
        }

        $click_node->click();
    }

    /**
     * Determines whether the link name refers to the given NodeElement.
     *
     * Some toolbar links do not have any visible text, so we handle those
     * seperately. Otherwise we compoare the link text to the text of the
     * element.
     *
     * @param NodeElement $element The element to check
     * @param string $link_text The link (text) to check for
     * @return bool True if $link_text refers to $element. False otherwise.
     */
    protected function elementIsTargetLink($element, $link_text)
    {
        $current_item_name = strtolower($element->find('css', '.ab-item')->getText());

        switch (strtolower($link_text)) :
            case 'wordpress':
                $is_link = $element->getAttribute('id') === 'wp-admin-bar-wp-logo';
                break;
            case 'updates':
                $is_link = $element->getAttribute('id') === 'wp-admin-bar-updates';
                break;
            case 'comments':
                $is_link = $element->getAttribute('id') === 'wp-admin-bar-comments';
                break;
            default:
                $is_link = strtolower($link_text) === $current_item_name;
        endswitch;

        return $is_link;
    }

    /**
     * Retruns a second-level toolbar NodeElement corresponding to the link text
     * under the given first-level toolbar NodeElement.
     *
     * @param NodeElement $first_level_item The element to check under
     * @param string $link_text The link (text) to check for
     * @return NodeElement|null
     */
    protected function getSubmenuLinkNode($first_level_item, $link_text)
    {
        $second_level_items = $first_level_item->findAll('css', 'ul li a');
        $submenu_link_node = null;
        foreach ($second_level_items as $second_level_item) {
            $current_item_name = Util\stripTagsAndContent($second_level_item->getHtml());
            if (strtolower($link_text) === strtolower($current_item_name)) {
                try {
                    // "Focus" (add hover class) on the toolbar link so the submenu appears
                    $id = $first_level_item->getAttribute('id');
                    $this->getDriver()->evaluateScript(
                        'jQuery("#' . $id . '").addClass("hover");'
                    );
                } catch (UnsupportedDriverActionException $e) {
                    // This will fail for GoutteDriver but neither is it necessary
                }
                $submenu_link_node = $second_level_item;
                break;
            }
        }
        return $submenu_link_node;
    }

    /**
     * Searches for the given text using the toolbar search field.
     *
     * @param string $text Text to enter into the search field
     */
    public function search($text)
    {
        $search = $this->find('css', '#adminbarsearch');

        if (! $search) {
            throw new ExpectationException(
                'Search field in the toolbar could not be found',
                $this->getDriver()
            );
        }

        try {
            $search->find('css', '#adminbar-search')->press();
            $search->find('css', '#adminbar-search')->focus();
            $search->fillField('adminbar-search', $text);
            $search->find('css', '#adminbar-search')->focus();
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary
            $search->fillField('adminbar-search', $text);
        }

        $search->submit();
    }

    /**
     * Logs-out using the toolbar log-out link.
     */
    public function logOut()
    {
        // Using NodeElement::mouseOver() won't work because WordPress is using hoverIndent. Instead we just
        // manually add the hover class. See https://github.com/paulgibbs/behat-wordpress-extension/issues/65
        try {
            $this->getDriver()->evaluateScript(
                'jQuery("#wp-admin-bar-my-account").addClass("hover");'
            );
        } catch (UnsupportedDriverActionException $e) {
            // This will fail for GoutteDriver but neither is it necessary
        }
        $this->find('css', '#wp-admin-bar-logout a')->click();
    }
}
