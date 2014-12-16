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
 * Defines Preg errors displayed to users.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');

class qtype_preg_error {

    // Human-understandable error message.
    public $errormsg;
    //
    public $index_first;
    //
    public $index_last;

    protected function highlight_regex($regex, $indfirst, $indlast) {
        if ($indfirst >= 0 && $indlast >= 0) {
            return htmlspecialchars(qtype_poasquestion_string::substr($regex, 0, $indfirst)) . '<b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $indfirst, $indlast - $indfirst + 1)) . '</b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $indlast + 1));
        } else {
            return htmlspecialchars($regex);
        }
    }

     public function __construct($errormsg, $regex = '', $index_first = -2, $index_last = -2, $preservemsg = false) {
        if (!$preservemsg) {
            $errormsg = htmlspecialchars($errormsg);
        }
        $this->index_first = $index_first;
        $this->index_last = $index_last;
        if ($index_first != -2) {
            $this->errormsg = $this->highlight_regex($regex, $index_first, $index_last). '<br/>' . $errormsg;
        } else {
            $this->errormsg = $errormsg;
        }
     }
}

// A syntax error occured while parsing a regex.
class qtype_preg_parsing_error extends qtype_preg_error {

    public function __construct($regex, $parsernode) {
        $this->index_first = $parsernode->indfirst;
        $this->index_last = $parsernode->indlast;
        $this->errormsg = $this->highlight_regex($regex, $this->index_first, $this->index_last) . '<br/>' . $parsernode->error_string();
    }
}

// There's an unacceptable node in a regex.
class qtype_preg_accepting_error extends qtype_preg_error {

    /**
     * Returns a string with first character converted to upper case.
     */
    public function uppercase_first_letter($str) {
        $firstchar = qtype_poasquestion_string::strtoupper(qtype_poasquestion_string::substr($str, 0, 1));
        $rest = qtype_poasquestion_string::substr($str, 1, qtype_poasquestion_string::strlen($str));
        return $firstchar.$rest;
    }

    public function __construct($regex, $matchername, $nodename, $indexes) {
        $a = new stdClass;
        $a->nodename = $this->uppercase_first_letter($nodename);
        $a->indfirst = $indexes['start'];
        $a->indlast = $indexes['end'];
        $a->engine = get_string($matchername, 'qtype_preg');
        $this->index_first = $a->indfirst;
        $this->index_last = $a->indlast;
        $this->errormsg = $this->highlight_regex($regex, $this->index_first, $this->index_last) . '<br/>' . get_string('unsupported', 'qtype_preg', $a);
    }

}

// There's an unsupported modifier in a regex.
class qtype_preg_modifier_error extends qtype_preg_error {

    public function __construct($matchername, $modifier) {
        $a = new stdClass;
        $a->modifier = $modifier;
        $a->classname = $matchername;
        $this->errormsg = get_string('unsupportedmodifier', 'qtype_preg', $a);
    }
}

// FA is too large.
class qtype_preg_too_complex_error extends qtype_preg_error {

    public function __construct($regex, $matcher, $indexes = array('start' => -1, 'end' => -2)) {
        global $CFG;
        $a = new stdClass;
        if ($indexes['start'] == -1 && $indexes['end'] == -2) {
            $a->indfirst = 0;
            $a->indlast = qtype_poasquestion_string::strlen($regex) - 1;
        } else {
            $a->indfirst = $indexes['start'];
            $a->indlast = $indexes['end'];
        }
        $a->engine = get_string($matcher->name(), 'qtype_preg');
        $this->index_first = $a->indfirst;
        $this->index_last = $a->indlast;
        $a->link = $CFG->wwwroot.'/'.$CFG->admin.'/settings.php?section=qtypesettingpreg';
        $this->errormsg = $this->highlight_regex($regex, $this->index_first, $this->index_last) . '<br/>' . get_string('too_large_fa', 'qtype_preg', $a);
    }
}
