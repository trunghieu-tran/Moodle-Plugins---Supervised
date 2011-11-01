<?php

defined('MOODLE_INTERNAL') || die;

$qtypepregdefultgvpath = get_string('gvpath', 'qtype_preg');
$qtypepreggvdescription = get_string('gvdescription', 'qtype_preg');
$settings->add(new admin_setting_configtext('dotpath', get_string('gvpath', 'qtype_preg'), $qtypepreggvdescription, 'C:\\Program Files\\ GraphViz\\bin\\dot.exe'));
$qtypepregdefaultmaxdfasize = get_string('maxdfasize', 'qtype_preg');
$qtypepregmaxdfastatecountdesription = get_string('dfastatecountdescription', 'qtype_preg');
$qtypepregmaxdfapassagecountdesription = get_string('dfapassagecountdescription', 'qtype_preg');
$settings->add(new admin_setting_configtext('statecount', $qtypepregdefaultmaxdfasize, $qtypepregmaxdfastatecountdesription, '250'));
$settings->add(new admin_setting_configtext('passcount', $qtypepregdefaultmaxdfasize, $qtypepregmaxdfapassagecountdesription, '250'));
?>