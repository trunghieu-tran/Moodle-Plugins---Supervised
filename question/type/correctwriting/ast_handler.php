<?php
// This file is part of Correct Writing question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Correct Writing question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Correct Writing is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains definition of ast handler, which can go through items of tree
 *
 * @package    qtype_correctwriting
 * @subpackage hints
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

abstract class qtype_correctwriting_ast_handler  {
    /**
     * Visits array of parent nodes
     * @param array $nodes
     */
    public function visit_array($nodes) {
        if (count($nodes)) {
            foreach($nodes as $node) {
                $this->visit($node);
            }
        }
    }

    /**
     * Visits node, performing needed operations
     * @param block_formal_langs_ast_node_base $node
     * @return mixed
     */
    public function visit($node) {
        $children = $this->children($node);
        if (count($children)) {
            foreach($children as $child) {
                $this->visit($child);
            }
        }
    }

    /**
     * Returns children data
     * @param block_formal_langs_ast_node_base $node node data
     * @return mixed
     */
    public function children($node) {
        return $node->childs();
    }
}


class qtype_correctwriting_marked_tree_builder extends qtype_correctwriting_ast_handler {
    /**
     * A tree  as array of nodes
     * @var array
     */
    public $tree = array();
    /**
     * An array of arrays with keys as names of marked items and groups as numbers
     * @var array
     */
    public $markers = array();

    /**
     * Data for parent node
     * @var block_formal_langs_ast_node_base
     */
    public $parentnode;


    /**
     * Visits array of parent nodes
     * @param array $nodes
     */
    public function visit_array($nodes) {
        if (count($nodes)) {
            foreach($nodes as $node) {
                $this->tree[] = $this->visit($node);
            }
        }
    }

    /**
     * Visits node, performing needed operations
     * @param block_formal_langs_ast_node_base $node
     * @return mixed
     */
    public function visit($node) {
        $result = new stdClass();
        $result->item = $node;
        $result->marker = $this->marker_for_node($node);
        $result->children = array();
        $children = $this->children($node);
        if (count($children)) {
            foreach($children as $child) {
                $result->children[] = $this->visit($child);
            }
        }
        return $result;
    }


    /**
     * Returns marker for node
     * @var block_formal_langs_ast_node_base $node node data
     * @return array|null data
     */
    protected function marker_for_node($node) {
        $number = $node->number();
        $result = null;
        foreach($this->markers as $firstpart => $group) {
            foreach($group as $key => $list) {
                if (in_array($number, $list)) {
                    $result = array($firstpart, $key);
                }
            }
        }
        return $result;
    }
}

class qtype_correctwriting_marked_tree_remarker extends qtype_correctwriting_ast_handler {

    public $changed = false;

    public static function mark_tree($nodes) {
        $changed = true;
        while($changed) {
            $r = new qtype_correctwriting_marked_tree_remarker();
            $r->changed = false;
            $r->visit_array($nodes);
            $changed = $r->changed;
        }
    }
    /**
     * Visits node, performing needed operations
     * @param stdClass $node
     * @return mixed
     */
    public function visit($node) {
        if ($node->marker !== null) {
            return;
        }
        $children = $this->children($node);
        if (count($children)) {
            $marker = null;
            $first = true;
            $allmarkersequal = true;
            foreach($children as $child) {
                if ($first) {
                    $marker = $child->marker;
                    $first = false;
                } else {
                    $allmarkersequal = $allmarkersequal && ($child->marker == $marker && $marker !== null);
                }
            }

            if ($allmarkersequal && $marker != NULL) {
                $node->marker = $marker;
                $this->changed = true;
            } else {
                parent::visit($node);
            }
        }
    }
    /**
     * Returns children data
     * @param stdClass $node node data
     * @return mixed
     */
    public function children($node) {
        return $node->children;
    }
}

class qtype_correctwriting_find_top_marked_nodes extends qtype_correctwriting_ast_handler {

    public $result = array();


    /**
     * Visits node, performing needed operations
     * @param stdClass $node
     * @return mixed
     */
    public function visit($node) {
        if ($node->marker === null) {
            parent::visit($node);
        } else {
            $this->result[] = $node;
        }
    }


    /**
     * Returns children data
     * @param stdClass $node node data
     * @return mixed
     */
    public function children($node) {
        return $node->children;
    }
}