<?php

/**
 * Removes all supervised access rules for nonexistent quizzes
 */
function supervisedcheck_cleanup(){
    // TODO Remove all supervised access rules for nonexistent quizzes.
    echo('supervisedcheck_cleanup...</br>');
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function event_handler_course_deleted($course){
    supervisedcheck_cleanup();
}

/**
 * Course deleted (event). Remove all out-of-date quiz access rules.
 *
 * @param $course int course id
 */
function event_handler_course_content_removed($course){
    supervisedcheck_cleanup();
}