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

class qtype_preg_tool_explaining_graph_test extends PHPUnit_Framework_TestCase {

    /**
     * Compares two nodes.
     * @param n1 - first node.
     * @param n2 - second node.
     * @return true if two nodes of graph are equal.
     */
    public static function cmp_nodes(&$n1, &$n2) {
        if ($n1->color != $n2->color) {
            print("\nColors of nodes failed! " . $n1->color . ' != ' . $n2->color);
            return false;
        }
        if ($n1->label != $n2->label) {
            print("\nLabels of nodes failed! "  . $n1->label[0] . ' != ' . $n2->label[0]);
            return false;
        }
        if ($n1->shape != $n2->shape) {
            print("\nShapes of nodes failed! "  . $n1->shape . ' != ' . $n2->shape);
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
    public static function cmp_graphs(&$g1, &$g2) {
        if ($g1->label != $g2->label) {
            print(chr(10));
            print('Labels of subgraphs failed!' . $g1->label . '!=' . $g2->label);
            return false;
        }
        if ($g1->style != $g2->style) {
            print(chr(10));
            print('Styles of subgraphs failed!' . $g1->style . '!=' . $g2->style);
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
            print('Count of nodes is different.');
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
            return false;
        }

        if (count($g1->links) == count($g2->links)) {
            for ($i = 0; $i < count($g1->links); ++$i) {
                if ($g1->links[$i]->label != $g2->links[$i]->label) {
                    return false;
                }
                if (!self::cmp_nodes($g1->links[$i]->destination, $g2->links[$i]->destination)) {
                    return false;
                }
                if (!self::cmp_nodes($g1->links[$i]->source, $g2->links[$i]->source)) {
                    return false;
                }
            }
        } else {
            print('Count of links is different.');
            return false;
        }

        if (count($g1->subgraphs) == count($g2->subgraphs)) {
            for ($i = 0; $i < count($g1->subgraphs); ++$i) {
                if (!self::cmp_graphs($g1->subgraphs[$i], $g2->subgraphs[$i])) {
                    return false;
                }
            }
        } else {
            print('Count of subgraph is different.');
            return false;
        }

        return true;
    }

    public function test_unicode() {
        $graph = new qtype_preg_explaining_graph_tool('абв');
        $json = array();
        $graph->generate_json($json);
    }
}
