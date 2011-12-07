<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
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
 * Correct writing question definition class.
 *
 * @package    qtype
 * @subpackage correctwriting
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Represents a preg question.
 *
 * @copyright  2011 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_preg_question /*extends question_graded_automatically
        implements question_automatically_gradable*/ {
        //TODO - commented out temporarily to use class as passive container for unit-testing - uncomment when real question class would be implemented

    //Fields defining a question
    /** @var array of question_answer objects. */
    //Typical answer objects usually contains answer (string), fraction and feedback fields
    //Our answer object should also contain elementnames array, with teacher-given sematic names 
    //for either important nodes (when syntax analysis is posssible) or all tokens (otherwise).
    public $answers = array();
    //Threshold, defining maximum percent of token length mistake weight could be to provide a valid matched pair
    public $threshold = 0;
    //Language id in the languages table
    public $langid = 0;
    //Other necessary question data like penalty for each type of mistakes etc
}
 ?>