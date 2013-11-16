<?php
function can_delete_lessontype($lessontypeid) {
    //global $USER, $DB;

    // TODO check: user permissions; is in session(s); is in quiz access rules table
    
    //$params = array('userid'=>$USER->id, 'url'=>"view.php?id=$courseid", 'since'=>$since);
    //$select = "module = 'course' AND action = 'new' AND userid = :userid AND url = :url AND time > :since";
    //$DB->record_exists_select('log', $select, $params);
    return true;
}

function can_delete_classroom($classroomid) {
    // TODO check: user permissions; is in session(s);
    return true;
}