<?php

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/question/type/preg/questiontype.php');

if($ADMIN->fulltree) {
$settings->add(new admin_setting_heading('questioneditingheading', get_string('questioneditingheading', 'qtype_preg'), ''));
$qtypeobj = new qtype_preg;
$engines = $qtypeobj->available_engines();
$settings->add(new admin_setting_configselect('qtype_preg_defaultengine', get_string('defaultenginelabel', 'qtype_preg'),
                                                get_string('defaultenginedescription', 'qtype_preg'), 'nfa_matcher', $engines));
$notations = $qtypeobj->available_notations();
$settings->add(new admin_setting_configselect('qtype_preg_defaultnotation', get_string('defaultnotationlabel', 'qtype_preg'),
                                                get_string('defaultnotationdescription', 'qtype_preg'), 'native', $notations));
$settings->add(new admin_setting_configtext('qtype_preg_maxerrorsshown', get_string('maxerrorsshownlabel', 'qtype_preg'),
                                                get_string('maxerrorsshowndescription', 'qtype_preg'), 5, PARAM_INT));

$settings->add(new admin_setting_heading('dfaheading', get_string('dfaheading', 'qtype_preg'), get_string('engineheadingdescriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_dfastatecount', get_string('maxfasizestates', 'qtype_preg'),
                                                get_string('dfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_dfapasscount', get_string('maxfasizetransitions', 'qtype_preg'),
                                                get_string('dfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));

$settings->add(new admin_setting_heading('nfaheading', get_string('nfaheading', 'qtype_preg'), get_string('engineheadingdescriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_nfastatelimit', get_string('maxfasizestates', 'qtype_preg'),
                                                get_string('nfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_nfatransitionlimit', get_string('maxfasizetransitions', 'qtype_preg'),
                                                get_string('nfalimitsdescription', 'qtype_preg'), 250, PARAM_INT));

$settings->add(new admin_setting_heading('debugheading', get_string('debugheading', 'qtype_preg'), ''));
}
