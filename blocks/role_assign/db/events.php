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
$handlers = array (
    'quiz_attempt_started' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_attempt_start',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'quiz_attempt_submitted' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_attempt_submit',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'poasassignment_task_recieved' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_poasassignment_task_recieved',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'session_started' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_session_started',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'session_finished' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_session_finished',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),
    'session_updated' => array (
        'handlerfile'      => '/blocks/role_assign/lib.php',
        'handlerfunction'  => 'event_handler_session_updated',
        'schedule'         => 'instant',
        'internal'         => 1,
    )
);
