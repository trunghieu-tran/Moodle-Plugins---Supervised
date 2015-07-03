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
ob_start();
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_regex_testing_tool_loader.php');
ob_end_clean();

class qtype_preg_tool_testing_test extends PHPUnit_Framework_TestCase {

    function test_loader_no_selection() {
        $_GET['regex'] = 'a';
        $_GET['engine'] = 'fa_matcher';
        $_GET['notation'] = 'native';
        $_GET['exactmatch'] = 0;
        $_GET['usecase'] = 0;
        $_GET['indfirst'] = -2;
        $_GET['indlast'] = -2;
        $_GET['strings'] = 'a';
        $_GET['ajax'] = 1;

        $json = qtype_preg_get_json_array();
        $this->assertEquals($json['indfirst'], -2);
        $this->assertEquals($json['indlast'], -2);
    }

    function test_loader_selection() {
        $_GET['regex'] = 'a';
        $_GET['engine'] = 'fa_matcher';
        $_GET['notation'] = 'native';
        $_GET['exactmatch'] = 0;
        $_GET['usecase'] = 0;
        $_GET['indfirst'] = 0;
        $_GET['indlast'] = 0;
        $_GET['strings'] = 'a';
        $_GET['ajax'] = 1;

        $json = qtype_preg_get_json_array();
        $this->assertEquals($json['indfirst'], 0);
        $this->assertEquals($json['indlast'], 0);
    }

    function test_loader_exact_selection() {
        $_GET['regex'] = 'a';
        $_GET['engine'] = 'fa_matcher';
        $_GET['notation'] = 'native';
        $_GET['exactmatch'] = 1;
        $_GET['usecase'] = 0;
        $_GET['indfirst'] = 0;
        $_GET['indlast'] = 0;
        $_GET['strings'] = 'a';
        $_GET['ajax'] = 1;

        $json = qtype_preg_get_json_array();
        $this->assertEquals($json['indfirst'], 0);
        $this->assertEquals($json['indlast'], 0);
    }

    function test_correct() {
        $regex = 'a|b';
        $strings = "a\nb";
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span></span><br /><span id="qtype-preg-colored-string"><span class="correct">b</span></span><br />');
    }

    function test_empty_strings() {
        $regex = 'a|b';
        $strings = '';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<br />');
    }

    function test_syntax_error() {
        $regex = 'smile! :)';
        $strings = ':/';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<br />smile! :<b>)</b><br/>Syntax error: missing opening parenthesis \'(\' for the closing parenthesis in position 8.');
    }

    function test_accepting_error() {
        $regex = '(?=some day this will be supported)...';
        $strings = 'wat';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<br /><b>(?=some day this will be supported)</b>...<br/>Lookaround assertion in position from 0:0 to 0:34 is not supported by finite state automata.');
    }

    function test_empty_regex() {
        $regex = '';
        $strings = '';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '');

        $strings = "a|b";
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position());
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '');
    }

    function test_selection_dummy() {
        $regex = 'a';
        $strings = 'a';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(0, 0));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="partiallycorrect">a</span></span><br />');
    }

    function test_selection_grouping() {
        $regex = 'a(?:bc)d';
        $strings = 'abcd';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(1, 6));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">bc</span><span class="correct">d</span></span><br />');

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(1, 1));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">bc</span><span class="correct">d</span></span><br />');

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(6, 6));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">bc</span><span class="correct">d</span></span><br />');
    }

    function test_selection_non_preserved() {
        $regex = 'a(?i)b';
        $strings = 'ab';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(1, 4));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">ab</span></span><br />');

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(0, 4));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="partiallycorrect">a</span><span class="correct">b</span></span><br />');

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(1, 5));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">b</span></span><br />');
    }

    function test_selection_partial_match() {
        $regex = '^a(bc)def';
        $strings = 'abc';
        $usecase = false;
        $exactmatch = false;
        $engine = 'fa_matcher';
        $notation = 'native';

        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(2, 5));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">bc</span>...</span><br />');

        $strings = 'ab';
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(2, 5));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span><span class="partiallycorrect">b</span>...</span><br />');

        $strings = 'a';
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(2, 5));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<span id="qtype-preg-colored-string"><span class="correct">a</span>...</span><br />');

        $strings = '';
        $tool = new qtype_preg_regex_testing_tool($regex, $strings, $usecase, $exactmatch, $engine, $notation, new qtype_preg_position(2, 5));
        $json = $tool->generate_json();
        $str = strip_tags($json['regex_test'], '<span><br><b>');
        $this->assertEquals($str, '<br />');
    }
 }
