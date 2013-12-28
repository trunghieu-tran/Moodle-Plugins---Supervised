<?php

function get_session($id){
    global $DB;

    $select = "SELECT
        {block_supervised_session}.id,
        {block_supervised_session}.timestart,
        {block_supervised_session}.duration,
        {block_supervised_session}.timeend,
        {block_supervised_session}.courseid,
        {block_supervised_session}.teacherid,
        {block_supervised_session}.state,
        {block_supervised_session}.sessioncomment,
        {block_supervised_classroom}.name   AS classroomname,
        {block_supervised_lessontype}.name  AS lessontypename,
        {user}.firstname,
        {user}.lastname,
        {groups}.name                       AS groupname,
        {course}.fullname                   AS coursename

        FROM {block_supervised_session}
            JOIN {block_supervised_classroom}
              ON {block_supervised_session}.classroomid       =   {block_supervised_classroom}.id
            LEFT JOIN {block_supervised_lessontype}
              ON {block_supervised_session}.lessontypeid =   {block_supervised_lessontype}.id
            JOIN {user}
              ON {block_supervised_session}.teacherid    =   {user}.id
            LEFT JOIN {groups}
              ON {block_supervised_session}.groupid      =   {groups}.id
            JOIN {course}
              ON {block_supervised_session}.courseid     =   {course}.id

        WHERE {block_supervised_session}.id      = :id
        ";
    $params['id']      = $id;

    return $DB->get_record_sql($select, $params);
}