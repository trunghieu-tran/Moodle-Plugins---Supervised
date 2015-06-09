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

class qtype_preg_error {

    /** Human-understandable error message. */
    public $errormsg;
    /** An instance of qtype_preg_position. */
    public $position;

    /**
     * Returns a string with first character in upper case and the rest of the string in lower case.
     */
    protected function uppercase_first_letter($str) {
        $head = core_text::strtoupper(core_text::substr($str, 0, 1));
        $tail = core_text::strtolower(core_text::substr($str, 1));
        return $head . $tail;
    }

    protected function highlight_regex($regex, $position) {
        if ($position->indfirst >= 0 && $position->indlast >= 0) {
            return htmlspecialchars(core_text::substr($regex, 0, $position->indfirst)) . '<b>' .
                   htmlspecialchars(core_text::substr($regex, $position->indfirst, $position->indlast - $position->indfirst + 1)) . '</b>' .
                   htmlspecialchars(core_text::substr($regex, $position->indlast + 1));
        } else {
            return htmlspecialchars($regex);
        }
    }

    /**
     * Constructs error with given parameters.
     * @param errormsg string error message to show to the user.
     * @param regex string regular expression, containing error.
     * @param position qtype_preg_position instance.
     * @param preservemsg bool if true, message contains HTML code and should not be treated by htmlspecialchars function. PHP preg matcher use it to show links to PCRE documentation.
     */
    public function __construct($errormsg, $regex, $position, $preservemsg = false) {

        if ($position === null) {
            $position = new qtype_preg_position(0, core_text::strlen($regex) - 1, -1, -1, -1, -1);  // TODO
        }

        $errormsg = $this->uppercase_first_letter($errormsg);
        if (!$preservemsg) {
            $errormsg = htmlspecialchars($errormsg);
        }
        $this->position = $position;
        if ($position->colfirst != -2) {
            $this->errormsg = $this->highlight_regex($regex, $position) . '<br/>' . $errormsg;
        } else {
            $this->errormsg = $errormsg;
        }
    }
}

// A syntax error occured while parsing a regex.
class qtype_preg_parsing_error extends qtype_preg_error {

    public function __construct($regex, $astnode) {
        parent::__construct($astnode->error_string(), $regex, $astnode->position);
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

        parent::__construct($errormsg, $regex, $astnode->position);
    }
}

// There's an unsupported modifier in a regex.
class qtype_preg_modifier_error extends qtype_preg_error {

    public function __construct($matchername, $modifier) {
        $a = new stdClass;
        $a->modifier = $modifier;
        $a->classname = $matchername;

        $errormsg = get_string('unsupportedmodifier', 'qtype_preg', $a);

        parent::__construct($errormsg, '', null);
    }
}

// FA is too large.
class qtype_preg_too_complex_error extends qtype_preg_error {

    public function __construct($regex, $matcher, $position = null) {
        global $CFG;

        $a = new stdClass;
        $a->linefirst = $position->linefirst;
        $a->linelast = $position->linelast;
        $a->colfirst = $position->colfirst;
        $a->collast = $position->collast;
        $a->engine = get_string($matcher->name(), 'qtype_preg');
        $a->link = $CFG->wwwroot . '/' . $CFG->admin . '/settings.php?section=qtypesettingpreg';

        $errormsg = get_string('too_large_fa', 'qtype_preg', $a);

        parent::__construct($errormsg, $regex, $position);
    }
}

class qtype_preg_empty_fa_error extends qtype_preg_error {

    public function __construct($regex, $position = null) {
        $errormsg = get_string('empty_fa', 'qtype_preg');

        parent::__construct($errormsg, $regex, $position);
    }
}

class qtype_preg_backref_intersection_error extends qtype_preg_error {

    public function __construct($regex, $position = null) {
        $errormsg = get_string('backref_intersection', 'qtype_preg');

        parent::__construct($errormsg, $regex, $position);
    }
}


class qtype_preg_mergedassertion_option_error extends qtype_preg_error {

    public function __construct($regex, $position = null) {
        $errormsg = get_string('mergedassertion_option', 'qtype_preg');

        parent::__construct($errormsg, $regex, $position);
    }
}