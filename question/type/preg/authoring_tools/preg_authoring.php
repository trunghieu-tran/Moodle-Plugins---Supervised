<?php
/**
 * Creates authoring tools form.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_form.php');

$PAGE->set_url('/question/type/preg/authoring_tools/preg_authoring.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('popup');

// Enable preferences ajax update.
$prefs = array('regex_input',
               'regex_matching_options',
               'regex_tree',
               'regex_graph',
               'regex_description',
               'regex_testing',
               );
foreach ($prefs as $pref) {
    user_preference_allow_ajax_update('qtype_preg_' . $pref . '_expanded', PARAM_BOOL);
}

// Do output.
echo $OUTPUT->header();

$mform = new qtype_preg_authoring_form();
$mform->display();

/**
 * @badcode We don't want M.core.init_popuphelp() executed once more. TODO: any better way to achieve it?
 */
$footer = preg_replace('/M\.core\.init_popuphelp\(\);/u', '', $OUTPUT->footer());
echo $footer;
