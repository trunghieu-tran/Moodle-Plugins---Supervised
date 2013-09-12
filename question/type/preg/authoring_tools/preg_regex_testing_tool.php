<?php
/**
 * Defines class which is builder of graphical syntax tree.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Terechov Grigory <grvlter@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package qtype_preg
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/engine/states.php');
require_once($CFG->dirroot . '/question/type/rendererbase.php');
require_once($CFG->dirroot . '/question/type/preg/preg_hints.php');
require_once($CFG->dirroot . '/question/type/preg/question.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

/*
 * Defines a tool for testing regex against strings.
 */
class qtype_preg_regex_testing_tool implements qtype_preg_i_authoring_tool {

    //TODO - PHPDoc comments!
    private $regex = '';
    private $renderer = null;
    private $strings = null;
    private $hintmatch = null;
    private $errormsgs = null;

    public function __construct($regex, $strings, $usecase, $exactmatch, $matcher, $notation) {
        global $PAGE;
        $this->regex = $regex;
        $this->renderer = $PAGE->get_renderer('qtype_preg');
        $this->strings = $strings;

        if ($this->regex == '') {
            return;
        }

        $regular = qtype_preg_question::question_from_regex($regex, $usecase, $exactmatch, $matcher, $notation);
        $matcher = $regular->get_matcher($matcher, $regex, /*'exactmatch'*/false,
                                         $regular->get_modifiers($usecase), (-1), $notation, true);
        if ($matcher->errors_exist()) {
            $this->errormsgs = $matcher->get_error_messages(true);
        } else {
            $this->hintmatch = $regular->hint_object('hintmatchingpart');
        }
    }

    public function json_key() {
        return 'regex_test';
    }

    public function generate_json(&$json, $id = -1) {
        if ($this->regex == '') {
            $this->generate_json_for_empty_regex($json, $id);
        } else if ($this->errormsgs !== null) {
            $this->generate_json_for_unaccepted_regex($json, $id);
        } else {
            $this->generate_json_for_accepted_regex($json, $id);
        }
    }

    public function generate_json_for_accepted_regex(&$json, $id = -1) {
        // Generate colored string showing matched and non-matched parts of response.
        $strings = explode("\n", $this->strings);
        $result = '';
        foreach ($strings as $answer) {
            $result .= $this->hintmatch->render_hint($this->renderer, null, null, array('answer' => $answer)) . '</br>';
        }
        $json[$this->json_key()] = $result;
    }

    public function generate_json_for_unaccepted_regex(&$json, $id = -1) {
        $result = '';
        foreach ($this->errormsgs as $error) {
            $result .= '<br />' . $error;
        }
        $json[$this->json_key()] = $result;
    }

    public function generate_json_for_empty_regex(&$json, $id = -1) {
        $json[$this->json_key()] = '';
    }
}
