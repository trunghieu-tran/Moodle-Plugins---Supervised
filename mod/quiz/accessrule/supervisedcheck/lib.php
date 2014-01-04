<?php

function supervisedcheck_cleanup(){
    global $DB;
    // TODO Remove all supervised access rules for nonexistent quizzes.
    echo('supervisedcheck_cleanup...</br>');
}
function event_handler_course_deleted($course){
    supervisedcheck_cleanup();
}

function event_handler_course_content_removed($course){
    supervisedcheck_cleanup();
}