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

$string = "class A { A(); ~A(); }; A::A() { construct(this); } A::~A() { destroy(this); }";