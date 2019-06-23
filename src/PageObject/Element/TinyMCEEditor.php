<?php
declare(strict_types=1);
namespace PaulGibbs\WordpressBehatExtension\PageObject\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

/**
 * An Element representing a WYSIWYG editor.
 */
class TinyMCEEditor extends Element
{
    /**
     * The available modes for the WYSIWYG editor.
     */
    const VISUAL = 'VISUAL';
    const TEXT   = 'TEXT';

    /**
     * @var string The element ID of the underlying iFrame.
     */
    protected static $wysiwyg_iframe_id = 'content_ifr';

    /**
     * @var string The element ID of the underlying textarea.
     */
    protected static $textarea_id = 'content';

    /**
     * Sets the mode of the WYSIWYG editor.
     *
     * @param string $mode VISUAL or TEXT.
     */
    public function setMode(string $mode)
    {
        if (self::VISUAL === $mode) {
            $this->find('css', '#content-tmce')->press();
        } else {
            $this->find('css', '#content-html')->press();
        }
    }

    /**
     * Returns the mode of the WYSIWYG editor.
     *
     * @return string Either 'VISUAL' or 'TEXT'.
     */
    public function getMode(): string
    {
        return $this->find('css', '#wp-content-wrap')->hasClass('tmce-active') ? self::VISUAL : self::TEXT;
    }

    /**
     * Enter the given content into the WYSIWYG editor
     *
     * @param string $content The content being entered
     */
    public function setContent(string $content)
    {
        if (self::VISUAL === $this->getMode()) {
            $this->getDriver()->executeScript(
                ';document.getElementById(\'' . self::$wysiwyg_iframe_id . '\').contentDocument.body.innerHTML = \'<p>' . addslashes(htmlspecialchars($content)) . '</p>\';'
            );
        } else {
            $this->fillField(self::$textarea_id, $content);
        }
    }
}
