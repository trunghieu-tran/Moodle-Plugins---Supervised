<?php
/**
 * Create interactive tree, explain graph and description.
 * 
 * @copyright &copy; 2012  Terechov Grigory
 * @author Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
//defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../../../../config.php');

global $CFG;
//require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/question/type/preg/authors_tool/explain_graph_tool.php');
//require_once($CFG->dirroot.'/question/type/preg/question.php');
//require_once($CFG->dirroot.'/question/type/preg/preg_hints.php');
require_once($CFG->dirroot.'/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot.'/question/type/preg/preg_dotstyleprovider.php');
require_once($CFG->dirroot.'/question/type/preg/authors_tool/preg_description.php');

class preg_authors_tool_load {
    
    /**
     * Generate image and map for interative tree
     * 
     * @param array $json_array contains link on image and text map of interactive tree
     */
    public static function load_tree(&$json_array){
        
        global $CFG;
        $regextext = optional_param('regex', '', PARAM_TEXT);
        $id = optional_param('id', '', PARAM_INT);

        if(!empty($regextext)) {
            
            $regexhandler = new qtype_preg_regex_handler($regextext);
            
            //Checking parser errors
            $pars_error = false;
            foreach($regexhandler->get_errors() as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    $pars_error = true;
                    break;
                }
            }

            if($pars_error === false && $regexhandler->get_ast_root() !== NULL) {
                
                $styleprovider = new qtype_preg_dot_style_provider();
                $dotscript = $regexhandler->get_ast_root()->dot_script($styleprovider);
                if($id!=-1){
                    $dotscript = $styleprovider->select_subtree($dotscript, $id);
                }
                
                qtype_preg_regex_handler::execute_dot($dotscript, $CFG->dirroot . '/question/type/preg/tmp_img/tree.png');//Generate image
                qtype_preg_regex_handler::execute_dot($dotscript, $CFG->dirroot . '/question/type/preg/tmp_img/tree.cmapx');//Generate map
                
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

                $json_array['tree_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/tree.png';//Add tree
                $json_array['map'] = $tree_map;//Add map
                
            } else {                
                $json_array['tree_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/tree_err.png';
            }
        } else {
            $json_array['tree_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/tree_dif.png';//Add tree
        }
    }
    
    /**
     * Generate image for explain graph
     * 
     * @param array $json_array contains link on image of explain graph
     */
    public static function load_graph(&$json_array){
        
        global $CFG;
        $regextext = optional_param('regex', '', PARAM_TEXT);
        $id = optional_param('id', '', PARAM_INT);

        if(!empty($regextext)) {            
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

                $json_array['graph_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/graph.png';//Add graph
                
            } else {                
                $json_array['graph_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/graph_err.png';
            }
        } else {
            $json_array['graph_src'] = $CFG->wwwroot  . '/question/type/preg/tmp_img/graph_dif.png';//Add graph
        }
    }
    
    /**
     * Generate description
     * 
     * @param array $json_array contains text of description
     */
    public static function load_description(&$json_array){
        
        global $CFG;
        $regextext = optional_param('regex', '', PARAM_TEXT);
        $id = optional_param('id', '', PARAM_INT);

        if(!empty($regextext)) {
            if($id == -1){
                $description = new qtype_preg_author_tool_description($regextext);
                
                //Checking parser errors
                $pars_error = false;
                foreach($description->get_errors() as $error) {
                    if (is_a($error, 'qtype_preg_parsing_error')) {
                        $pars_error = true;
                        break;
                    }
                }
                
                if($pars_error === false && $description->get_ast_root() !== NULL) {
                    $json_array['description'] = $description->default_description();//Add tree                
                } else {                
                    $json_array['description'] = 'Ooops! I can\'t build description!';
                }
            }
        } else {
            $json_array['description'] = 'Ooops! I can\'t build description!';
        }
    }
    
    /**
     * Generate array of links on image of interactive tree, explain graph, text map for interactive tree and text of description
     * 
     * @param array $json_array contains author tool content
     */
    public static function get_json_array(){
        
        $json_array = array();
        
        /*$regextext = optional_param('regex', '', PARAM_TEXT);
        $json_array['regex'] = $regextext;*/
        
        self::load_tree($json_array);
        self::load_graph($json_array);
        self::load_description($json_array);
        
        return $json_array;
    
    }
}

$json_array = preg_authors_tool_load::get_json_array();
echo json_encode($json_array);
