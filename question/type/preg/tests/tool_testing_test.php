<?php

/**
 * Unit tests for explain graph tool.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Terechov Grigory <grvlter@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool.php');

class qtype_preg_tool_testing_test extends PHPUnit_Framework_TestCase {

    function test_correct() {
        $regex = 'a|b';
        $strings = "a\nb";
        $usecase = false;
        $exactmatch = false;
        $engine = 'nfa_matcher';
        $notation = 'native';

        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '<span class="correct">a</span></br><span class="correct">b</span></br>');
    }

    function test_empty_strings() {
        $regex = 'a|b';
        $strings = '';
        $usecase = false;
        $exactmatch = false;
        $engine = 'nfa_matcher';
        $notation = 'native';

        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '</br>');
    }

    function test_syntax_error() {
        $regex = 'smile! :)';
        $strings = ':/';
        $usecase = false;
        $exactmatch = false;
        $engine = 'nfa_matcher';
        $notation = 'native';

        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '<br />smile! :<b>)</b><br/>Syntax error: missing opening parenthesis \'(\' for the closing parenthesis in position 8.');
    }

    function test_accepting_error() {
        $regex = '(?=some day this will be supported)...';
        $strings = 'wat';
        $usecase = false;
        $exactmatch = false;
        $engine = 'nfa_matcher';
        $notation = 'native';

        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '<br /><b>(?=some day this will be supported)</b>?=some day this will be supported)...<br/>Lookaround assertion in position from 0:0 to 0:34 is not supported by nondeterministic finite state automata.');
    }

    function test_empty_regex() {
        $regex = '';
        $strings = '';
        $usecase = false;
        $exactmatch = false;
        $engine = 'nfa_matcher';
        $notation = 'native';

        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '');

        $strings = "a|b";
        $json = array();
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation);
        $tool->generate_json($json);
        $this->assertTrue($json['regex_test'] == '');
    }
 }
