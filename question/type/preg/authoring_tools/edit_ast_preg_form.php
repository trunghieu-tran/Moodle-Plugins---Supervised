<?php
/**
 * Defines authoring tools form class.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

global $CFG;
global $PAGE;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_tree_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');
//require_once($CFG->dirroot . '/question/type/preg/renderer.php');

class qtype_preg_authoring_tool_form extends moodleform {

    private $isblok;

    function __constructor($_isblock = false){
        $isblok = $_isblock;
        parent::moodleform();
    }

    //Add elements to form
    function definition() {
        global $CFG;
        global $PAGE;

        // Create the form.
        $mform =& $this->_form;

        // Add header.
        $mform->addElement('html', '<div align="center"><h2>' . get_string('authoring_tool_page_header', 'qtype_preg') . '</h2></div>');

        // Add widget on form.
        $mform->addElement('header', 'regex_input_header', get_string('regex_edit_header_text', 'qtype_preg'));
        $mform->setExpanded('regex_input_header', 1);
        $mform->addHelpButton('regex_input_header','regex_edit_header', 'qtype_preg');

        $mform->addElement('textarea', 'regex_text', get_string('regex_text_text', 'qtype_preg'), array('cols' => 100, 'rows' => 1));
        $mform->setType('regex_text', PARAM_RAW);

        $mform->addElement('submit', 'regex_check', get_string('regex_check_text', 'qtype_preg'));
        $mform->addElement('button', 'regex_show_selection', 'show selection (todo - get_string)');
        if(!$this->isblok){
            $mform->addElement('button', 'regex_back', get_string('regex_back_text', 'qtype_preg'));
        }
        
        $radiocharsetprocessarray=array();
        $radiocharsetprocesarray[] =& $mform->createElement('radio', 'authoring_tools_charset_process', '', get_string('authoring_form_charset_userinscription', 'qtype_preg'), 'userinscription', null);
        $radiocharsetprocesarray[] =& $mform->createElement('radio', 'authoring_tools_charset_process', '', get_string('authoring_form_charset_flags', 'qtype_preg'), 'flags', null);
        $mform->addGroup($radiocharsetprocesarray, 'charset_process_radioset', get_string('authoring_form_charset_mode', 'qtype_preg'), array(' '), false);
        $mform->setDefault('authoring_tools_charset_process', 'userinscription');

        if (stristr($agent, 'MSIE')) {
            $mform->addElement('html', '</div>');
        }
        // Add generated map.
        // Add tree.
        $mform->addElement('header', 'regex_tree_header', get_string('regex_tree_header', 'qtype_preg'));
        $mform->setExpanded('regex_tree_header', 1);
        $mform->addHelpButton('regex_tree_header','regex_tree_header','qtype_preg');
        // Add tree orientation radio buttons.
        $radiotreeorientationsarray=array();
        $radiotreeorientationsarray[] =& $mform->createElement('radio', 'authoring_tools_tree_orientation', '', get_string('authoring_form_tree_vert', 'qtype_preg'), 'vertical', null);
        $radiotreeorientationsarray[] =& $mform->createElement('radio', 'authoring_tools_tree_orientation', '', get_string('authoring_form_tree_horiz', 'qtype_preg'), 'horizontal', null);
        $mform->addGroup($radiotreeorientationsarray, 'tree_orientation_radioset', '', array(' '), false);
        $mform->setDefault('authoring_tools_tree_orientation', 'vertical');

        $mform->addElement('html', '<div id="tree_map" ></div></br>');//Add generated map
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="tree_handler">' .
                                       '<div style="width:10px">' .
                                           '<img src="" id="id_tree" usemap="#' . qtype_preg_explaining_tree_node::get_graph_name() . '" alt="' . get_string('regex_tree_build', 'qtype_preg') . '" />' .
                                    '</div></div></br>');

        // Add graph.
        $mform->addElement('header', 'regex_graph_header', get_string('regex_graph_header', 'qtype_preg'));
        $mform->setExpanded('regex_graph_header', 1);
        $mform->addHelpButton('regex_graph_header','regex_graph_header','qtype_preg');
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="graph_handler"><div style="width:10px"><img src="" id="id_graph" alt="' . get_string('regex_graph_build', 'qtype_preg') . '" /></div></div></br>');

        // Add description.
        $mform->addElement('header', 'regex_description_header', get_string('regex_description_header', 'qtype_preg'));
        $mform->setExpanded('regex_description_header', 1);
        $mform->addHelpButton('regex_description_header','regex_description_header','qtype_preg');
        $mform->addElement('html', '<div id="description_handler"></div>');

        /*$answer = array('answer' => 'Di bats eat cats?');
        $testing_tool = new qtype_preg_regex_testing_tool('33', $answer);
        $mform->addElement('html', $testing_tool->render_hint());*/

        //Add tool for check regexp match
        $mform->addElement('header', 'regex_match_header', 'Input string for check here:');
        $mform->setExpanded('regex_match_header', 1);
        $mform->addHelpButton('regex_match_header','regex_match_header','qtype_preg');

        $mform->addElement('text', 'regex_match_text', 'Input string', array('size' => 100));
        $mform->setType('regex_match_text', PARAM_RAW);
        $mform->addElement('html', '</br><div id="test_regex" ></div></br>');
        $mform->registerNoSubmitButton('regex_check_string');
        $mform->addElement('button', 'regex_check_string', 'Check string');

        //$mform->addElement('text_and_button', 'regex_match_text', 'regex_check_string', 'Input string', array('link_to_button_image' => $CFG->wwwroot . '/question/type/preg/tmp_img/edit.gif'), array('size' => 100));

        /*$mform->registerNoSubmitButton('regex_next_character');
        $mform->addElement('button', 'regex_next_character', 'Get next character');

        $mform->addElement('textarea', 'must_match', 'Must match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regex_check_match', 'Check match');

        $mform->addElement('textarea', 'must_not_match', 'Must not match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regex_check_not_match', 'Check no match');*/

    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
?>
