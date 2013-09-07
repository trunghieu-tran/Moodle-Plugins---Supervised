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

    /** Human-understandable error message. */
    public $errormsg;
    /** Index of the line where the erroneous sequence begins. */
    public $linefirst = -1;
    /** Index of the line where the erroneous sequence ends. */
    public $linelast = -1;
    /** Index of the first character in erroneous sequence. */
    public $colfirst;
    /** Index of the last character in erroneous sequence. */
    public $collast;

    /**
     * Returns a string with first character in upper case and the rest of the string in lower case.
     */
    protected function uppercase_first_letter($str) {
        $head = qtype_poasquestion_string::strtoupper(qtype_poasquestion_string::substr($str, 0, 1));
        $tail = qtype_poasquestion_string::strtolower(qtype_poasquestion_string::substr($str, 1));
        return $head . $tail;
    }

    protected function highlight_regex($regex, $colfirst, $collast, $linefirst, $linelast) {
        $stringcolfirst = -1;// Initialise to -1 since when looking for next line break we must start from previous line break + 1, but first start is 0.
        $stringcollast = -1;
        if ($linefirst == 0) {// No string breaks loop, so initialise to 0.
            $stringcolfirst = 0;
        }
        // Look for the line.
        for($i = 0; $i < $linefirst; $i++) {
            $stringcolfirst = qtype_poasquestion_string::strpos($regex, "\n", $stringcolfirst + 1);
        }
        $stringcolfirst += $colfirst; // And add column.
        if ($linelast == 0) {// No string breaks loop, so initialise to 0.
            $stringcollast = 0;
        }
        // Look for the line.
        for($i = 0; $i < $linelast; $i++) {
            $stringcollast = qtype_poasquestion_string::strpos($regex, "\n", $stringcollast + 1);
        }
        $stringcollast += $collast; // And add column.
        if ($colfirst >= 0 && $collast >= 0) {
            return htmlspecialchars(qtype_poasquestion_string::substr($regex, 0, $stringcolfirst)) . '<b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $stringcolfirst, $stringcollast - $stringcolfirst + 1)) . '</b>' .
                   htmlspecialchars(qtype_poasquestion_string::substr($regex, $stringcollast + 1));
        } else {
            return htmlspecialchars($regex);
        }
    }

    /**
     * Constructs error with given parameters.
     * @param errormsg string error message to show to the user.
     * @param regex string regular expression, containing error.
     * @param colfirst int column, where error started.
     * @param collast int column, where error ended.
     * @param linefirst int line, where error started.
     * @param linelast int line, where error ended.
     * @param preservemsg bool if true, message contains HTML code and should not be treated by htmlspecialchars function. PHP preg matcher use it to show links to PCRE documentation.
     */
    public function __construct($errormsg, $regex = '', $colfirst = -1, $collast = -1, $linefirst = -1, $linelast = -1, $preservemsg = false) {
        $errormsg = $this->uppercase_first_letter($errormsg);
        if (!$preservemsg) {
            $errormsg = htmlspecialchars($errormsg);
        }
        $this->linefirst = $linefirst;
        $this->linelast = $linelast;
        $this->colfirst = $colfirst;
        $this->collast = $collast;
        if ($colfirst != -2) {
            $this->errormsg = $this->highlight_regex($regex, $colfirst, $collast, $linefirst, $linelast) . '<br/>' . $errormsg;
        } else {
            $this->errormsg = $errormsg;
        }
    }
}

// A syntax error occured while parsing a regex.
class qtype_preg_parsing_error extends qtype_preg_error {

    public function __construct($regex, $astnode) {
        parent::__construct($astnode->error_string(), $regex, $astnode->position->colfirst, $astnode->position->collast, $astnode->position->linefirst, $astnode->position->linelast);
    }
}

// There's an unacceptable node in a regex.
class qtype_preg_accepting_error extends qtype_preg_error {

    public function __construct($regex, $matchername, $nodename, $astnode) {
        $a = new stdClass;
        $a->nodename = $nodename;
        $a->linefirst = $astnode->position->linefirst;
        $a->linelast = $astnode->position->linelast;
        $a->colfirst = $astnode->position->colfirst;
        $a->collast = $astnode->position->collast;
        $a->engine = get_string($matchername, 'qtype_preg');

        $errormsg = get_string('unsupported', 'qtype_preg', $a);

        parent::__construct($errormsg, $regex, $astnode->position->colfirst, $astnode->position->collast, $astnode->position->linefirst, $astnode->position->linelast);
    }
}

// There's an unsupported modifier in a regex.
class qtype_preg_modifier_error extends qtype_preg_error {

    public function __construct($matchername, $modifier) {
        $a = new stdClass;
        $a->modifier = $modifier;
        $a->classname = $matchername;

        $errormsg = get_string('unsupportedmodifier', 'qtype_preg', $a);

        parent::__construct($errormsg);
    }
}

// FA is too large.
class qtype_preg_too_complex_error extends qtype_preg_error {

    public function __construct($regex, $matcher, $colfirst = -1, $collast = -1, $linefirst = -1, $linelast = -1) {
        global $CFG;

        if ($colfirst == -1 || $collast == -1) {
            $colfirst = 0;
            $collast = qtype_poasquestion_string::strlen($regex) - 1;
        }

        $a = new stdClass;
        $a->linefirst = $linefirst;
        $a->linelast = $linelast;
        $a->colfirst = $colfirst;
        $a->collast = $collast;
        $a->engine = get_string($matcher->name(), 'qtype_preg');
        $a->link = $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=qtypesettingpreg';

        $errormsg = get_string('too_large_fa', 'qtype_preg', $a);

        parent::__construct($errormsg, $regex, $colfirst, $collast, $linefirst, $linelast);
    }
}
