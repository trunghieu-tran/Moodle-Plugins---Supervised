<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
$text = optional_param('text', 'fielddescription', PARAM_TEXT);

echo $text;