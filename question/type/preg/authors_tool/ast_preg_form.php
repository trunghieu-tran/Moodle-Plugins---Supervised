<?php
/**
 * Creates authors tool form.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
global $PAGE;

require_once($CFG->dirroot . '/question/type/preg/authors_tool/edit_ast_preg_form.php');
//require_once('preg_authors_tool_load.php');

//Instantiate simplehtml_form 
$mform = new qtype_preg_authors_tool_form();

//TODO: do check user
if ($mform->no_submit_button_pressed()) {
    //$mform->addElement('html', 'Interactive tree</br><img src="" />');
    
} else {
//Form processing and displaying is done here
/*if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
    
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.
  
} else {*/
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
  

    //Set default data (if any)
    //$mform->set_data($toform);

    //displays the form
    $mform->display();

}
?>
