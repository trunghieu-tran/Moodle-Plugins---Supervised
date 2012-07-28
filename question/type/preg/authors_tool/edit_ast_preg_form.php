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
        
        $regextext = optional_param('regex', '', PARAM_TEXT);
        $id = optional_param('id', '', PARAM_INT);
        $id_line_edit = optional_param('id_line_edit', '', PARAM_TEXT);
        
        $mform->addElement('html', '<imput type="hidden" id="hidden_id" value="' . $id_line_edit . '" />');//Add hidden field to store id line edit with answer
        
        /*var_dump($id);
        var_dump($regextext); 
        var_dump($id_line_edit);*/
        if(!empty($regextext)) {

            $mform->setDefault('regex_text', $regextext);//Add regex in line edit
            
            //-----------------------------------------Add tree-----------------------------------------
            $mform->addElement('header', 'regex_tree_header', 'Interactive tree');
            $mform->addHelpButton('regex_tree_header','regex_tree_header','qtype_preg');
            $regexhandler = new qtype_preg_regex_handler($regextext);
            
            //Checking parser errors
            $pars_error = false;
            foreach($regexhandler->get_errors() as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    $pars_error = true;
                    break;
                }
            }
            //var_dump($pars_error);

            if($pars_error === false && $regexhandler->get_ast_root() !== NULL) {
                //TODO: implement creating and use $dir
                //$dir = $regexhandler->get_temp_dir('tmp_img');

                qtype_preg_regex_handler::execute_dot($regexhandler->get_ast_root()->dot_script(new qtype_preg_dot_style_provider()), $CFG->dirroot . '/question/type/preg/tmp_img/tree.png');//Generate image
                qtype_preg_regex_handler::execute_dot($regexhandler->get_ast_root()->dot_script(new qtype_preg_dot_style_provider()), $CFG->dirroot . '/question/type/preg/tmp_img/tree.cmapx');//Generate map
                //Add generated images
                $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="tree_handler"><img src="' . $CFG->wwwroot  . '/question/type/preg/tmp_img/tree.png" id="id_tree" usemap="_anonymous_0" alt="Build tree..." /></div></br>');
                
                //-----------------------------------------Add maps-----------------------------------------
                $tree_map ='';//tag <map>                 
                $tree_handle = fopen($CFG->dirroot . '/question/type/preg/tmp_img/tree.cmapx', 'r');//Open and read tag <map> from file
                
                if($tree_handle){//If tree.cmapx is open            
                    while (!feof($tree_handle)) {                    
                        $tree_map .= fgets($tree_handle);
                    }
                    fclose($tree_handle);
                } else {
                    $tree_map = 'Error read map file from disk!';
                }
                
                $mform->addElement('html', $tree_map.'</br>');//Add generated map
                
            } else {
                $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="tree_handler"><img src="' . $CFG->wwwroot  . '/question/type/preg/tmp_img/tree_err.png" id="id_tree" usemap="_anonymous_0" alt="Build tree..." /></div></br>');
                $mform->addElement('html', 'Ooops! I can\' build map!</br>');//Add generated map
            }

            //-----------------------------------------Add graph-----------------------------------------
            $mform->addElement('header', 'regex_graph_header', 'Graph');
            $mform->addHelpButton('regex_graph_header','regex_graph_header','qtype_preg');
            
            //Generate graph image
            $tmp_graph = new qtype_preg_author_tool_explain_graph($regextext);
            
            //Checking parser errors
            $pars_error = false;
            foreach($tmp_graph->get_errors() as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    $pars_error = true;
                    break;
                }
            }
            
            if($pars_error === false && $tmp_graph->get_ast_root() !== NULL) {
                
                $graph = $tmp_graph->create_graph($id);
                $dot_instructions_graph = $graph->create_dot();
                
                qtype_preg_regex_handler::execute_dot($dot_instructions_graph, $CFG->dirroot . '/question/type/preg/tmp_img/graph.png');//Generate image      
                 
                $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="graph_handler"><img src="' . $CFG->wwwroot . '/question/type/preg/tmp_img/graph.png" id="id_graph" alt="Build graph..." /></div></br>');
                
            } else {
                $mform->addElement('html', '<div style="width:950px;max-height:350px;overflow:auto;position:relative" id="graph_handler"><img src="' . $CFG->wwwroot . '/question/type/preg/tmp_img/graph_err.png" id="id_graph" alt="Build graph..." /></div></br>');
            }
            //TODO: implement the removal of temporary files
            /*if(!unlink('/var/www/moodle/question/type/preg/tmp_img/tree.cmapx')){
                echo "Can't delete file";
            }*/
            
            //-----------------------------------------Add description-----------------------------------------
            //Add description on form
            $mform->addElement('header', 'regex_description_header', 'Description here:');
            $mform->addHelpButton('regex_description_header','regex_description_header','qtype_preg');
                
            $description = new qtype_preg_author_tool_description($regextext);
            
            //Checking parser errors
            $pars_error = false;
            foreach($tmp_graph->get_errors() as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    $pars_error = true;
                    break;
                }
            }
            
            if($pars_error === false && $description->get_ast_root() !== NULL) {
                $mform->addElement('html', $description->default_description());
            } else {
                $mform->addElement('html', '<div id="description_handler">Ooops! I can\'t build description!</div>');
            }
            
        } else {
            
            //TODO:fix bag and update to new version
            $mform->setDefault('regex_text', 'input regex');//Add regex in line edit
            
            //Add tree
            $mform->addElement('header', 'regex_tree_header', 'Interactive tree');
            $mform->addHelpButton('regex_tree_header','regex_tree_header','qtype_preg');
            $mform->addElement('html', '<div id="tree_handler"><img src="' . $CFG->wwwroot  . '/question/type/preg/tmp_img/tree_def.png" id="id_tree" usemap="_anonymous_0" /></div></br>');
            
            //Add graph
            $mform->addElement('header', 'regex_graph_header', 'Graph');
            $mform->addHelpButton('regex_graph_header','regex_graph_header','qtype_preg');
            $mform->addElement('html', '<div id="graph_handler"><img src="' . $CFG->wwwroot . '/question/type/preg/tmp_img/graph_def.png" id="id_graph" /></div></br>');
            
            //Add description on form
            $mform->addElement('header', 'regex_description_header', 'Description here:');
            $mform->addHelpButton('regex_description_header','regex_description_header','qtype_preg');
            $mform->addElement('html', '<div id="description_handler">This is description</div>');
            
            //$question = qtype_preg_question::question_from_regex('regex', false, true, 'nfa_matcher', 'native');
            //$hint = new qtype_preg_hintnextchar($question);
            //$rend = $PAGE->get_renderer('qtype_preg');
            //$hint->render_hint($rend, array('answer' => 'Do rats eat bat?'));
            
            //$mform->addElement('html',  $preg_hint->render_hint($rend , array('answer' => 'Do rats eat bat?') ) );
        }
        
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
