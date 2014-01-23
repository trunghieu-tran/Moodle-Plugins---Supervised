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
