<?php
namespace PaulGibbs\WordpressBehatExtension\PageObject\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use PaulGibbs\WordpressBehatExtension\Util;

/**
 * An Element representing the admin menu.
 */
class AdminMenu extends Element
{
    /**
     * @var array|string $selector
     */
    protected $selector = '#adminmenu';

    /**
     * Obtains a list of the top-leve menu items.
     *
     * The list contains the 'human readable' strings. e.g. 'Dashboard'.
     *
     * @return array List of top level menu links. E.g. [ 'Dashboard', 'Posts',...].
     */
    public function getTopLevelMenuItems()
    {
        $menu_item_nodes = $this->findAll('css', '#adminmenu > li a .wp-menu-name');
        $menu_item_texts = array();

        foreach ($menu_item_nodes as $n => $element) {
            $menu_item_texts[] = Util\stripTagsAndContent($element->getHtml());
        }

        return $menu_item_texts;
    }

    /**
     * Click a specific item in the admin menu.
     *
     * Top-level items are identified by their link text (e.g. 'Comments' ).
     * Second-level items are identified by their parent text and link text,
     * delimited by a right angle bracket. E.g. Posts > Add New.
     *
     * @param string $item The menu item to click.
     * @throws \Exception If the menu item does not exist
     */
    public function clickMenuItem($item)
    {
        $item = array_map('trim', preg_split('/(?<!\\\\)>/', $item));
        $click_node = false;

        $first_level_items = $this->findAll('css', 'li.menu-top');

        foreach ($first_level_items as $first_level_item) {
            // We use getHtml and strip the tags, as `.wp-menu-name` might not be visible (i.e. when the menu is
            // collapsed) so getText() will be empty.
            // @link https://github.com/stephenharris/WordPressBehatExtension/issues/2
            $item_name = Util\stripTagsAndContent($first_level_item->find('css', '.wp-menu-name')->getHtml());

            if (strtolower($item[0]) === strtolower($item_name)) {
                if (isset($item[1])) {
                    $second_level_items = $first_level_item->findAll('css', 'ul li a');

                    foreach ($second_level_items as $second_level_item) {
                        $item_name = Util\stripTagsAndContent($second_level_item->getHtml());
                        if (strtolower($item[1]) === strtolower($item_name)) {
                            try {
                                // Focus on the menu link so the submenu appears
                                $first_level_item->find('css', 'a.menu-top')->focus();
                            } catch (UnsupportedDriverActionException $e) {
                                // This will fail for GoutteDriver but neither is it necessary
                            }
                            $click_node = $second_level_item;
                            break;
                        }
                    }
                } else {
                    // We are clicking a top-level item:
                    $click_node = $first_level_item->find('css', 'a');
                }
                break;
            }
        }

        if (false === $click_node) {
            throw new \Exception('Menu item could not be found');
        }

        $click_node->click();
    }
}
