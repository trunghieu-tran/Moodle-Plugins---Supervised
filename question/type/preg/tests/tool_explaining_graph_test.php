<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for explainning graph tool.
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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');

class qtype_preg_tool_explaining_graph_test extends PHPUnit_Framework_TestCase
{

    /**
     * Compares two nodes.
     * @param n1 - first node.
     * @param n2 - second node.
     * @return true if two nodes of graph are equal.
     */
    public static function cmp_nodes(&$n1, &$n2)
    {
        if ($n1->color != $n2->color) {
            print("\nColors of nodes failed! " . $n1->color . ' != ' . $n2->color . chr(10));
            return false;
        }
        if ($n1->label != $n2->label) {
            print("\nLabels of nodes failed! " . $n1->label[0] . ' != ' . $n2->label[0] . chr(10));
            return false;
        }
        if ($n1->shape != $n2->shape) {
            print("\nShapes of nodes failed! " . $n1->shape . ' != ' . $n2->shape . chr(10));
            return false;
        }

        return true;
    }

    /**
     * Compares two graphs (subgraphs).
     * @param g1 - first graph.
     * @param g2 - second graph.
     * @return true if two subgraphs are equal.
     */
    public static function cmp_graphs(&$g1, &$g2)
    {
        if ($g1->label != $g2->label) {
            print(chr(10));
            print('Labels of subgraphs failed! ' . $g1->label . ' != ' . $g2->label . chr(10));
            return false;
        }
        if ($g1->style != $g2->style) {
            print(chr(10));
            print('Styles of subgraphs failed! ' . $g1->style . ' != ' . $g2->style . chr(10));
            return false;
        }

        if (count($g1->nodes) == count($g2->nodes)) {
            for ($i = 0; $i < count($g1->nodes); ++$i) {
                $isnodesnotequal = !self::cmp_nodes($g1->nodes[$i], $g2->nodes[$i]);
                if ($isnodesnotequal) {
                    return false;
                }
            }
        } else {
            print('Count of nodes is different.' . chr(10));
            return false;
        }

        if (count($g1->entries) == count($g2->entries)) {
            for ($i = 0; $i < count($g1->entries); ++$i) {
                $isnodesnotequal = !self::cmp_nodes($g1->entries[$i], $g2->entries[$i]);
                if ($isnodesnotequal) {
                    return false;
                }
            }
        } else {
            print('Count of entries is different.' . chr(10));
            return false;
        }

        if (count($g1->exits) == count($g2->exits)) {
            for ($i = 0; $i < count($g1->exits); ++$i) {
                $isnodesnotequal = !self::cmp_nodes($g1->exits[$i], $g2->exits[$i]);
                if ($isnodesnotequal) {
                    return false;
                }
            }
        } else {
            print('Count of exits is different.' . chr(10));
            return false;
        }

        if (count($g1->links) == count($g2->links)) {
            for ($i = 0; $i < count($g1->links); ++$i) {
                if ($g1->links[$i]->label != $g2->links[$i]->label) {
                    print ($i + 1) . ' link is different, because labels are different: ' . $g1->links[$i]->label . ' != ' . $g2->links[$i]->label . chr(10);
                    return false;
                }
                if (!self::cmp_nodes($g1->links[$i]->destination, $g2->links[$i]->destination)) {
                    print ($i + 1) . ' link is different' . chr(10);
                    return false;
                }
                if (!self::cmp_nodes($g1->links[$i]->source, $g2->links[$i]->source)) {
                    print ($i + 1) . ' link is different' . chr(10);
                    return false;
                }
            }
        } else {
            print('Count of links is different.' . chr(10));
            return false;
        }

        if (count($g1->subgraphs) == count($g2->subgraphs)) {
            for ($i = 0; $i < count($g1->subgraphs); ++$i) {
                if (!self::cmp_graphs($g1->subgraphs[$i], $g2->subgraphs[$i])) {
                    print ($i + 1) . ' subgraph is different' . chr(10);
                    return false;
                }
            }
        } else {
            print('Count of subgraph is different.' . chr(10));
            return false;
        }

        return true;
    }

