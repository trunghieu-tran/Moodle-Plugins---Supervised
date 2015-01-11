<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines templates, an abstraction over regular expressions.
 *
 * @package    qtype_preg
 * @copyright  2015 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qtype_preg;

defined('MOODLE_INTERNAL') || die();

/**
 * Represents a named template. For example, a template named 'word' can correspond to '\w+'.
 * Don't bother yourself wrapping regex in (?:grouping), it will be done automatically during parsing.
 */
class template {

    /** Name of this template. */
    public $name;

    /** Actual template (regular expression) that can contain placeholders like $$1, $$2, ...*/
    public $regex;

    /** The regular expression above may need its own options. This is a string like 'imx'. */
    public $options;

    /** Number of such placeholders in this template. */
    public $placeholderscount;

    /**
     * All available templates. Can be changed from outside for testing purposes.
     * placeholders syntax:
     * same as in regex, but can contain required form definition for description generation
     * like $$g1 (g - is required form for description generation, and will be ignored in other functions)
     */
    private static $templates;

    /** Descriptions */
    private $descriptions;

    public function __construct($name = '', $regex = '', $options = '', $descriptions, $placeholderscount = 0) {
        $this->name = $name;
        $this->regex = $regex;
        $this->options = $options;
        $this->descriptions = $descriptions;
        $this->placeholderscount = $placeholderscount;
    }

    /**
     * Returns all templates that should be recognized by parser.
     */
    public static function available_templates() {
        if (template::$templates === null) {
            template::$templates = array(
                'word' => new template('word', '\w+', '', array('en' => 'word', 'ru' => 'слово')),
                'integer' => new template('integer', '[+-]?\d+', '', array('en' => 'integer', 'ru' => 'целое число')),
                'parens_req' => new template('parens_req', '(   \(    (?:$$1|(?-1))   \)  )', 'x', array('en' => '$$1 in parens', 'ru' => '$$1 в скобках'), 1),
                'parens_opt' => new template('parens_opt', '$$1|(?###parens_req<)$$1(?###>)', '', array('en' => '$$1 in parens or not', 'ru' => '$$1 в скобках или без'), 1),
            );
        }
        return template::$templates;
    }

    public function get_description() {
        $description = false;
        $mylang = current_language();
        $parentlang = get_parent_language($mylang);
        if (array_key_exists($mylang, $this->descriptions)) {
            $description = $this->descriptions[$mylang];
        } else if (array_key_exists($parentlang, $this->descriptions)) {
            $description = $this->descriptions[$parentlang];
        } else if (array_key_exists('en', $this->descriptions)) {
            $description = $this->descriptions['en'];
        } else {
            $description = reset($this->descriptions); // dont use in foreach
        }
        return $description;
    }

    /**
     * You are not supposed to call this one unless you are testing the parser.
     */
    public static function set_available_templates($value) {
        template::$templates = $value;
    }
}
