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
    template<class T> class myarray {};
 
    template<class K, class V, template<typename> class C = myarray>
    class Map {
        C<K> key;
        C<V> value;
    };
";