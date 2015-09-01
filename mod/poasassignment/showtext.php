<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
$text = optional_param('text', 'text', PARAM_TEXT);
header("Content-type: text/html; charset=utf-8");
echo $text;