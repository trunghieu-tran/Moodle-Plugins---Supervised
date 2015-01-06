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

    public function __construct($name = '', $regex = '', $options = '', $placeholderscount = 0) {
        $this->name = $name;
        $this->regex = $regex;
        $this->options = $options;
        $this->placeholderscount = $placeholderscount;
    }

    /**
     * Returns all templates that should be recognized by parser.
     */
    public static function available_templates() {

        if (defined('qtype_preg_templates_test')) {
            return self::available_testing_templates();
        }

        static $result;
        if ($result === null) {
            $result = array(
                'word' => new template('word', '\w+'),
                'integer' => new template('integer', '[+-]?\d+'),
                'parens_req' => new template('parens_req', '(\((?:$$1|(?-1))\))', '', 1),
            );
        }
        return $result;
    }

    /**
     * You are not supposed to call this one unless you are testing the parser.
     */
    private static function available_testing_templates() {
        static $result;
        if ($result === null) {
            $result = array(
                'word' => new template('word', '\w+'),
                'integer' => new template('integer', '[+-]?\d+'),
                'word_and_integer' => new template('word_and_integer', '(?###word)(?###integer)'),
                'parens_req' => new template('parens_req', '(\((?:$$1|(?-1))\))', '', 1),
                'brackets_req' => new template('brackets_req', '(\[(?:$$1|(?-1))\])', '', 1),
                'word_in_parens' => new template('word_in_parens', '(?###parens_req<)(?###word)(?###>)'),
                'word_in_parens_in_brackets' => new template('word_in_parens_in_brackets', '(?###brackets_req<)(?###parens_req<)(?###word)(?###>)(?###>)'),
            );
        }
        return $result;
    }
}
