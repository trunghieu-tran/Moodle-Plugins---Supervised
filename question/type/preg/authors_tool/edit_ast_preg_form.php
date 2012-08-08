<?php
/**
 * Defines authors tool form class.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
//defined('MOODLE_INTERNAL') || die();

global $CFG;
//global $PAGE;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/question/type/preg/authors_tool/explain_graph_tool.php');
require_once($CFG->dirroot.'/question/type/preg/question.php');
require_once($CFG->dirroot.'/question/type/preg/preg_hints.php');
//require_once($CFG->dirroot.'/question/type/preg/renderer.php');
require_once($CFG->dirroot.'/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot.'/question/type/preg/preg_dotstyleprovider.php');
require_once($CFG->dirroot.'/question/type/preg/authors_tool/preg_description.php');

class qtype_preg_authors_tool_form extends moodleform {

    /*function __constructor(){
        parent::moodleform();
    }*/
    
    //Add elements to form
    function definition() {
        global $CFG;
        global $PAGE;
 
        $mform =& $this->_form;//Create form 
        
        //$PAGE->requires->js('/question/type/preg/authors_tool/author_tool.js');
        $mform->addElement('html', '<div id="script_test"><script src="http://yui.yahooapis.com/3.5.1/build/yui/yui-min.js"></script></div>');
        //$mform->addElement('html', '<div id="script_test"><script type="text/javascript" src="'.$CFG->wwwroot.'/question/type/preg/authors_tool/preg_authors_tool_script.js" ></script></div>');
        
        //Add header
        $mform->addElement('html', '<div align="center"><h2>Test regex</h2></div>');
        
        //Add widget on form
        $mform->addElement('header', 'regex_edit_header', 'Input regex here:');
        $mform->addHelpButton('regex_edit_header','regex_edit_header', 'qtype_preg');
        
        $mform->addElement('text', 'regex_text', 'Input regex', array('size' => 100));        
        $mform->addElement('submit', 'regex_check', 'Check');
        $mform->addElement('button', 'regex_back', 'Back (and save regex in this field)'); 
        
        //Add tree
        $mform->addElement('header', 'regex_tree_header', 'Interactive tree');
        $mform->addHelpButton('regex_tree_header','regex_tree_header','qtype_preg');
        $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="tree_handler"><img src="" id="id_tree" usemap="_anonymous_0" alt="Build tree..." /></div></br>');
        $mform->addElement('html', '<div id="tree_map" ></div></br>');//Add generated map
        
        //Add graph
        $mform->addElement('header', 'regex_graph_header', 'Graph');
        $mform->addHelpButton('regex_graph_header','regex_graph_header','qtype_preg');
        $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="graph_handler"><img src="" id="id_graph" alt="Build graph..." /></div></br>');
        
        //Add description
        $mform->addElement('header', 'regex_description_header', 'Description here:');
        $mform->addHelpButton('regex_description_header','regex_description_header','qtype_preg');
        $mform->addElement('html', '<div id="description_handler"></div>');
        
        //Add tool for check regexp match        
        $mform->addElement('header', 'regex_match_header', 'Input string for check here:');
        $mform->addHelpButton('regex_match_header','regex_match_header','qtype_preg');
        
        $mform->addElement('text', 'regex_match_text', 'Input string', array('size' => 100));
        $mform->registerNoSubmitButton('regex_check_string');
        $mform->addElement('button', 'regex_check_string', 'Check string');

        $mform->registerNoSubmitButton('regex_next_character');
        $mform->addElement('button', 'regex_next_character', 'Get next character');
        
        $mform->addElement('textarea', 'must_match', 'Must match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regex_check_match', 'Check match');
        
        $mform->addElement('textarea', 'must_not_match', 'Must not match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regex_check_not_match', 'Check no match');
        
    }
    
    /*function definition_inner($mform){
        //$mform->addElement('button', 'testbuton1', 'PRESS ME!!!');
        return true;
    }*/

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
?>
