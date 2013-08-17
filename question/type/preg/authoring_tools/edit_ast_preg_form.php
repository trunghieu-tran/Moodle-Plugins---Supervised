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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_syntax_tree_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_textarea.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');

class qtype_preg_authoring_tool_form extends moodleform {

    private $isblock;

    function __constructor($_isblock = false) {
        $isblock = $_isblock;
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

        // Add the editing widgets.
        $mform->addElement('header', 'regex_input_header', get_string('regex_edit_header_text', 'qtype_preg'));
        $mform->setExpanded('regex_input_header', 1);
        $mform->addHelpButton('regex_input_header', 'regex_edit_header', 'qtype_preg');

        $mform->addElement('textarea', 'regex_text', get_string('regex_text_text', 'qtype_preg'), array('cols' => 100, 'rows' => 1));
        $mform->setType('regex_text', PARAM_RAW);

        $topline = array();
        $topline[] =& $mform->createElement('submit', 'regex_update', get_string('regex_update_text', 'qtype_preg'));
        if (!$this->isblock) {
            $topline[] =& $mform->createElement('button', 'regex_save', get_string('regex_save_text', 'qtype_preg'));
        }
        $topline[] =& $mform->createElement('button', 'regex_cancel', get_string('regex_cancel_text', 'qtype_preg'));
        $topline[] =& $mform->createElement('button', 'regex_show_selection', get_string('regex_show_selection', 'qtype_preg'));
        $mform->addGroup($topline, 'input_regex_line', '', array(' '), false);

        $radiocharsetprocessarray = array();
        $radiocharsetprocesarray[] =& $mform->createElement('radio', 'authoring_tools_charset_process', '', get_string('authoring_form_charset_userinscription', 'qtype_preg'), 'userinscription', null);
        $radiocharsetprocesarray[] =& $mform->createElement('radio', 'authoring_tools_charset_process', '', get_string('authoring_form_charset_flags', 'qtype_preg'), 'flags', null);
        $mform->addGroup($radiocharsetprocesarray, 'charset_process_radioset', get_string('authoring_form_charset_mode', 'qtype_preg'), array(' '), false);
        $mform->setDefault('authoring_tools_charset_process', 'userinscription');

        // Add syntax tree tool.
        $mform->addElement('header', 'regex_tree_header', get_string('syntax_tree_tool', 'qtype_preg'));
        $mform->setExpanded('regex_tree_header', 1);
        $mform->addHelpButton('regex_tree_header', 'syntax_tree_tool', 'qtype_preg');

        // Add tree orientation radio buttons.
        $radiotreeorientationsarray = array();
        $radiotreeorientationsarray[] =& $mform->createElement('radio', 'authoring_tools_tree_orientation', '', get_string('authoring_form_tree_vert', 'qtype_preg'), 'vertical', null);
        $radiotreeorientationsarray[] =& $mform->createElement('radio', 'authoring_tools_tree_orientation', '', get_string('authoring_form_tree_horiz', 'qtype_preg'), 'horizontal', null);
        $mform->addGroup($radiotreeorientationsarray, 'tree_orientation_radioset', '', array(' '), false);
        $mform->setDefault('authoring_tools_tree_orientation', 'vertical');

        // Add generated map.
        $mform->addElement('html', '<div id="tree_map" ></div></br>');
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="tree_hnd">' .
                                        '<div id="tree_err"></div>' .
                                        '<div style="width:10px">' .
                                            '<img src="" id="tree_img" usemap="#' . qtype_preg_syntax_tree_node::get_graph_name() . '" alt="' . get_string('regex_tree_build', 'qtype_preg') . '" />' .
                                        '</div></div></br>');

        // Add explaining graph tool.
        $mform->addElement('header', 'regex_graph_header', get_string('explaining_graph_tool', 'qtype_preg'));
        $mform->setExpanded('regex_graph_header', 1);
        $mform->addHelpButton('regex_graph_header', 'explaining_graph_tool', 'qtype_preg');
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="graph_hnd">' .
                                        '<div id="graph_err"></div>' .
                                        '<div style="width:10px">' .
                                            '<img src="" id="graph_img" alt="' . get_string('regex_graph_build', 'qtype_preg') . '" />' .
                                        '</div></div></br>');

        // Add description tool.
        $mform->addElement('header', 'regex_description_header', get_string('description_tool', 'qtype_preg'));
        $mform->setExpanded('regex_description_header', 1);
        $mform->addHelpButton('regex_description_header', 'description_tool', 'qtype_preg');
        $mform->addElement('html', '<div id="description_handler"></div>');

        // Add testing tool.
        $mform->addElement('header', 'regex_match_header', get_string('regex_match_header', 'qtype_preg'));
        $mform->setExpanded('regex_match_header', 1);
        $mform->addHelpButton('regex_match_header', 'regex_match_header', 'qtype_preg');

        $mform->addElement('preg_textarea', 'regex_match_text', get_string('regex_match_textarea', 'qtype_preg'), array('cols' => 50));

        $mform->registerNoSubmitButton('regex_check_strings');
        $mform->addElement('button', 'regex_check_strings', get_string('regex_check_strings', 'qtype_preg'));
    }

    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
