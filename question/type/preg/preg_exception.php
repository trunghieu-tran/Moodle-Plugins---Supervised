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
 * Defines Preg exception class
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Preg questiontype exception class.
 */
class qtype_preg_exception extends moodle_exception {
    /**
     * @param string $errorcode
     * @param object $a
     * @param string $debuginfo
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, '', '', $a, $debuginfo);
    }
}

/**
 * A special class, throwed by finite automata, should be catched to
 * generate qtype_preg_too_complex_error object by the code building automata.
 *
 * No actual info needed since it would be filled by catching code.
 */
class qtype_preg_toolargefa_exception extends qtype_preg_exception {
    /**
     * @param string $errorcode
     * @param object $a
     * @param string $debuginfo
     */
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * Class for exceptions caused by empty automaton which can be after merging.
 */
class qtype_preg_empty_fa_exception extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

class qtype_preg_backref_intersection_exception extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * Class for exceptions caused by using complex asserions when merged option is unset.
 */
class qtype_preg_mergedassertion_option_exception extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * Class for exceptions caused by empty pathtodot option.
 */
class qtype_preg_pathtodot_empty extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * Class for exceptions caused by incorrect pathtodot option.
 */
class qtype_preg_pathtodot_incorrect extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}

/**
 * Class for exceptions caused by dot (incorrect dot code or even a bug in dot itself).
 */
class qtype_preg_dot_error extends qtype_preg_exception {
    public function __construct($errorcode, $a = null, $debuginfo = null) {
        parent::__construct($errorcode, $a, $debuginfo);
    }
}
