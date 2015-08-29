<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Defines abstract mapper from lexer to parser, which maps lexer tokens to parser AST
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy, Maria Birukova
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
define('MOODLE_INTERNAL', 1);

$CFG = new stdClass();
$CFG->dirroot = dirname(dirname(dirname(__FILE__)));
$CFG->libdir = $CFG->dirroot . '/lib';

require_once($CFG->dirroot.'/lib/classes/text.php');
require_once($CFG->dirroot .'/blocks/formal_langs/language_cpp_parseable_language.php');

/**
 * A class, which represents tree, needed to graphviz
 */
class block_formal_langs_tree_dot_representation
{
    /**
     * Constructs new empty graph
     */
    public function __construct() {
        $this->nodes = array();
        $this->edges = array();
    }

    /**
     * Adds new node to graph
     * @param string $text a text for node
     * @return int index
     */
    public function push_node($text) {
        $id = count($this->nodes);
        $this->nodes[] = $text;
        return $id;
    }

    /**
     * Adds new edge to graph
     * @param int $from a starting node index
     * @param int $to an ending node index
     */
    public function push_edge($from, $to) {
        $this->edges[] = array($from, $to);
    }

    /**
     * Converts everything to dot
     * @return string data in dot representation
     */
    public function to_dot() {
        $string = 'digraph G {' . PHP_EOL;
        foreach($this->nodes as $k => $v)
        {
            $string .=  '    node_' . $k . ' [ shape=box label="' . str_replace('"', '\\"', $v) . '" ]' . PHP_EOL;
        }
        foreach($this->edges as $edge)
        {
            $string .=  '    node_' . $edge[0] . ' -> node_' . $edge[1] . PHP_EOL;
        }
        $string .= '}';
        return $string;
    }

    /**
     * Строит данные по результатам
     * @param array|block_formal_langs_ast_node_base $node вершины
     * @return int id самой верхней вершины
     */
    public function build_tree($node) {
        if (is_array($node)) {
            foreach($node as $child) {
                $this->build_tree($child);
            }
            return 0;
        } else {
            if (is_a($node, 'block_formal_langs_ast_node_base')) {
                $text = 0;
                if (count($node->childs()) == 0 && method_exists($node, 'value')) {
                    $text = $node->value();
                } else {
                    $classname = get_class($node);
                    $text = $node->type();
                    if ($classname != 'block_formal_langs_ast_node_base' && $classname != 'block_formal_langs_token_base')
                    {
                        $text .= '(' . $classname . ')';
                    }
                }

                $myid = $this->push_node($text);
                if (count($node->childs())) {
                    foreach($node->childs() as $child) {
                        $nodeid = $this->build_tree($child);
                        $this->push_edge($myid, $nodeid);
                    }
                }
                return $myid;
            } else {
                return $this->push_node(var_export($node, true));
            }
        }
    }

    /**
     * A list of nodes texts, indexed as lists
     * @var array
     */
    protected  $nodes;
    /**
     * A list of edges, as array of node indexes
     * @var
     */
    protected $edges;
};

if (count($argv) > 1) {
    $lang = new block_formal_langs_language_cpp_parseable_language();
    $result = $lang->create_from_string($argv[1]);

    $data = new block_formal_langs_tree_dot_representation();
    $data->build_tree($result->syntaxtree);
    echo $data->to_dot();
}
