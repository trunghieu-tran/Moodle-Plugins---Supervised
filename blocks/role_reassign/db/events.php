<?php
$handlers = array (
    'quiz_attempt_started' => array (
        'handlerfile'      => '/blocks/role_reassign/lib.php',
        'handlerfunction'  => 'quiz_attempt_started_handler',
        'schedule'         => 'instant',
        'internal'         => 0,
    ),

    'quiz_attempt_processed' => array (
        'handlerfile'      => '/blocks/role_reassign/lib.php',
        'handlerfunction'  => 'quiz_attempt_processed_handler',
        'schedule'         => 'instant',
        'internal'         => 0,
    ),

);