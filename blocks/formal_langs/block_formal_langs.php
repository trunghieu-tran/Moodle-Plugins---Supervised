<?php
/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');

class block_formal_langs extends block_base {
    //TODO: Implement this
    public function init() {
        $this->title = get_string('pluginname', 'block_formal_langs');
    }

    /**
     * Returns an array of languages for given context
     *
     * @param contextid id of context, null means whole site
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function available_langs($contextid = null) {
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param langid id of the language
     * @return an intialised object of the child of the block_formal_langs_abstract_language class
     */
    public static function lang_object($langid) {
    }
}