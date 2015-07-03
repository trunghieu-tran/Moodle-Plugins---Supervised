<?php

require_once('Main.php');

$a = new Lemon;
$_SERVER['argv'] = array('lemon', '-s', '../preg_dot_parser.y');
$a->main();
