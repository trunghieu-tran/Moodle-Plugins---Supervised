<?php
/**
 * Defines authoring tools form class.
 *
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

//defined('MOODLE_INTERNAL') || die();

global $CFG;
global $PAGE;
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_tree_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');
//require_once($CFG->dirroot . '/question/type/preg/renderer.php');

class qtype_preg_authoring_tool_form extends moodleform {

    /*function __constructor(){
        parent::moodleform();
    }*/

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
        $mform->addHelpButton('regex_input_header','regex_edit_header', 'qtype_preg');

        $mform->addElement('text', 'regex_text', get_string('regex_text_text', 'qtype_preg'), array('size' => 100));

        $agent = getenv('HTTP_USER_AGENT');
        if (stristr($agent, 'MSIE')) {
            $mform->addElement('html', '<div style="margin-left: 79px" >');
            $mform->addElement('submit', 'regex_check', get_string('regex_check_text', 'qtype_preg'));
            $mform->addElement('button', 'regex_back', get_string('regex_back_text', 'qtype_preg'));
            $mform->addElement('html', '</div>');
        } else {
            $mform->addElement('submit', 'regex_check', get_string('regex_check_text', 'qtype_preg'));
            $mform->addElement('button', 'regex_back', get_string('regex_back_text', 'qtype_preg'));
        }

        // Add tree.
        $mform->addElement('header', 'regex_tree_header', get_string('regex_tree_header', 'qtype_preg'));
        $mform->addHelpButton('regex_tree_header','regex_tree_header','qtype_preg');
        $mform->addElement('html', '<div id="tree_map" ></div></br>');//Add generated map
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="tree_handler">' .
                                       '<div style="width:10px">' .
                                           '<img src="" id="id_tree" usemap="#' . qtype_preg_explaining_tree_node::get_graph_name() . '" alt="' . get_string('regex_tree_build', 'qtype_preg') . '" />' .
                                    '</div></div></br>');

        // Add graph.
        $mform->addElement('header', 'regex_graph_header', get_string('regex_graph_header', 'qtype_preg'));
        $mform->addHelpButton('regex_graph_header','regex_graph_header','qtype_preg');
        $mform->addElement('html', '<div style="max-height:400px;position:relative;overflow:auto !important;width:100%;max-width:100%" id="graph_handler"><div style="width:10px"><img src="" id="id_graph" alt="' . get_string('regex_graph_build', 'qtype_preg') . '" /></div></div></br>');

        // Add description.
        $mform->addElement('header', 'regex_description_header', get_string('regex_description_header', 'qtype_preg'));
        $mform->addHelpButton('regex_description_header','regex_description_header','qtype_preg');
        $mform->addElement('html', '<div id="description_handler"></div>');

        //----------------------TEST REGEX--------------------------
        /*$renderer = $PAGE->get_renderer('qtype_preg');

        $regular = new qtype_preg_question;
        $regular->usecase = false;
        $regular->correctanswer = 'Do cats eat bats?';
        $regular->exactmatch = true;
        $regular->usecharhint = true;
        $regular->penalty = 0.1;
        $regular->charhintpenalty = 0.2;
        $regular->hintgradeborder = 0.6;
        $regular->engine = 'nfa_matcher';
        $regular->notation = 'native';

        //correct answer
        $answer100 = new stdClass();
        $answer100->id = 100;
        $answer100->answer = 'Do ([cbr]at(s|)) eat ([cbr]at\2)\?';
        $answer100->fraction = 1;
        $answer100->feedback = 'Predator is {$1}. The prey is {$3}.';

        $regular->answers = array(100=>$answer100);

        $hintmatch = new qtype_preg_hintmatchingpart($regular);

        //$bestfit = $regular->get_best_fit_answer(array('answer' => 'Do bats eat cats?'));
        $answerArr = array('answer' => 'Do bats eat cats?');
        //var_dump($hintmatch->render_stringextension_hint($renderer, $answerArr));*/

        //$mform->addElement('html', $hintmatch->render_stringextension_hint($renderer, array('answer' => 'Do bats eat cats?')));

        //Add tool for check regexp match
        /*$mform->addElement('header', 'regex_match_header', 'Input string for check here:');
        $mform->addHelpButton('regex_match_header','regex_match_header','qtype_preg');*/

        /*$mform->addElement('text', 'regex_match_text', 'Input string', array('size' => 100));
        $mform->registerNoSubmitButton('regex_check_string');
        $mform->addElement('button', 'regex_check_string', 'Check string');*/

        /*$mform->addElement('text_and_button', 'regex_match_text', 'regex_check_string', 'Input string', array('link_to_button_image' => $CFG->wwwroot . '/question/type/preg/tmp_img/edit.gif'), array('size' => 100));

        $mform->registerNoSubmitButton('regex_next_character');
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
