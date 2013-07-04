<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */
require_once(dirname(__FILE__) . '/../../../../config.php');
global $CFG;
global $PAGE;
require_once($CFG->dirroot . '/question/engine/states.php');
require_once($CFG->dirroot . '/question/type/rendererbase.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

class qtype_preg_regex_testing_tool {

    private $renderer;
    private $answer;
    private $hintmatch;
    
    public function __construct($regex, $answer) {
        global $PAGE;
        $this->renderer = $PAGE->get_renderer('qtype_preg');
        $regular = qtype_preg_question::question_from_regex($regex, false, true, 'nfa_matcher', 'native');
        $this->hintmatch = $regular->hint_object('hintmatchingpart');
        $this->answer = $answer;
    }

    /**
     * Generate image and map for ...
     *
     * @param array $json_array contains link on image and text map of interactive tree
     */
    public function generate_json(&$json_array, $regex  = null, $id = null) {
		$json_array['regex'] = $regex;
        $json_array['id'] = $id;
        $this->generate_json_for_accepted_regex($json_array, $id);
    }
	
    protected function json_key(){
		return 'regex_test';
	}

    protected function generate_json_for_empty_regex(&$json_array, $id){
		$json_array[$this->json_key()] = '';
	}

    protected function generate_json_for_unaccepted_regex(&$json_array, $id){
		$json_array[$this->json_key()] = 'Ooops, i can\'t build text';
	}

    protected function generate_json_for_accepted_regex(&$json_array, $id){
		$json_array[$this->json_key()] = $this->hintmatch->render_hint($this->renderer, null, null, $this->answer);
	}
	
	/*public function render_hint(){
		return $this->hintmatch->render_hint($this->renderer, null, null, $this->answer);
	}*/
}
