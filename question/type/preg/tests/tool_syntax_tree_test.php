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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_syntax_tree_tool.php');

class qtype_preg_tool_syntax_tree_test extends PHPUnit_Framework_TestCase {

    function get_pregnode($str) {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        StringStreamController::createRef('regex', $str);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        return $lexer->nextToken()->value;
    }

    function test_charset() {
        $tree = new qtype_preg_syntax_tree_tool();

        // Single character.
        $node = $tree->from_preg_node($this->get_pregnode('α'));
        $this->assertEquals($node->label(), 'α');
        $this->assertEquals($node->tooltip(), 'character α');

        // Single character in brackets.
        $node = $tree->from_preg_node($this->get_pregnode('α'));
        $this->assertEquals($node->label(), 'α');
        $this->assertEquals($node->tooltip(), 'character α');

        // Some characters in brackets.
        $node = $tree->from_preg_node($this->get_pregnode('[αя]'));
        $this->assertEquals($node->label(), '&#91;&#10;α&#10;я&#10;&#93;');
        $this->assertEquals($node->tooltip(), 'character set&#10;α&#10;я');

        // Negative character set of one character.
        $node = $tree->from_preg_node($this->get_pregnode('[^α]'));
        $this->assertEquals($node->label(), '&#91;^&#10;α&#10;&#93;');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;α');

         // Negative character set of multiple characters.
        $node = $tree->from_preg_node($this->get_pregnode('[^ab]'));
        $this->assertEquals($node->label(), '&#91;^&#10;a&#10;b&#10;&#93;');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;a&#10;b');

        // Single flag.
        $node = $tree->from_preg_node($this->get_pregnode('\w'));
        $this->assertEquals($node->label(), '\w');
        $this->assertEquals($node->tooltip(), 'a word character');

        // Single negative flag.
        $node = $tree->from_preg_node($this->get_pregnode('\W'));
        $this->assertEquals($node->label(), '\W');
        $this->assertEquals($node->tooltip(), 'not a word character');
    }

    function test_something() {
        $tree = new qtype_preg_syntax_tree_tool('(?:(a{6,6})|([^b-f]))(?(2)A)\1+[f\dgjf\w]f');
        //var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context($tree, true)));
    }

    function test_syntax_errors() {
        $tree = new qtype_preg_syntax_tree_tool('a(');
        /*$json = array();
        $tree->generate_json($json);*/
        //var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context($tree, true)));
    }
 }
