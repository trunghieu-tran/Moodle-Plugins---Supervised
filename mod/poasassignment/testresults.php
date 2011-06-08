<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
$attemptid = 6;
global $DB;
require_once('grader/autotester/autotester.php');
$grader = new autotester();
echo $grader->test_attempt($attemptid);
exec('Call grader/autotester/runattempt' . $attemptid . '.bat');
$grader->clean_files($attemptid);