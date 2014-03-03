<?php

/**
 * Removes all supervised access rules for nonexistent quizzes
 */
function supervisedcheck_cleanup($course) {
    global $DB;
    $quizzes = $DB->get_records('quiz', array('course' => $course->id));
    $DB->delete_records_list('quizaccess_supervisedcheck', 'quizid', array_keys($quizzes));
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function event_handler_course_deleted($course) {
    supervisedcheck_cleanup($course);
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function event_handler_course_content_removed($course) {
    supervisedcheck_cleanup($course);
}