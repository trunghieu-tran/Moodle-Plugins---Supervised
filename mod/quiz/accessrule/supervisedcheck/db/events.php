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

$handlers = array (
    'course_deleted' => array (
        'handlerfile'      => '/mod/quiz/accessrule/supervisedcheck/lib.php',
        'handlerfunction'  => 'event_handler_course_deleted',
        'schedule'         => 'instant',
        'internal'         => 1,
    ),

    'course_content_removed' => array (
        'handlerfile'      => '/mod/quiz/accessrule/supervisedcheck/lib.php',
        'handlerfunction'  => 'event_handler_course_content_removed',
        'schedule'         => 'instant',
        'internal'         => 1,
    )
);

$observers = array(
    array(
        'eventname'   => '\core\event\course_module_deleted',
        'callback'    => 'event_handler_course_module_deleted',
        'includefile' => '/mod/quiz/accessrule/supervisedcheck/lib.php',
    ),
);