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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Represents a named template. For example, a template named 'word' can correspond to '\w+'.
 */
class qtype_preg_template {

    /** Name of this template. */
    public $name;

    /** Actual template (regular expression) that can contain placeholders like $$1, $$2, ...*/
    public $regex;

    /** Number of such placeholders in this template. */
    public $placeholderscount;

    public function __construct($name = '', $regex = '', $placeholderscount = 0) {
        $this->name = $name;
        $this->regex = $regex;
        $this->placeholderscount = $placeholderscount;
    }
}
