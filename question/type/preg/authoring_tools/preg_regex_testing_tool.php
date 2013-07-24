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

/*
 * Testing regex on strings
 */
class qtype_preg_regex_testing_tool {

    private $renderer;
    private $answer;
    private $hintmatch;
    
    public function __construct($regex, $answer, $mathcer) {
        global $PAGE;
        $this->renderer = $PAGE->get_renderer('qtype_preg');
        $regular = qtype_preg_question::question_from_regex($regex, false, true, $mathcer, 'native');
        $this->hintmatch = $regular->hint_object('hintmatchingpart');
        $this->answer = $answer;
    }

    /**
     * Generate colored string showing matched and non-matched parts of response.
     *
     * @param array $json_array contains colored string
     */
    public function generate_json(&$json_array) {
        $this->generate_json_for_accepted_regex($json_array);
    }

    protected function json_key(){
        return 'regex_test';
    }

    protected function generate_json_for_empty_regex(&$json_array){
        $json_array[$this->json_key()] = '';
    }

    protected function generate_json_for_unaccepted_regex(&$json_array){
        $json_array[$this->json_key()] = 'Ooops, i can\'t build text';
    }

    protected function generate_json_for_accepted_regex(&$json_array){
        $answer = strtok($this->answers, "\n");
        $json_array[$this->json_key()] = $this->hintmatch->render_hint($this->renderer, null, null, array('answer' => $answer)) . "</br>";
        while(($answer = strtok("\n")) !== false)	{
            $json_array[$this->json_key()] .= $this->hintmatch->render_hint($this->renderer, null, null, array('answer' => $answer)) . "</br>";
        }
    }
}
