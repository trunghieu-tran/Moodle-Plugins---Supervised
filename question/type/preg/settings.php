<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/question/type/preg/questiontype.php');
require_once($CFG->dirroot.'/blocks/formal_langs/block_formal_langs.php');

if($ADMIN->fulltree) {

$settings->add(new admin_setting_heading('questioneditingheading', get_string('questioneditingheading', 'qtype_preg'), ''));
$qtypeobj = new qtype_preg;
$engines = $qtypeobj->available_engines();
$settings->add(new admin_setting_configselect('qtype_preg_defaultengine', get_string('defaultenginelabel', 'qtype_preg'), get_string('defaultenginedescription', 'qtype_preg'), 'nfa_matcher', $engines));
$notations = $qtypeobj->available_notations();
$settings->add(new admin_setting_configselect('qtype_preg_defaultnotation', get_string('defaultnotationlabel', 'qtype_preg'), get_string('defaultnotationdescription', 'qtype_preg'), 'native', $notations));
$langs = block_formal_langs::available_langs();
$settings->add(new admin_setting_configselect('qtype_preg_defaultlang', get_string('defaultlanglabel', 'qtype_preg'), get_string('defaultlangdescription', 'qtype_preg'), '2', $langs));
$settings->add(new admin_setting_configtext('qtype_preg_maxerrorsshown', get_string('maxerrorsshownlabel', 'qtype_preg'), get_string('maxerrorsshowndescription', 'qtype_preg'), 5, PARAM_INT));
$settings->add(new admin_setting_heading('debugheading', get_string('debugheading', 'qtype_preg'), ''));

/******* DFA and NFA limitations *******/
$settings->add(new admin_setting_heading('dfa_settings_heading', get_string('dfa_settings_heading', 'qtype_preg'), get_string('engine_heading_descriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_dfa_state_limit', get_string('fa_state_limit', 'qtype_preg'), get_string('dfa_state_limit_description', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_dfa_transition_limit', get_string('fa_transition_limit', 'qtype_preg'), get_string('dfa_transition_limit_description', 'qtype_preg'), 250, PARAM_INT));

$settings->add(new admin_setting_heading('nfa_settings_heading', get_string('nfa_settings_heading', 'qtype_preg'), get_string('engine_heading_descriptions', 'qtype_preg')));
$settings->add(new admin_setting_configtext('qtype_preg_nfa_state_limit', get_string('fa_state_limit', 'qtype_preg'), get_string('nfa_state_limit_description', 'qtype_preg'), 250, PARAM_INT));
$settings->add(new admin_setting_configtext('qtype_preg_nfa_transition_limit', get_string('fa_transition_limit', 'qtype_preg'), get_string('nfa_transition_limit_description', 'qtype_preg'), 250, PARAM_INT));

}
