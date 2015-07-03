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
		int main(int  argc, char ** argv)
        {
            for(i = 0; i < argc; i++)
                printf(\"%d\", argv[i]);
            return 0;
        }
";