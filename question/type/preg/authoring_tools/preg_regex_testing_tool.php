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
require_once($CFG->dirroot . '/question/engine/states.php');
require_once($CFG->dirroot . '/question/type/rendererbase.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');

/*
 * Testing regex on strings
 */
class qtype_preg_regex_testing_tool {

    //TODO - PHPDoc comments!
    private $renderer;
    private $answers;
    private $hintmatch;
    private $errormsgs = null;

    //TODO - what means $answers?! is it $tests? change name probably...
    public function __construct($regex, $answers, $usecase, $exactmatch, $matcher, $notation) {
        global $PAGE;
        $this->renderer = $PAGE->get_renderer('qtype_preg');
        $regular = qtype_preg_question::question_from_regex($regex, $usecase, $exactmatch, $matcher, $notation);
        $matcher = $regular->get_matcher($matcher, $regex, /*'exactmatch'*/false,
                        $regular->get_modifiers($usecase), (-1), $notation, true);
        if ($matcher->errors_exist()) {
            $this->errormsgs = $matcher->get_error_messages(true);
        } else {
            $this->hintmatch = $regular->hint_object('hintmatchingpart');
            $this->answers = $answers;
        }
    }

    public function generate_json(&$json, $id = -1) {
        // Generate colored string showing matched and non-matched parts of response.
        $answers = explode("\n", $this->answers);
        $json[$this->json_key()] = '';
        foreach ($answers as $answer) {
            $json[$this->json_key()] .= $this->hintmatch->render_hint($this->renderer, null, null, array('answer' => $answer)) . '</br>';
        }
    }

    protected function json_key() {
        return 'regex_test';
    }
}
