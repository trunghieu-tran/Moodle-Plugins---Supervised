<?php
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
