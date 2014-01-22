<?php

$handlers = array (
    'course_deleted' => array (
        'handlerfile'      => '/blocks/supervised/lib.php',
        'handlerfunction'  => 'event_handler_course_deleted',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'course_content_removed' => array (
        'handlerfile'      => '/blocks/supervised/lib.php',
        'handlerfunction'  => 'event_handler_course_content_removed',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'groups_group_deleted' => array (
        'handlerfile'      => '/blocks/supervised/lib.php',
        'handlerfunction'  => 'event_handler_groups_group_deleted',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'course_groups_groups_deleted' => array (
        'handlerfile'      => '/blocks/supervised/lib.php',
        'handlerfunction'  => 'event_handler_groups_groups_deleted',
        'schedule'         => 'instant',
        'internal'         => 1,
    )
);
