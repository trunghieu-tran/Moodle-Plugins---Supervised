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

$string = "
    template<typename T>
    class List
    {
        T * data;
        List<T> * next;
    };

    List<int> a;
";