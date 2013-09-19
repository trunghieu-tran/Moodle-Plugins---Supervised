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
    private $strings = null;
    private $question = null;
    private $engine = null;
    private $errormsgs = null;

    public function __construct($regex, $strings, $usecase, $exactmatch, $engine, $notation) {
        $this->regex = $regex;
        $this->strings = $strings;

        if ($this->regex == '') {
            return;
        }

        $regular = new qtype_preg_question;
        // Fill engine field that is used by the hint to determine whether colored string should be shown.
        $regular->engine = $engine;
        // Get matcher to see, if there are errors in the regular expression.
        $matcher = $regular->get_matcher($engine, $regex, /*'exactmatch'*/false,
                                         $regular->get_modifiers($usecase), (-1), $notation, true);

        if ($matcher->errors_exist()) {
            $this->errormsgs = $matcher->get_error_messages(true);
        } else {
            // Correct regex, so create a real matcher.
            // Do not use qtype_preg_question::get_matcher to pass selection to the options.
            // Engine script file would be required by get_matcher call above, so no reason to worry about it.
            $this->question = $regular;
            $matchingoptions = new qtype_preg_matching_options();
            $matchingoptions->modifiers = $regular->get_modifiers($usecase);
            $matchingoptions->extensionneeded = false; // No need to generate next characters there.
            $matchingoptions->capturesubexpressions = true; // Capture selection - or should this be false? TODO...
            $matchingoptions->notation = $notation;
            $matchingoptions->exactmatch = $exactmatch;
            // $this->matchingoptions->selection = $selection; // TODO - pass selection to the testing tool.
            $engineclass = 'qtype_preg_' . $engine;
            $this->engine = new $engineclass($regex, $matchingoptions);
        }
    }

    public function json_key() {
        return 'regex_test';
    }

    public function generate_json(&$json) {
        if ($this->regex == '') {
            $this->generate_json_for_empty_regex($json);
        } else if ($this->errormsgs !== null) {
            $this->generate_json_for_unaccepted_regex($json);
        } else {
            $this->generate_json_for_accepted_regex($json);
        }
    }

    public function generate_json_for_accepted_regex(&$json) {
        global $PAGE;
        // Generate colored string showing matched and non-matched parts of response.
        $renderer = $PAGE->get_renderer('qtype_preg');
        $hintmatch = $this->question->hint_object('hintmatchingpart');
        $strings = explode("\n", $this->strings);
        $result = '';
        
        foreach ($strings as $string) {
            $matchresults = $this->engine->match($string);
            $result .= $hintmatch->render_colored_string_by_matchresults($renderer, $matchresults) . '<br />';
        }
        $json[$this->json_key()] = $result;
    }

    public function generate_json_for_unaccepted_regex(&$json) {
        $result = '';
        foreach ($this->errormsgs as $error) {
            $result .= '<br />' . $error;
        }
        $json[$this->json_key()] = $result;
    }

    public function generate_json_for_empty_regex(&$json) {
        $json[$this->json_key()] = '';
    }
}
