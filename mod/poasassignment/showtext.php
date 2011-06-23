<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
$text = optional_param('text', 'text', PARAM_TEXT);
echo $text;