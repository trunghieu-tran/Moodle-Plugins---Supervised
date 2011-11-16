<?php

defined('MOODLE_INTERNAL') || die;

$qtypepregdefultgvpath = get_string('gvpath', 'qtype_preg');
$qtypepreggvdescription = get_string('gvdescription', 'qtype_preg');
$settings->add(new admin_setting_configtext('dotpath', get_string('gvpath', 'qtype_preg'), $qtypepreggvdescription, 'C:\\Program Files\\GraphViz\\bin\\'));
$qtypepregdefaultnfasizelimit = get_string('nfasizelimit', 'qtype_preg');
$qtypepregnfastatelimitdescription = get_string('nfastatelimitdescription', 'qtype_preg');
$qtypepregnfatransitionlimitdescription = get_string('nfatransitionlimitdescription', 'qtype_preg');
$settings->add(new admin_setting_configtext('nfastatelimit', $qtypepregdefaultnfasizelimit, $qtypepregnfastatelimitdescription, '250'));
$settings->add(new admin_setting_configtext('nfatransitionlimit', $qtypepregdefaultnfasizelimit, $qtypepregnfatransitionlimitdescription, '250'));
?>