<?php
/**
 * Defines authors tool form class.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
global $CFG;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/question/type/preg/ast_tree_nodes.php');
require_once($CFG->dirroot.'/question/type/preg/explain_graph/explain_graph_tool.php');

class qtype_preg_authors_tool_form extends moodleform {

    /*function __constructor(){
        parent::moodleform();
    }*/
    
    //Add elements to form
    function definition() {
        global $CFG;
 
        $mform =& $this->_form;//Create form 
        
        //Add header
        $mform->addElement('html', '<div align="center"><h2>Test regex</h2></div>');
        
        //Add widget on form
        $mform->addElement('header', 'regexeditheader', 'Input regex here:');
        //$mform->addHelpButton('regexeditheader','regexeditheader',get_string('input_regex'));
        
        $mform->addElement('text', 'regextext', 'Input regex', array('size' => 100));        
        $mform->addElement('button', 'regexcheck', 'Check');
        //$mform->addElement('button', 'regexback', 'Back (and save regex in this field)');
        
        //Add images with graph and tree on form
        $mform->addElement('header', 'regeximgheader', 'Image here:');
        //$mform->addHelpButton('regeximgheader','regeximgheader','This is help for users.');
        
        $regextext = optional_param('regex', '', PARAM_TEXT);
        if(!empty($regextext)) {
            $mform->setDefault('regextext', $regextext);//Add regex in line edit
            
            //Generate tree image
            $tree = new qtype_preg_author_tool_explain_tree($regextext);            
            qtype_preg_regex_handler::execute_dot($tree->create_dot(),'/var/www/moodle/question/type/preg/tmp_img/tree.png');
            
            //Generate graph image
            $tmp_graph = new qtype_preg_author_tool_explain_graph($regextext);
            $graph = $tmp_graph->create_graph();
            //var_dump($graph);       
            qtype_preg_regex_handler::execute_dot($graph->create_dot(),'/var/www/moodle/question/type/preg/tmp_img/graph.png');
            
            //Add generated images
            $mform->addElement('html', 'Interactive tree</br><img src="http://localhost/moodle/question/type/preg/tmp_img/tree.png" /></br>');        
            $mform->addElement('html', 'Graph</br><img src="http://localhost/moodle/question/type/preg/tmp_img/graph.png" />');
        } else {
            $mform->setDefault('regextext', 'input regex');
            $mform->addElement('html', 'Interactive tree</br><img src="http://localhost/moodle/question/type/preg/tmp_img/tree_def.png" /></br>');        
            $mform->addElement('html', 'Graph</br><img src="http://localhost/moodle/question/type/preg/tmp_img/graph_def.png" />');
        }
        
        //Add description on form
        $mform->addElement('header', 'regexdescriptionheader', 'Description here:');
        //$mform->addHelpButton('regexdescriptionheader','regexmatchheader','This is help for users.');
        $mform->addElement('html', '<div id="description_handler">This is description</div>');
        
        //Add tool for check regexp match        
        $mform->addElement('header', 'regexmatchheader', 'Input string for check here:');
        //$mform->addHelpButton('regexmatchheader','regexmatchheader','This is help for users.');
        
        $mform->addElement('text', 'regexmatchtext', 'Input regex', array('size' => 100));
        $mform->registerNoSubmitButton('regexcheckstring');
        $mform->addElement('button', 'regexcheckstring', 'Check regex');
        
        $mform->addElement('textarea', 'mustmatch', 'Must match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regexcheckmatch', 'Check match');
        
        $mform->addElement('textarea', 'mustnotmatch', 'Must not match', 'wrap="virtual" rows="10" cols="100"');
        $mform->addElement('button', 'regexchecknotmatch', 'Check no match');
        
        //$mform->addElement('html', '<div id="script_test"><script type="text/javascript" src="http://localhost/moodle/question/type/preg/preg_authors_tool_script.js"></script></div>');
        
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
