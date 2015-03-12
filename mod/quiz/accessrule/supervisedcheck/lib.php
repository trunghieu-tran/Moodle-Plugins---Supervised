<?php
// This file is part of Student Access Control Kit - https://code.google.com/p/oasychev-moodle-plugins/
//
// Student Access Control Kit is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Student Access Control Kit is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package     quizaccess_supervisedcheck
 * @author      Andrey Ushakov <andrey200964@yandex.ru>
 * @copyright   2014 Oleg Sychev, Volgograd State Technical University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Removes all supervised access rules for nonexistent quizzes
 */
function supervisedcheck_cleanup($course) {
    global $DB;
    $quizzes = $DB->get_records('quiz', array('course' => $course->objectid));
    $DB->delete_records_list('quizaccess_supervisedcheck', 'quizid', array_keys($quizzes));
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function supervisedcheck_course_deleted($course) {
    supervisedcheck_cleanup($course);
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function supervisedcheck_course_content_deleted($course) {
    supervisedcheck_cleanup($course);
}

function supervisedcheck_course_module_deleted($cm) {
    global $DB;
    if ($cm->other['modulename'] == 'quiz') {
        $DB->delete_records('quizaccess_supervisedcheck', array('quizid' => $cm->other['instanceid']));
    }
}