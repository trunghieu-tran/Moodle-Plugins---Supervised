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

    function test_label_tooltip_charset() {
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
        $this->assertEquals($node->label(), '[αя]');
        $this->assertEquals($node->tooltip(), 'character set&#10;α&#10;я');

        // Negative character set of one character.
        $node = $tree->from_preg_node($this->get_pregnode('[^α]'));
        $this->assertEquals($node->label(), '[^α]');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;α');

         // Negative character set of multiple characters.
        $node = $tree->from_preg_node($this->get_pregnode('[^ab]'));
        $this->assertEquals($node->label(), '[^ab]');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;a&#10;b');

        // Single flag.
        $node = $tree->from_preg_node($this->get_pregnode('\w'));
        $this->assertEquals($node->label(), '\w');
        $this->assertEquals($node->tooltip(), 'a word character');

        // Single negative flag.
        $node = $tree->from_preg_node($this->get_pregnode('\W'));
        $this->assertEquals($node->label(), '\W');
        $this->assertEquals($node->tooltip(), 'not a word character');

        // All flags.
        $node = $tree->from_preg_node($this->get_pregnode('[\d\D\h\H\s\S\v\V\w\W]'));
        $this->assertEquals($node->label(), '[\d\D\h\H\s\S\v\V\w\W]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              'a decimal digit&#10;'.
                                              'not a decimal digit&#10;'.
                                              'a horizontal white space character&#10;'.
                                              'not a horizontal white space character&#10;'.
                                              'a white space&#10;'.
                                              'not a white space&#10;'.
                                              'a vertical white space character&#10;'.
                                              'not a vertical white space character&#10;'.
                                              'a word character&#10;'.
                                              'not a word character');

        // All POSIX classes.
        $node = $tree->from_preg_node($this->get_pregnode('[[:alnum:][:alpha:][:ascii:][:blank:][:cntrl:][:digit:][:graph:][:lower:][:print:][:punct:][:space:][:upper:][:word:][:xdigit:]]'));
        $this->assertEquals($node->label(), '[[:alnum:][:alpha:][:ascii:][:blank:][:cntrl:][:digit:][:graph:][:lower:][:print:][:punct:][:space:][:upper:][:word:][:xdigit:]]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              'a letter or digit&#10;'.
                                              'a letter&#10;'.
                                              'a character with codes 0-127&#10;'.
                                              'a space or tab only&#10;'.
                                              'a control character&#10;'.
                                              'a decimal digit&#10;'.
                                              'a printing character (excluding space)&#10;'.
                                              'a lower case letter&#10;'.
                                              'a printing character (including space)&#10;'.
                                              'a printing character (excluding letters and digits and space)&#10;'.
                                              'a white space&#10;'.
                                              'an upper case letter&#10;'.
                                              'a word character&#10;'.
                                              'a hexadecimal digit');

        // Positive and negative POSIX classes.
        $node = $tree->from_preg_node($this->get_pregnode('[[:alnum:][:^alpha:]]'));
        $this->assertEquals($node->label(), '[[:alnum:][:^alpha:]]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              'a letter or digit&#10;'.
                                              'not a letter');

        // Unicode properties.
        $node = $tree->from_preg_node($this->get_pregnode('[\pL\PM]'));
        $this->assertEquals($node->label(), '[\pL\PM]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              'letter&#10;'.
                                              'not mark');

    }

    function test_label_tooltip_simple_assertions() {
        $tree = new qtype_preg_syntax_tree_tool();

        $node = $tree->from_preg_node($this->get_pregnode('\\b'));
        $this->assertEquals($node->label(), '\\b');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_B, 'qtype_preg'));
        $node = $tree->from_preg_node($this->get_pregnode('\\B'));
        $this->assertEquals($node->label(), '\\B');
        $this->assertEquals($node->tooltip(), 'not ' . get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_B, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('\\A'));
        $this->assertEquals($node->label(), '\\A');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_A, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('\\z'));
        $this->assertEquals($node->label(), '\\z');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_Z, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('\\Z'));
        $this->assertEquals($node->label(), '\\Z');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_Z, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('\\G'));
        $this->assertEquals($node->label(), '\\G');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_G, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('^'));
        $this->assertEquals($node->label(), '^');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX, 'qtype_preg'));

        $node = $tree->from_preg_node($this->get_pregnode('$'));
        $this->assertEquals($node->label(), '$');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_DOLLAR, 'qtype_preg'));
    }

    function test_label_subexpr() {
        $tree = new qtype_preg_syntax_tree_tool('(?<name>body)');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '(?&#60;name&#62;...) #1');
        $this->assertEquals($node->tooltip(), 'subexpression \\"name\\" #1');
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
