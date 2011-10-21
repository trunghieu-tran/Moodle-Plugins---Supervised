<?php

defined('MOODLE_INTERNAL') || die;

$qtypepregdefultgvpath = get_string('gvpath', 'qtype_preg');
$qtypepreggvdescription = get_string('gvdescription', 'qtype_preg');
$settings->add(new admin_setting_configtext('dotpath', get_string('gvpath', 'qtype_preg'), $qtypepreggvdescription, 'C:\\Program Files\\ GraphViz\\bin\\dot.exe'));
?>