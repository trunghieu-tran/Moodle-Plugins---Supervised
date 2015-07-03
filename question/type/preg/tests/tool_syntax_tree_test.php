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

    function get_node($str) {
        $options = new qtype_preg_handling_options();
        $options->preserveallnodes = true;
        StringStreamController::createRef('regex', $str);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        $token = $lexer->nextToken();

        $tree = new qtype_preg_syntax_tree_tool();
        if (is_array($token)) {
            $result = array();
            foreach ($token as $t) {
                $result[] = $tree->from_preg_node($t->value);
            }
            return $result;
        }
        return $tree->from_preg_node($token->value);
    }

    function test_label_tooltip_charset() {
        // Single character.
        $node = $this->get_node('α');
        $this->assertEquals($node->label(), 'α');
        $this->assertEquals($node->tooltip(), 'character α');

        $node = $this->get_node(' ');
        $this->assertTrue($node->needs_highlighting());
        $this->assertEquals($node->label(), get_string('description_char20', 'qtype_preg'));
        $this->assertEquals($node->tooltip(), 'character ' . get_string('description_char20', 'qtype_preg'));

        $node = $this->get_node("\r");
        $this->assertTrue($node->needs_highlighting());
        $this->assertEquals($node->label(), get_string('description_charD', 'qtype_preg'));
        $this->assertEquals($node->tooltip(), 'character ' . get_string('description_charD', 'qtype_preg'));

        // Single character in brackets.
        $node = $this->get_node('α');
        $this->assertEquals($node->label(), 'α');
        $this->assertEquals($node->tooltip(), 'character α');

        $node = $this->get_node('[ ]');
        $this->assertFalse($node->needs_highlighting());
        $this->assertEquals($node->label(), '[ ]');
        $this->assertEquals($node->tooltip(), 'character set&#10;' . get_string('description_char20', 'qtype_preg'));

        // Some characters in brackets.
        $node = $this->get_node('[αя]');
        $this->assertEquals($node->label(), '[αя]');
        $this->assertEquals($node->tooltip(), 'character set&#10;α&#10;я');

        // Negative character set of one character.
        $node = $this->get_node('[^α]');
        $this->assertEquals($node->label(), '[^α]');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;α');

         // Negative character set of multiple characters.
        $node = $this->get_node('[^ab]');
        $this->assertEquals($node->label(), '[^ab]');
        $this->assertEquals($node->tooltip(), 'negative character set&#10;a&#10;b');

        // Escape sequences representing single characters.
        $node = $this->get_node('\n');
        $this->assertEquals($node->label(), '\n');
        $this->assertEquals($node->tooltip(), get_string('description_charA', 'qtype_preg'));

        // Single flag.
        $node = $this->get_node('.');
        $this->assertEquals($node->label(), get_string('description_charflag_dot', 'qtype_preg'));
        $this->assertEquals($node->tooltip(), get_string('description_charflag_dot', 'qtype_preg'));

        $node = $this->get_node('\w');
        $this->assertEquals($node->label(), '\w');
        $this->assertEquals($node->tooltip(), get_string('description_charflag_slashw', 'qtype_preg'));

        // Single negative flag.
        $node = $this->get_node('\W');
        $this->assertEquals($node->label(), '\W');
        $this->assertEquals($node->tooltip(), get_string('description_charflag_slashw_neg', 'qtype_preg'));

        // All flags.
        $node = $this->get_node('[\d\D\h\H\s\S\v\V\w\W]');
        $this->assertEquals($node->label(), '[\d\D\h\H\s\S\v\V\w\W]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              get_string('description_charflag_slashd', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashd_neg', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashh', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashh_neg', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashs', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashs_neg', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashv', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashv_neg', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashw', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_slashw_neg', 'qtype_preg'));

        // All POSIX classes.
        $node = $this->get_node('[[:alnum:][:alpha:][:ascii:][:blank:][:cntrl:][:digit:][:graph:][:lower:][:print:][:punct:][:space:][:upper:][:word:][:xdigit:]]');
        $this->assertEquals($node->label(), '[[:alnum:][:alpha:][:ascii:][:blank:][:cntrl:][:digit:][:graph:][:lower:][:print:][:punct:][:space:][:upper:][:word:][:xdigit:]]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              get_string('description_charflag_alnum', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_alpha', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_ascii', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_blank', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_cntrl', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_digit', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_graph', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_lower', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_print', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_punct', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_space', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_upper', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_word', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_xdigit', 'qtype_preg'));

        // Positive and negative POSIX classes.
        $node = $this->get_node('[[:alnum:][:^alpha:]]');
        $this->assertEquals($node->label(), '[[:alnum:][:^alpha:]]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              get_string('description_charflag_alnum', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_alpha_neg', 'qtype_preg'));

        // Unicode properties.
        $node = $this->get_node('[\pL\PM]');
        $this->assertEquals($node->label(), '[\pL\PM]');
        $this->assertEquals($node->tooltip(), 'character set&#10;'.
                                              get_string('description_charflag_L', 'qtype_preg') . '&#10;' .
                                              get_string('description_charflag_M_neg', 'qtype_preg'));

        // Escaping.
        $node = $this->get_node('\\\\');
        $this->assertEquals($node->label(), '\\');
        $this->assertEquals($node->tooltip(), 'character \\');

        $node = $this->get_node('[\\]\\\\\\-]');   // For better understanding: [\]\\\-]
        $this->assertEquals($node->tooltip(), 'character set&#10;]&#10;\&#10;-');

        $nodes = $this->get_node('\18');
        $this->assertEquals($nodes[0]->label(), '\1');
        $this->assertEquals($nodes[1]->label(), '8');
    }

    function test_label_tooltip_simple_assertions() {
        $node = $this->get_node('\b');
        $this->assertEquals($node->label(), '\b');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_B, 'qtype_preg'));
        $node = $this->get_node('\B');
        $this->assertEquals($node->label(), '\B');
        $this->assertEquals($node->tooltip(), 'not ' . get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_B, 'qtype_preg'));

        $node = $this->get_node('\A');
        $this->assertEquals($node->label(), '\A');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_A, 'qtype_preg'));

        $node = $this->get_node('\z');
        $this->assertEquals($node->label(), '\z');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z, 'qtype_preg'));

        $node = $this->get_node('\Z');
        $this->assertEquals($node->label(), '\Z');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_CAPITAL_ESC_Z, 'qtype_preg'));

        $node = $this->get_node('\G');
        $this->assertEquals($node->label(), '\G');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_ESC_G, 'qtype_preg'));

        $node = $this->get_node('^');
        $this->assertEquals($node->label(), '^');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX, 'qtype_preg'));

        $node = $this->get_node('$');
        $this->assertEquals($node->label(), '$');
        $this->assertEquals($node->tooltip(), get_string(qtype_preg_leaf_assert::SUBTYPE_DOLLAR, 'qtype_preg'));
    }

    function test_label_options() {
        $node = $this->get_node('(?i)');
        $this->assertEquals($node->label(), '(?i)');
        $this->assertEquals($node->tooltip(), get_string('description_option_i', 'qtype_preg'));
    }

    function test_label_subexpr() {
        $tree = new qtype_preg_syntax_tree_tool('(?<name>body)');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '(?<name>...) #1');
        $this->assertEquals($node->tooltip(), 'subpattern "name" #1');
    }

    function test_label_finite_quant() {
        $tree = new qtype_preg_syntax_tree_tool('a{2,3}?');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{2,3}?');
        $this->assertEquals($node->tooltip(), 'operand repeated from 2 to 3 times (lazy quantifier)');

        $tree = new qtype_preg_syntax_tree_tool('a{2,3}');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{2,3}');
        $this->assertEquals($node->tooltip(), 'operand repeated from 2 to 3 times');

        $tree = new qtype_preg_syntax_tree_tool('a{2,3}+');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{2,3}+');
        $this->assertEquals($node->tooltip(), 'operand repeated from 2 to 3 times (possessive quantifier)');

        $tree = new qtype_preg_syntax_tree_tool('a?');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '?');
        $this->assertEquals($node->tooltip(), 'operand may be missing');

        $tree = new qtype_preg_syntax_tree_tool('a{0,2}');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{0,2}');
        $this->assertEquals($node->tooltip(), 'operand repeated no more than 2 times or missing');

        $tree = new qtype_preg_syntax_tree_tool('a{1,}');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{1,}');
        $this->assertEquals($node->tooltip(), 'operand repeated any number of times');

        $tree = new qtype_preg_syntax_tree_tool('a{8}');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '{8}');
        $this->assertEquals($node->tooltip(), 'operand repeated 8 times');
    }

    function test_label_infinite_quant() {
        $tree = new qtype_preg_syntax_tree_tool('a+?');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '+?');
        $this->assertEquals($node->tooltip(), 'operand repeated any number of times (lazy quantifier)');

        $tree = new qtype_preg_syntax_tree_tool('a+');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '+');
        $this->assertEquals($node->tooltip(), 'operand repeated any number of times');

        $tree = new qtype_preg_syntax_tree_tool('a++');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '++');
        $this->assertEquals($node->tooltip(), 'operand repeated any number of times (possessive quantifier)');
    }

    function test_label_qe() {
        $tree = new qtype_preg_syntax_tree_tool('\Q\\\E');
        $node = $tree->get_dst_root();
        $this->assertEquals($node->label(), '\\');
    }

    function test_spaces() {
        $tree = new qtype_preg_syntax_tree_tool(' ');
        $json = $tree->generate_json();
        //var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context($tree, true)));
    }

    function test_something() {
        $tree = new qtype_preg_syntax_tree_tool('(?(2)A)');
        $json = $tree->generate_json();
        //var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context($tree, true)));
    }

    function test_syntax_errors() {
        $tree = new qtype_preg_syntax_tree_tool('a(');
        $json = $tree->generate_json();
        //var_dump($tree->get_dst_root()->dot_script(new qtype_preg_dot_node_context($tree, true)));
    }

    function test_templates() {
        $tree = new qtype_preg_syntax_tree_tool('(?###word)');
        $json = $tree->generate_json();

        $tree = new qtype_preg_syntax_tree_tool('(?###bad_non_existing_word)');
        $json = $tree->generate_json();

        $tree = new qtype_preg_syntax_tree_tool('(?###parens_opt<)(?###word)(?###>)');
        $json = $tree->generate_json();

        $tree = new qtype_preg_syntax_tree_tool('(?###bad_non_existing_parens_opt<)(?###word)(?###>)');
        $json = $tree->generate_json();

        $tree = new qtype_preg_syntax_tree_tool('(?###parens_opt<)syntax error here');
        $json = $tree->generate_json();
    }
 }
