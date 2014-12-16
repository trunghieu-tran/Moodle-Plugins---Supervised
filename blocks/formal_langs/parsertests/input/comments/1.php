<?php
$namespacetree = array(
	'vector' => array(
        
	),
    'std' => array(
        'vector' => array(
            'inner' => array(
            )
        )
    )
);

$donotstripcomments = true;

$string = "
/* 2 + 3 */
int // some random typename
a /* Just simple object name */ /* a is nice name for variable */
; /* Just a comment for semicolon */
";