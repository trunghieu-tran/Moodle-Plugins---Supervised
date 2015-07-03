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
 * Defines classes of notations, used to write regexes.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

/**
 * Abstract notation class.
 */
abstract class qtype_preg_notation {

    // Regular expression in this notation.
    public $regex;
    // Regular expression handling options.
    public $options;

    /**
     * Constructs notation object, should suit most notations.
     */
    public function __construct($regex, $options = null) {
        $this->regex = $regex;
        if ($options === null) {
            $options = new qtype_preg_handling_options;
        }
        $this->options = $options;
    }

    /**
     * Return notation name.
     */
    abstract public function name();

    /**
     * Returns regular expression in desired notation, should be overloaded.
     */
    public function convert_regex($targetnotation) {
        throw new qtype_preg_exception('Sorry, no conversion from '.$this->name().' to '.$targetnotation.' implemented yet.');
    }

    /**
     * Returns regular expression options in desired notation, should suit most notations.
     * When overloading this, you probably would want to set some options based on notation.
     */
    public function convert_options($targetnotation) {
        return $this->options;
    }

}

/**
 * Native notation, supported by internal regular expression parser and used by any regular expression handlers that using this parser.
 * You would usually convert other regexes to it.
 */
class qtype_preg_notation_native extends qtype_preg_notation {

    public function name() {
        return 'native';
    }

}

/**
 * Perl-compatible regular expression Extended notation is equivalent to PCRE_EXTENDED modifier.
 * It is used to write complex regular expression with comments
 * Easily converts to native notation by adding relevant modifier.
 */
class qtype_preg_notation_pcreextended extends qtype_preg_notation {

    public function name() {
        return 'pcreextended';
    }

    public function convert_regex($targetnotation) {
        if ($targetnotation == 'native') {
            return $this->regex;
        }
        return parent::convert_regex($targetnotation);
    }

    public function convert_options($targetnotation) {
        if ($targetnotation == 'native') {
            $this->options->modifiers = $this->options->modifiers | qtype_preg_handling_options::MODIFIER_EXTENDED;
        }
        return $this->options;
    }
}

/**
 * Moodle shortanswer notation is basically a string to match with '*' wildcard for any number of any characters.
 * Easily converts to both native and PCRE strict notations.
 */
class qtype_preg_notation_mdlshortanswer extends qtype_preg_notation {

    public function name() {
        return 'mdlshortanswer';
    }

    public function convert_regex($targetnotation) {

        if ($targetnotation == 'native') {
            // Code from qtype_shortanswer_question::compare_string_with_wildcard with proper respect for Tim Hunt.

            // Break the string on non-escaped asterisks.
            $bits = preg_split('/(?<!\\\\)\*/', $this->regex);
            // Escape regexp special characters in the bits.
            $excapedbits = array();
            foreach ($bits as $bit) {
                $excapedbits[] = preg_quote(str_replace('\*', '*', $bit));
            }
            // Put it back together to make the regexp.
            return implode('.*', $excapedbits);
        }
        return parent::convert_regex($targetnotation);
    }
}
