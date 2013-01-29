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
require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_tool.php');
require_once($CFG->dirroot . '/question/type/preg/preg_dotstyleprovider.php');

class qtype_preg_explain_graph_test extends PHPUnit_Framework_TestCase
{
   function test_create_graph_subpattern()
   {
       $tree = new qtype_preg_author_tool_explain_graph('(b)');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('subpattern #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with subpattern!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('(?:\d)');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array(chr(10).'decimal digit'), 'ellipse', 'green', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with grouping!');
   }

   function test_create_graph_alter()
   {
       $tree = new qtype_preg_author_tool_explain_graph('.|\D');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array(chr(10).'printing character (including space)'), 'ellipse', 'green', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array(chr(10).'not decimal digit'), 'ellipse', 'green', $etalon, 1);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[4], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[3], $etalon->nodes[5]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alternative!');
   }

   function test_create_graph_charclass()
   {
       $tree = new qtype_preg_author_tool_explain_graph('[ab0-9?]');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('ab?', chr(10).'from 0 to 9'), 'record', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with charclass!');
   }

   function test_create_graph_alone_meta()
   {
       $tree = new qtype_preg_author_tool_explain_graph('\W');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array(chr(10).'not word character'), 'ellipse', 'green', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alone meta!');
   }

   function test_create_graph_alone_simple()
   {
       $tree = new qtype_preg_author_tool_explain_graph('test');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('test'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();

       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with alone simple!');
   }

   function test_create_graph_asserts()
   {
       $tree = new qtype_preg_author_tool_explain_graph('^$');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('beginning of the string\nend of the string', $etalon->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with asserts!');
   }

   function test_create_graph_quantifiers()
   {
       $tree = new qtype_preg_author_tool_explain_graph('x+');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 1 to infinity times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier +!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('x*');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 0 to infinity times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier *!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('x?');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 0 to 1 time', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier ?!');

       //-----------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('x{3,7}');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('from 3 to 7 times', 'dotted; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with quantifier {}!');
   }

   function test_create_graph_assert_and_subgraph()
   {
       $tree = new qtype_preg_author_tool_explain_graph('^(a)');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('subpattern #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('beginning of the string', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)!');

       //---------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('a(\b)');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('subpattern #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('at a word boundary', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('a'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph a(\b)!');

       //---------------------------------------------------------------------------

       $tree = new qtype_preg_author_tool_explain_graph('^(a)$');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('subpattern #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon->subgraphs[0], -1);
       $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->subgraphs[0]->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[2]);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('beginning of the string', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('end of the string', $etalon->subgraphs[0]->nodes[2], $etalon->nodes[1]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)$!');
   }

   function test_create_graph_backref()
   {
       $tree = new qtype_preg_author_tool_explain_graph('(b)\1');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');
       $etalon->subgraphs[] = new qtype_preg_author_tool_explain_graph_subgraph('subpattern #1', 'solid; color=black');
       $etalon->subgraphs[0]->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('the result of subpattern #1'), 'ellipse', 'blue', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[2]);

       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with backreference!');
   }

   function test_create_graph_multialter()
   {
       $tree = new qtype_preg_author_tool_explain_graph('abc|acb|bac|bca|cab|cba');

       $etalon = new qtype_preg_author_tool_explain_graph_subgraph('', 'solid');

       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('abc'), 'ellipse', 'black', $etalon, 0);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('acb'), 'ellipse', 'black', $etalon, 1);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('bac'), 'ellipse', 'black', $etalon, 2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('bca'), 'ellipse', 'black', $etalon, 3);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('cab'), 'ellipse', 'black', $etalon, 4);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('cba'), 'ellipse', 'black', $etalon, 5);

       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon, -1);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $etalon, -1);

       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $etalon, -2);
       $etalon->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $etalon, -3);

       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[5]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[5], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[8], $etalon->nodes[6]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[7], $etalon->nodes[9]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[4]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[3]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[2]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[1]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[6], $etalon->nodes[0]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[4], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[3], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[2], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[1], $etalon->nodes[7]);
       $etalon->links[] = new qtype_preg_author_tool_explain_graph_link('', $etalon->nodes[0], $etalon->nodes[7]);
       
       $result = $tree->create_graph();
       
       $this->assertTrue(qtype_preg_author_tool_explain_graph::cmp_graphs($result, $etalon), 'Failed with multialter!');
   }
}

?>