    public function test_create_graph_subpattern()
    {
        $graph = new qtype_preg_explaining_graph_tool('(b)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1', 'solid; color=black');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with subpattern!');

        // -----------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('(?:\d)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; color=black');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a decimal digit'), 'ellipse', 'hotpink', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with grouping!');
    }

    public function test_create_graph_alter()
    {
        $graph = new qtype_preg_explaining_graph_tool('.|\D');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('any character'), 'ellipse', 'hotpink', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('not a decimal digit'), 'ellipse', 'hotpink', $etalon, 1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[3]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[4], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[5]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with alternation!');
    }

    public function test_create_graph_charclass()
    {
        $graph = new qtype_preg_explaining_graph_tool('[ab0-9?]');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('ab?', 'from 0 to 9'), 'record', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with charclass!');
    }

    public function test_create_graph_alone_meta()
    {
        $graph = new qtype_preg_explaining_graph_tool('\W');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('not a word character'), 'ellipse', 'hotpink', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with alone meta!');
    }

    public function test_create_graph_alone_simple()
    {
        $graph = new qtype_preg_explaining_graph_tool('test');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('test'), 'ellipse', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with alone simple!');
    }

    public function test_create_graph_asserts()
    {
        $graph = new qtype_preg_explaining_graph_tool('^$');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('start of the string\nend of the string', $etalon->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with asserts!');
    }

    public function test_create_graph_quantifiers()
    {
        $graph = new qtype_preg_explaining_graph_tool('x+');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(' repeated any number of times');
        $etalon->subgraphs[0]->style = 'dotted';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with quantifier +!');

        // -----------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('x*');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(' repeated any number of times or missing');
        $etalon->subgraphs[0]->style = 'dotted';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with quantifier *!');

        // -----------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('x?');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(' may be missing');
        $etalon->subgraphs[0]->style = 'dotted';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with quantifier ?!');

        // -----------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('x{3,7}');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph(' repeated from 3 to 7 times');
        $etalon->subgraphs[0]->style = 'dotted';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('x'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with quantifier {}!');
    }

    public function test_create_graph_assert_and_subgraph()
    {
        $graph = new qtype_preg_explaining_graph_tool('^(a)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('start of the string', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)!');

        // ---------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('a(\b)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('a word boundary', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with assert and subgraph a(\b)!');

        // ---------------------------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('^(a)$');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('start of the string', $etalon->nodes[0], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[3]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('end of the string', $etalon->nodes[3], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with assert and subgraph ^(a)$!');
    }

    public function test_create_graph_backref()
    {
        $graph = new qtype_preg_explaining_graph_tool('(b)\1');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('text that matched subpattern #1'), 'ellipse', 'blue', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with backreference!');

        // ----------------------------------------------------------

        $graph = new qtype_preg_explaining_graph_tool('(b)\2');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('text that matched subpattern #2'), 'ellipse', 'blue', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -3);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with faked backreference!');
    }

    public function test_create_graph_multialter()
    {
        $graph = new qtype_preg_explaining_graph_tool('abc|acb|bac|bca|cab|cba');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');

        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('acb'), 'ellipse', 'black', $etalon, 5);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('bac'), 'ellipse', 'black', $etalon, 11);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('bca'), 'ellipse', 'black', $etalon, 17);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('cab'), 'ellipse', 'black', $etalon, 23);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('cba'), 'ellipse', 'black', $etalon, 29);

        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);

        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);

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

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with multialter!');
    }

    public function test_create_graph_double_qoute()
    {
        $graph = new qtype_preg_explaining_graph_tool('".\\"');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('&#34;'), 'ellipse', 'black', $etalon, 0);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('any character'), 'ellipse', 'hotpink', $etalon, 1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('&#34;'), 'ellipse', 'black', $etalon, 2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[2]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[4]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with double quote!');
    }

    public function test_create_graph_recursion()
    {
        $graph = new qtype_preg_explaining_graph_tool('(abc(?R))');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1', 'solid; color=black');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon->subgraphs[0], 0);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(
            array('recursive match with whole regular expression'),
            'ellipse',
            'blue',
            $etalon->subgraphs[0],
            5
        );
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with recursion!');
    }

    public function test_create_graph_caseinsensetive()
    {
        $graph = new qtype_preg_explaining_graph_tool('(?i:abc)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[0]->style = 'filled';
        $etalon->subgraphs[0]->color = 'lightgrey';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'box', 'orange', $etalon->subgraphs[0], 3);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon->subgraphs[0], 4, 'filled', 'grey');
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[1]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with caseinsensetive!');
    }

    public function test_process_charset_interval()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('0-9'));

        $result = $node->get_value();

        $etalon = array('from 0 to 9');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed common range.');

        // ----------------------------------------------------

        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\x30-\x39'));

        $result = $node->get_value();

        $etalon = array('from 0 to 9');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed hex range.');
    }

    public function test_process_charset_posix()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('[:alpha:]'));

        $result = $node->get_value();

        $etalon = array('&#91;:alpha:&#93;');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed posix class.');
    }

    public function test_process_charset_unicode()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\p{C}'));

        $result = $node->get_value();

        $etalon = array('p&#123;C&#125;');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed unicode property with { }.');

        // ---------------------------------------------------

        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\pC'));

        $result = $node->get_value();

        $etalon = array('pC');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed unicode property without { }.');
    }

    public function test_process_charset_code()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\x{30}'));

        $result = $node->get_value();

        $etalon = array('0');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed character code with { }.');

        // ---------------------------------------------------

        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\x30'));

        $result = $node->get_value();

        $etalon = array('0');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed character code without { }.');
    }

    public function test_process_charset_special()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\d'));

        $result = $node->get_value();

        $etalon = array('d');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed special.');
    }

    public function test_process_charset_nospecial()
    {
        $node = new qtype_preg_explaining_graph_leaf_charset(null, null);
        $node->pregnode = new qtype_preg_leaf_charset();
        $node->pregnode->userinscription = array(new qtype_preg_userinscription('\y'));

        $result = $node->get_value();

        $etalon = array('y');

        $this->assertTrue(count(array_diff($etalon, $result)) == 0, 'Failed nospecial.');
    }

    public function test_process_simple_characters()
    {
        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('abc'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $tmp = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;
        $result->nodes[] = $tmp;
        $tmp = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 1, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;
        $result->nodes[] = $tmp;
        $tmp = new qtype_preg_explaining_graph_tool_node(array('c'), 'ellipse', 'black', $result, 2, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;
        $result->nodes[] = $tmp;
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[3], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[4], $result);

        $result->process_simple_characters($result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with combinable characters!');

        // ----------------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10) . 'not word character'), 'ellipse', 'hotpink', $etalon, 1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array(chr(10) . 'not word character'), 'ellipse', 'hotpink', $result, 1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[3], $result);

        $result->process_simple_characters($result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with uncombinable characters!');
    }

    public function recurse_process_asserts($graph, $parent, $gmain)
    {
        $graph->process_asserts($parent, $gmain);
        foreach ($graph->subgraphs as $subgraph) {
            $subgraph->process_asserts($graph, $gmain);
        }
    }

    public function test_process_asserts()
    {
        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[2], $result);

        $result->process_asserts($result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed without assert!');

        // ----------------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 2, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->nodes[0], $etalon->nodes[1], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $tmp = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;
        $result->nodes[] = $tmp;
        $tmp = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result, 1, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_ASSERT;
        $result->nodes[] = $tmp;
        $tmp = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 2, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;
        $result->nodes[] = $tmp;
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[3], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[4], $result);

        $result->process_asserts($result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed first case!');

        // -----------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 2, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->nodes[0], $etalon->nodes[1], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result, 1, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 2, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[3], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[4], $result);

        $result->process_asserts($result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed second case when left neighbor is child!');

        // -----------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[0], 2, '');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[1], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result, 1, '');
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result->subgraphs[0], 2, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->subgraphs[0]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[0], $result->nodes[3], $result);

        $result->process_asserts($result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed second case when left neighbor is not child!');

        // -----------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0, '');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link(
            'beginning of the string',
            $etalon->subgraphs[0]->nodes[0],
            $etalon->subgraphs[0]->nodes[1],
            $etalon->subgraphs[0]
        );
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[0], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 0, '');
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result->subgraphs[0], 1, '');
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result->subgraphs[0], 2, '');
        $result->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[0], $result->subgraphs[0]->nodes[1], $result->subgraphs[0]);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[1], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->subgraphs[0]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[2], $result);

        $this->recurse_process_asserts($result, $result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed third case when right neighbor is child!');

        // -----------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        // ---------------------------------
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0, '');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link(
            'beginning of the string',
            $etalon->subgraphs[0]->nodes[0],
            $etalon->subgraphs[0]->nodes[1],
            $etalon->subgraphs[0]
        );
        // ---------------------------------
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[1]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 0, '');
        // ---------------------------------
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[1]->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[1]->nodes[0], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        // ---------------------------------
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result->subgraphs[0], 0, '');
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result->subgraphs[0], 2, '');
        $result->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[0], $result->subgraphs[0]->nodes[1], $result->subgraphs[0]);
        // ---------------------------------
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[1]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 0, '');
        // ---------------------------------
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[1], $result->subgraphs[1]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->subgraphs[0]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[1]->nodes[0], $result->nodes[1], $result);

        $this->recurse_process_asserts($result, $result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed third case when right neighbor is not child!');

        // -----------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        // ---------------------------------
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], 0, '');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]);
        // ---------------------------------
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $etalon->subgraphs[1]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon->subgraphs[1], 0, '');
        $etalon->subgraphs[1]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[1], -1);
        $etalon->subgraphs[1]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[1]->nodes[1], $etalon->subgraphs[1]->nodes[0], $etalon->subgraphs[1]);
        // ---------------------------------
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[1]->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('beginning of the string', $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[1]->nodes[1], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('beginning of the string'), 'ellipse', 'red', $result, 1, '');
        // ---------------------------------
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result->subgraphs[0], 0, '');
        // ---------------------------------
        $result->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid', -1);
        $result->subgraphs[1]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result->subgraphs[1], 2, '');
        // ---------------------------------
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[0]->nodes[0], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->subgraphs[1]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->subgraphs[0]->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->subgraphs[1]->nodes[0], $result->nodes[2], $result);

        $this->recurse_process_asserts($result, $result, $result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed fourth case!');
    }

    public function test_process_voids()
    {
        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 2, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $tmp = new qtype_preg_explaining_graph_tool_node(array(''), 'ellipse', 'orange', $result, 1, '');
        $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_VOID;
        $result->nodes[] = $tmp;
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 2, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[3], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[4], $result);

        $result->process_voids($result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with void!');

        // ----------------------------------------------------------------------------------

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 0, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $etalon, 1, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('c'), 'ellipse', 'black', $etalon, 2, '');
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[4], $etalon);

        $result = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $result, 0, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('b'), 'ellipse', 'black', $result, 1, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('c'), 'ellipse', 'black', $result, 2, '');
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $result, -1);
        $result->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $result, -1);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[0], $result->nodes[1], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[1], $result->nodes[2], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[3], $result->nodes[0], $result);
        $result->links[] = new qtype_preg_explaining_graph_tool_link('', $result->nodes[2], $result->nodes[4], $result);

        $result->process_voids($result);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed without void!');
    }

    public function test_node_assert()
    {
        $graph = new qtype_preg_explaining_graph_tool('(?=xy)z');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; edge[style=dotted, color=green]; node[style=dashed, color=green]; color=grey');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('xy'), 'ellipse', 'black', $etalon->subgraphs[0], 3);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('z'), 'ellipse', 'black', $etalon, 2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0], $etalon, 'normal, color="green"');
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[1]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0]);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3]);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with node assert!');
    }

    public function test_empty_selection()
    {
        $options = new qtype_preg_authoring_tools_options;
        $options->selection = new qtype_preg_position(2, 1);
        $graph = new qtype_preg_explaining_graph_tool('a||c', $options);
        $result = $graph->create_graph();

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'darkgreen';
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('c'), 'ellipse', 'black', $etalon, 4);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[3], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[1], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[4], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[3], $etalon->nodes[5], $etalon);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with empty selection!');

        // ----------------------------------------------------------------------------------

        $options = new qtype_preg_authoring_tools_options;
        $options->selection = new qtype_preg_position(2, 1);
        $graph = new qtype_preg_explaining_graph_tool('a()c', $options);
        $result = $graph->create_graph();

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->color = 'black';
        $etalon->subgraphs[0]->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[0]->subgraphs[0]->style = 'solid';
        $etalon->subgraphs[0]->subgraphs[0]->color = 'darkgreen';
        $etalon->subgraphs[0]->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0]->subgraphs[0], -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, 2);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('c'), 'ellipse', 'black', $etalon, 4);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->subgraphs[0]->nodes[0], $etalon->nodes[1], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[2], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[3], $etalon);

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with empty selection in subpattern!');
    }

    public function test_preserved_node_selection()
    {
        $options = new qtype_preg_authoring_tools_options;
        $options->selection = new qtype_preg_position(0, 4);
        $graph = new qtype_preg_explaining_graph_tool('a(?i)b', $options);
        $result = $graph->create_graph();
        //var_dump($result);
    }

    public function test_node_assert_with_simple_assert()
    {
        $graph = new qtype_preg_explaining_graph_tool('(?=a\b)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; edge[style=dotted, color=green]; node[style=dashed, color=green]; color=grey');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->subgraphs[0]->nodes[1], $etalon->subgraphs[0]);
        $etalon->subgraphs[0]->links[] = new qtype_preg_explaining_graph_tool_link(
            'a word boundary',
            $etalon->subgraphs[0]->nodes[1],
            $etalon->subgraphs[0]->nodes[2],
            $etalon->subgraphs[0]
        );
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with node assert with simple assert!');
    }

    public function test_node_assert_with_void()
    {
        $graph = new qtype_preg_explaining_graph_tool('(?=)');

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('', 'solid');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('', 'solid; edge[style=dotted, color=green]; node[style=dashed, color=green]; color=grey');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon->subgraphs[0], -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);

        $result = $graph->create_graph();

        $this->assertTrue(self::cmp_graphs($result, $etalon), 'Failed with node assert with simple assert!');
    }

    public function test_assert_1()
    {
        $tool = new qtype_preg_explaining_graph_tool('(q)$^a');
        $graph = $tool->create_graph();

        $etalon = new qtype_preg_explaining_graph_tool_subgraph('');
        $etalon->subgraphs[] = new qtype_preg_explaining_graph_tool_subgraph('subpattern #1');
        $etalon->subgraphs[0]->nodes[] = new qtype_preg_explaining_graph_tool_node(array('q'), 'ellipse', 'black', $etalon->subgraphs[0], -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('a'), 'ellipse', 'black', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box', 'purple', $etalon, -1);
        $etalon->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $etalon, -1);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[1], $etalon->subgraphs[0]->nodes[0], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->nodes[0], $etalon->nodes[2], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('', $etalon->subgraphs[0]->nodes[0], $etalon->nodes[3], $etalon);
        $etalon->links[] = new qtype_preg_explaining_graph_tool_link('end of the string\nstart of the string', $etalon->nodes[3], $etalon->nodes[0], $etalon);

        /*echo chr(10);
        print_r($graph);
        echo chr(10);*/

        $this->assertTrue(self::cmp_graphs($graph, $etalon), 'Failed with assert 1!');
    }

    /*public function test_assert_2() {
        $tool = new qtype_preg_explaining_graph_tool('(a)(\b)b');
        $graph = $tool->create_graph();
        $dotscript = $graph->create_dot();
        var_dump($dotscript);
    }*/

    /*function test_temp() {
        $options = new qtype_preg_handling_options();
        //$options->selection = new qtype_preg_position(0, 4);
        $tree = new qtype_preg_explaining_graph_tool('(b)($)+', $options);
        $graph = $tree->create_graph();
        print_r($graph);
    }*/
}
