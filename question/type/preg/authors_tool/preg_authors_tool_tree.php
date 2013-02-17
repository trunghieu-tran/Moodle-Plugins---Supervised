<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_authors_tool.php');
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

class qtype_preg_author_tool_tree extends qtype_preg_author_tool {

    /**
     * Generate image and map for interative tree
     * 
     * @param array $json_array contains link on image and text map of interactive tree
     */
    public function generate_json(&$json_array, $regextext, $id) {

        global $CFG;

        if(!empty($regextext)) {
            
            //Checking parser errors
            $pars_error = false;
            foreach($this->get_errors() as $error) {
                if (is_a($error, 'qtype_preg_parsing_error')) {
                    $pars_error = true;
                    break;
                }
            }

            if($pars_error === false && $this->get_ast_root() !== NULL) {

                $styleprovider = new qtype_preg_dot_style_provider();
                $dotscript = $this->get_ast_root()->dot_script($styleprovider);
                if($id!=-1){
                    $dotscript = $styleprovider->select_subtree($dotscript, $id);
                }

                $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
                $json_array['map'] = qtype_preg_regex_handler::execute_dot($dotscript, 'cmapx');
                
            } else {
                $dotscript = 'digraph {
                            "Ooops! I can\'t build interactive tree!" [color=white];
                        }';
                $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
            }
        } else {
            $dotscript = 'digraph {
                        "This place is for interactive tree" [color=white];
                    }';
            $json_array['tree_src'] = 'data:image/png;base64,' . base64_encode(qtype_preg_regex_handler::execute_dot($dotscript, 'png'));
        }
    }
}
