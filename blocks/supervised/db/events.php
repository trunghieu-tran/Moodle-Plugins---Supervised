<?php
$handlers =  array (
                'start_lesson'    =>    array (
                    'handlerfile'         =>     '/blocks/supervised/inputevent.php',
                    'handlerfunction'     =>     'start_lesson_input',
                    'schedule'            =>     'instant',
                    'internal'            =>    0
                    ),

                'end_lesson'    =>    array (
                    'handlerfile'         =>     '/blocks/supervised/inputevent.php',
                    'handlerfunction'     =>     'end_lesson_input',
                    'schedule'            =>     'instant',
                    'internal'            =>    0
                    ),

                    'quiz_attempt_started' => array (
                    'handlerfile'         =>     '/blocks/supervised/inputevent.php',
                    'handlerfunction'     =>     'quiz_start_lesson',
                    'schedule'            =>     'instant',
                    'internal'            =>    0
                    ),

                'quiz_attempt_processed'    => array (
                    'handlerfile'         =>     '/blocks/supervised/inputevent.php',
                    'handlerfunction'     =>     'quiz_processed_lesson',
                    'schedule'            =>     'instant',
                    'internal'            =>    0
                    )
                );