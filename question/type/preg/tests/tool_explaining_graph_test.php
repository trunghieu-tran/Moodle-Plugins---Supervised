<?php

/**
 * Unit tests for explain graph tool.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Vladimir Ivanov
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_nodes.php');

class qtype_preg_tool_explaining_graph_test extends PHPUnit_Framework_TestCase {
   function test_create_graph_subexpression() {
       $tree = new qtype_preg_explaining_graph_tool('(b)');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with subexpression!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('(?:\d)');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10).'decimal digit'), 'ellipse', 'hotpink', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with grouping!');
   }

   function test_create_graph_alter() {
       $tree = new qtype_preg_explaining_graph_tool('.|\D');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10).'printing character (including space)'), 'ellipse', 'hotpink', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10).'not decimal digit'), 'ellipse', 'hotpink', $etalon, 1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[4], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[5]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with alternation!');
   }

   function test_create_graph_charclass() {
       $tree = new qtype_preg_explaining_graph_tool('[ab0-9?]');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('ab?', chr(10).'from 0 to 9'), 'record', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with charclass!');
   }

   function test_create_graph_alone_meta() {
       $tree = new qtype_preg_explaining_graph_tool('\W');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10).'not word character'), 'ellipse', 'hotpink', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with alone meta!');
   }

   function test_create_graph_alone_simple() {
       $tree = new qtype_preg_explaining_graph_tool('test');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('test'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with alone simple!');
   }

   function test_create_graph_asserts() {
       $tree = new qtype_preg_explaining_graph_tool('^$');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string\nend of the string', $etalon->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with asserts!');
   }

   function test_create_graph_quantifiers() {
       $tree = new qtype_preg_explaining_graph_tool('x+');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('from 1 to infinity times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with quantifier +!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('x*');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('from 0 to infinity times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with quantifier *!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('x?');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('from 0 to 1 time', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with quantifier ?!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('x{3,7}');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('from 3 to 7 times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with quantifier {}!');
   }

   function test_create_graph_assert_and_subgraph() {
       $tree = new qtype_preg_explaining_graph_tool('^(a)');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)!');

       //---------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('a(\b)');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('at a word boundary', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with assert and subgraph a(\b)!');

       //---------------------------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('^(a)$');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[2]);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('end of the string', $etalon->subgraphs[0]->nodes[2], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)$!');
   }

   function test_create_graph_backref() {
       $tree = new qtype_preg_explaining_graph_tool('(b)\1');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('back reference to subexpression #1'), 'ellipse', 'blue', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with backreference!');

       //----------------------------------------------------------

       $tree = new qtype_preg_explaining_graph_tool('(b)\2');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('back reference to subexpression #2'), 'ellipse', 'blue', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with faked backreference!');
   }

   function test_create_graph_multialter() {
       $tree = new qtype_preg_explaining_graph_tool('abc|acb|bac|bca|cab|cba');

       $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('acb'), 'ellipse', 'black', $etalon, 5);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('bac'), 'ellipse', 'black', $etalon, 11);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('bca'), 'ellipse', 'black', $etalon, 17);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('cab'), 'ellipse', 'black', $etalon, 23);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('cba'), 'ellipse', 'black', $etalon, 29);

       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);

       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);

       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[4]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[4], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[6], $etalon->nodes[5]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[5], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[8], $etalon->nodes[6]);
       $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[7], $etalon->nodes[9]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with multialter!');
   }

   function test_create_graph_double_qoute() {
        $tree = new qtype_preg_explaining_graph_tool('".\\"');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('"'), 'ellipse', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10).'printing character (including space)'), 'ellipse', 'hotpink', $etalon, 1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('"'), 'ellipse', 'black', $etalon, 2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[4]);

        $result = $tree->create_graph();

        $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with double quote!');
   }

   function test_create_graph_recursion() {
        $tree = new qtype_preg_explaining_graph_tool('(abc(?R))');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subexpression #1', 'solid; color=black');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('recursive match with whole regular expression'), 'ellipse', 'blue', $etalon->subgraphs[0], 5);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[1]);

        $result = $tree->create_graph();

        $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with recursion!');
   }

   function test_create_graph_caseinsensetive() {
        $tree = new qtype_preg_explaining_graph_tool('(?i:abc)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'filled;color=lightgrey');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon->subgraphs[0], 3, ', style=filled, fillcolor=grey');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $tree->create_graph();

        $this->assertTrue(qtype_preg_explaining_graph_tool::cmp_graphs($result, $etalon), 'Failed with caseinsensetive!');
   }

   function test_process_charset_interval() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('0-9'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'from 0 to 9');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed common range.');
       
       // ----------------------------------------------------

       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\x30-\x39'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'from character with code 0x30 to character with code 0x39');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed hex range.');
   }

   function test_process_charset_posix() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('[:alpha:]'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'letter');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed posix class.');
   }

   function test_process_charset_unicode() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\p{C}'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'other unicode property');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed unicode property with { }.');

       // ---------------------------------------------------

       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\pC'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'other unicode property');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed unicode property without { }.');
   }

   function test_process_charset_code() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\x{30}'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'character with code 0x30');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed character code with { }.');

       // ---------------------------------------------------

       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\X30'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'character with code 0x30');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed character code without { }.');
   }

   function test_process_charset_special() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\d'));

       $result = $node->process_charset();

       $etalon = array(chr(10) . 'decimal digit');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed special.');
   }

   function test_process_charset_nospecial() {
       $node = new qtype_preg_authoring_tool_leaf_charset(NULL, NULL);
       $node->pregnode = new qtype_preg_leaf_charset();
       $node->pregnode->userinscription = array(new qtype_preg_userinscription('\y'));

       $result = $node->process_charset();

       $etalon = array('y');

       $this->assertTrue(sizeof(array_diff($etalon, $result)) == 0, 'Failed nospecial.');
   }
}

?>
