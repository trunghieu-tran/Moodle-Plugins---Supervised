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
 * Defines classes relating to explaining graph.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

/**
 * A node of explaining graph.
 */
class qtype_preg_explaining_graph_tool_node {

    public $shape  = 'ellipse';     // Shape of node on image.
    public $color  = 'black';       // Color of node on image.
    public $style  = 'solid';       // Style of node on image.
    public $owner  = null;          // Owner of node.
    public $label  = '';            // Data of node on image.
    public $id     = -1;            // Id of node.
    public $fillcolor   = 'white';  // Filling color of node on image.
    public $invert = false;         // Flag of inversion of node.
    public $ismarked = false;       // Flag of marking (for voids).
    public $type = self::TYPE_OTHER;// Type of node.
    public $borderoftemplate = null;

    // Possible types of node.
    const TYPE_POINT       = 'node_point';
    const TYPE_SIMPLE      = 'node_simple';
    const TYPE_ASSERT      = 'node_assert';
    const TYPE_BOUNDARY    = 'node_boundary';
    const TYPE_VOID        = 'node_void';
    const TYPE_OPTION      = 'node_option';
    const TYPE_TEMPLATE    = 'node_template';
    const TYPE_OTHER       = 'node_other';

    public function __construct($lbl, $shp, $clr, $ownr, $id, $stl = 'solid', $fll = 'white') {
        $this->label = $lbl;
        $this->shape = $shp;
        $this->color = $clr;
        $this->style = $stl;
        $this->fillcolor = $fll;
        $this->owner = $ownr;
        $this->id = $id;
    }

    /**
     * Returns node which is right neighbor of $this.
     * Searches recursively in subgraph $gr.
     * @param qtype_preg_explaining_graph_tool_subgraph $gr Graph in which searching will occurs.
     * @return qtype_preg_explaining_graph_tool_node Found node or special 'error' node.
     */
    public function find_neighbor_dst($gr) {
        // Look over links...
        foreach ($gr->links as $iter) {
            if ($iter->source === $this) {   // If source of link is $nd...
                return $iter->destination; // ...then we found what we were looking for.
            }
        }

        // If we found nothing, then do the same with subgraphs.
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_dst($iter);
            if ($result !== null) {    // If result is valid...
                return $result;         // ...then the right neighbor of $nd is child of current subgraph.
            }
        }

        // Overwise return an invalid node.
        $result = null;

        return $result;
    }

    /**
     * Returns node which is left neighbor of $this.
     * Searches recursively in subgraph $gr.
     * @param qtype_preg_explaining_graph_tool_subgraph $gr Graph in which searching will occurs.
     * @return qtype_preg_explaining_graph_tool_node Found node or special 'error' node.
     */
    public function find_neighbor_src($gr) {
        // Look over links...
        foreach ($gr->links as $iter) {
            if ($iter->destination === $this) {   // If destination of link is $nd...
                return $iter->source;             // ...then we found what we were looking for.
            }
        }

        // If we found nothing, then do the same with subgraphs.
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_src($iter);
            if ($result !== null) {        // If result is valid...
                return $result;             // ...then the left neighbor of $nd is child of current subgraph.
            }
        }

        // Overwise return an invalid node.
        $result = null;

        return $result;
    }
}

/**
 * A link of explaining graph.
 */
class qtype_preg_explaining_graph_tool_link {

    public $source = null;      // Source of link.
    public $destination = null; // Destination of link.
    public $label = '';         // Label of link on image.
    public $style = '';         // Visual style of link (for image).
    public $owner = null;       // Subgraph which has this link.
    public $tooltip = '';       // Tooltip for link.
    public $color = 'black';    // Color of arrow.
    public $ltail = null;       // Behavior of link's tail with clusters.
    public $lhead = null;       // Behavior of link's head with clusters.
    public $id = -1;            // Id of link.

    public function __construct($lbl, $src, $dst, $ownr = null, $stl = 'normal') {
        $this->label = $lbl;
        $this->source = $src;
        $this->destination = $dst;
        $this->style = $stl;
        $this->owner = $ownr;
    }

    /**
     * Integrates two labels of nodes or links.
     * @param string $lbl1 First label (left side).
     * @param string $lbl2 Second label (right side).
     * @return string Integrated label.
     */
    public static function compute_label($lbl1, $lbl2) {
        $empty = '';
        if ($lbl1 == $empty && $lbl2 == $empty) {
            return $empty;
        } else if ($lbl1 == $empty) {
            return $lbl2;
        } else if ($lbl2 == $empty) {
            return $lbl1;
        } else {
            return $lbl1 . '\n' . $lbl2;
        }
    }
}

/**
 * A subgraph of explaining graph.
 */
class qtype_preg_explaining_graph_tool_subgraph {

    public $label       = '';           // Label of subgraph on image.
    public $style       = 'solid';      // Style of subgraph on image.
    public $color       = 'black';      // Border color of subgraph.
    public $nodes       = array();      // Array of nodes in subgraph.
    public $links       = array();      // Array of links between nodes in subgraph.
    public $subgraphs   = array();      // Array of subgraphs in subgraph.
    public $entries     = array();      // Array if nodes "entries".
    public $exits       = array();      // Array of nodes "exits".
    public $id          = -1;           // Identifier of subgraph.
    public $bgcolor     = 'white';      // Background color of subgraph.
    public $node        = '';           // Special nodes options.
    public $edge        = '';           // Special edges options.
    public $isexact     = false;
    public $isselection = false;        // This flag shows
    public $tooltip     = '';           // Tooltip for subgraph.

    public function __construct($lbl, $id = -1) {
        $this->label = $lbl;
        $this->id = $id;
    }

    /**
     * Merges two subgraphs, where acceptor is $this.
     * @param qtype_preg_explaining_graph_tool_subgraph $acceptor - accumulated graph.
     */
    public function assume_subgraph($donor) {
        foreach ($donor->nodes as $node) {
            $node->owner = $this;
            $this->nodes[] = $node;
        }

        foreach ($donor->links as $link) {
            $link->owner = $this;
            $this->links[] = $link;
        }

        foreach ($donor->subgraphs as $subgraph) {
            $this->subgraphs[] = $subgraph;
        }
    }

    /**
     * Optimizes this subgraph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function optimize_graph($gmain) {
        $this->process_simple_characters($gmain);
        $this->process_voids($gmain);
        $this->process_asserts($gmain);
        foreach ($this->subgraphs as $subgraph) {
            $subgraph->optimize_graph($gmain);
        }
    }

    /**
     * Second part of optimization - processing sequences of simple characters in graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function process_simple_characters($gmain) {
        for ($i = 0; $i < count($this->nodes); $i++) {
            $neighbor = null;   // No neighbor yet.

            $tmpdnode = $this->nodes[$i];  // Remember current node.

            // If it is simple node with text...
            if ($tmpdnode->type == qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE) {
                // Find a right neighbor of node.
                $neighbor = $tmpdnode->find_neighbor_dst($gmain);
                // If neighbor is simple node with text too and it's a child of the same subgraph,
                // then we need to join this two nodes.
                if ($neighbor !== null and $neighbor->type == qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE && $neighbor->owner === $this) {

                    $ids_this = explode('_', $tmpdnode->id);
                    $ids_neighbor = explode('_', $neighbor->id);
                    $ids_new = $ids_this[0] . '_' . $ids_this[1] . '_' . ($ids_neighbor[2]);

                    // Create the new joined node.
                    $tmp = new qtype_preg_explaining_graph_tool_node(
                                array($tmpdnode->label[0] . $neighbor->label[0]),
                                $neighbor->shape,
                                $neighbor->color,
                                $this, $ids_new,
                                $tmpdnode->style,
                                $tmpdnode->fillcolor
                            );

                    $tmp->type = qtype_preg_explaining_graph_tool_node::TYPE_SIMPLE;

                    // Find a link between left neighbor and current node, then change destination to new node.
                    $tmpneighbor = $tmpdnode->find_neighbor_src($gmain);
                    $tmpneighbor = $gmain->find_link($tmpneighbor, $tmpdnode);
                    $tmpneighbor->destination = $tmp;

                    // Find a link between neighbor and his right neighbor, then change source to new node.
                    $tmpneighbor = $neighbor->find_neighbor_dst($gmain);
                    $has_next = false;
                    if ($tmpneighbor !== null) {
                        $tmpneighbor = $gmain->find_link($neighbor, $tmpneighbor);
                        $tmpneighbor->source = $tmp;
                        $has_next = true;
                    }

                    // Destroy old link.
                    $tmpneighborlink = $gmain->find_link($tmpdnode, $neighbor);
                    if ($has_next && $tmpneighborlink->lhead !== null)
                        $this->copy_lattrs_for_right($tmpneighborlink, $tmpneighbor);
                    unset($tmpneighborlink->owner->links[array_search($tmpneighborlink, $tmpneighborlink->owner->links)]);
                    $tmpneighborlink->owner->links = array_values($tmpneighborlink->owner->links);

                    // Destroy old node.
                    unset($this->nodes[array_search($neighbor, $this->nodes)]);
                    $this->nodes = array_values($this->nodes);

                    $this->nodes[array_search($tmpdnode, $this->nodes)] = $tmp;

                    $i = -1; // Start this loop again.
                }
            }
        }
    }

    /**
     * Third part of optimization - processing sequences of asserts in graph and something more.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function process_asserts($gmain) {
        // Lets find an assert.
        foreach ($this->nodes as $iter) {
            $neighbor = null;

            $tmpdnode = $iter; // Let copy current node.

            // If this node is assert...
            if ($iter->type == qtype_preg_explaining_graph_tool_node::TYPE_ASSERT) {
                // Find its neighbors (left and right).
                $rightneighbor = $tmpdnode->find_neighbor_dst($gmain);
                $leftneighbor = $tmpdnode->find_neighbor_src($gmain);

                // First case - both neighbors are in same subgraph.
                if ($rightneighbor !== null and $rightneighbor->owner === $leftneighbor->owner && $leftneighbor->owner === $this) {
                    // Find labels of links between neighbors and assert.
                    $tmplabel1 = $gmain->find_link($leftneighbor, $tmpdnode)->label;
                    $tmplabel2 = $gmain->find_link($tmpdnode, $rightneighbor)->label;

                    // Create a new link between neighbors with new label.
                    $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                        qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $tmplabel2), $leftneighbor, $rightneighbor, $this);
                    // Second case - neighbors are not in the same subgraphs, but right neighbor is in same as assert.
                } else if ($rightneighbor !== null and $rightneighbor->owner !== $leftneighbor->owner && $leftneighbor->owner !== $this && $rightneighbor->owner === $this) {
                    // If right neighbor is not just a point...
                    if ($rightneighbor->type != qtype_preg_explaining_graph_tool_node::TYPE_POINT) {
                        // Create a new point-node.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        // Link left neighbor with it.
                        $oldlink = $gmain->find_link($leftneighbor, $tmpdnode);
                        $newlink = clone $oldlink;
                        $newlink->id = -1;
                        $newlink->destination = end($this->nodes);
                        $oldlink->owner->links[] = $newlink;

                        $oldlink = $gmain->find_link($tmpdnode, $rightneighbor);
                        $newlink = clone $oldlink;
                        $newlink->source = end($this->nodes);
                        $newlink->label = $tmpdnode->label[0];
                        $oldlink->owner->links[] = $newlink;
                    } else {
                        $oldlink = $gmain->find_link($tmpdnode, $rightneighbor);
                        $newlink = clone $oldlink;
                        $tmplabel2 = $oldlink->label;
                        $newlink->label = qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2);
                        $newlink->source = $leftneighbor;
                        $oldlink->owner->links[] = $newlink;
                    }
                    // Third case - neighbors are not in the same subgraphs, but left neighbor is in same as assert.
                } else if ($rightneighbor !== null and $rightneighbor->owner !== $leftneighbor->owner && $leftneighbor->owner === $this && $rightneighbor->owner !== $this) {
                    // If right neighbor is not just a point...
                    if ($leftneighbor->type != qtype_preg_explaining_graph_tool_node::TYPE_POINT) {
                        // Create a new point-node.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                        $oldlink = $gmain->find_link($tmpdnode, $rightneighbor);
                        $newlink = clone $oldlink;
                        $newlink->id = -1;
                        $newlink->source = end($this->nodes);
                        $oldlink->owner->links[] = $newlink;

                        $oldlink = $gmain->find_link($leftneighbor, $tmpdnode);
                        $newlink = clone $oldlink;
                        $tmplabel2 = $oldlink->label;
                        $newlink->label = qtype_preg_explaining_graph_tool_link::compute_label($tmplabel2, $tmpdnode->label[0]);
                        $newlink->destination = end($this->nodes);
                        $oldlink->owner->links[] = $newlink;
                    } else {
                        $oldlink = $gmain->find_link($leftneighbor, $tmpdnode);
                        $newlink = clone $oldlink;
                        $tmplabel2 = $oldlink->label;
                        $newlink->label = qtype_preg_explaining_graph_tool_link::compute_label($tmplabel2, $tmpdnode->label[0]);
                        $newlink->destination = $rightneighbor;
                        $oldlink->owner->links[] = $newlink;
                    }
                } else { // Fourth case - neighbors are not in the same subgraphs and no one in current subgraph.
                    // If right neighbor is existing...
                    if ($rightneighbor !== null) {
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $rightborder = end($this->nodes);   // Now right neighbor of assert is point.
                        $oldlink = $gmain->find_link($tmpdnode, $rightneighbor);
                        $rightlink = clone $oldlink;
                        $rightlink->id = -1;
                        $rightlink->source = $rightborder;
                        $oldlink->owner->links[] = $rightlink;

                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $leftborder = end($this->nodes);    // Now left neighbor of assert is point.
                        $oldlink = $gmain->find_link($leftneighbor, $tmpdnode);
                        $leftlink = clone $oldlink;
                        $leftlink->id = -1;
                        $leftlink->destination = $leftborder;
                        $oldlink->owner->links[] = $leftlink;

                        // Link left and right neighbors with corresponding label.
                        $this->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder, $this);
                    } else {
                        // Right neighbor is not existing, so we just replace it with point-node.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $rightborder = end($this->nodes);

                        // If left neighbor isn't point-node then create it and link them.
                        if ($leftneighbor->type != qtype_preg_explaining_graph_tool_node::TYPE_POINT) {
                            $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                            $oldlink = $gmain->find_link($leftneighbor, $tmpdnode);
                            $newlink = clone $oldlink;
                            $newlink->id = -1;
                            $newlink->destination = end($this->nodes);
                            $oldlink->owner->links[] = $newlink;
                            $leftborder = end($this->nodes);    // Now left neighbor of assert is point.
                        } else {
                            $leftborder = $leftneighbor;
                        }

                        // Link left and right neighbors with corresponding label.
                        $this->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder, $this);
                    }
                }

                // Set an id and a tooltip for new link within destroyed node.
                $this->links[count($this->links)-1]->tooltip = $this->links[count($this->links)-1]->label;
                $this->links[count($this->links)-1]->id = $tmpdnode->id;

                // Destroy links between nighbors and assert.
                $tmplink = $gmain->find_link($leftneighbor, $tmpdnode);
                $this->copy_lattrs_for_left($tmplink, $this->links[count($this->links)-1]);
                unset($tmplink->owner->links[array_search($tmplink, $tmplink->owner->links)]);
                $tmplink->owner->links = array_values($tmplink->owner->links);
                // If right neighbor isn't existing then there is no to destroy.
                if ($rightneighbor !== null) {
                    $tmplink = $gmain->find_link($tmpdnode, $rightneighbor);
                    $this->copy_lattrs_for_right($tmplink, $this->links[count($this->links)-1]);
                    unset($tmplink->owner->links[array_search($tmplink, $tmplink->owner->links)]);
                    $tmplink->owner->links = array_values($tmplink->owner->links);
                }

                // Destroy assert.
                unset($this->nodes[array_search($tmpdnode, $this->nodes)]);
                $this->nodes = array_values($this->nodes);

                reset($this->nodes); // Start loop again.
            }
        }
    }

    /**
     * Fourth part of optimization - processing sequences of voids in graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Processed graph.
     */
    public function process_voids($gmain) {
        foreach ($this->nodes as $iter) {
            // If this node is void...
            if ($iter->type == qtype_preg_explaining_graph_tool_node::TYPE_VOID
            || $iter->type == qtype_preg_explaining_graph_tool_node::TYPE_OPTION) {
                // Find neighbors of void.
                $neighborl = $iter->find_neighbor_src($gmain);
                $neighborr = $iter->find_neighbor_dst($gmain);

                if ($iter->type != qtype_preg_explaining_graph_tool_node::TYPE_OPTION) {
                    if ($this->isselection != true || $iter->ismarked) {
                        // Find a link between left neighbor and void.
                        $tmplink_l = $gmain->find_link($neighborl, $iter);
                        $tmplink_l->destination = $neighborr;    // Set a new destination.
                        $tmplink_l->tooltip = "Void";
                        $tmplink_l->id = $iter->id;

                        if ($neighborr !== null) {
                            // Find a link between void and right neighbor and destroy it.
                            $tmplink_r = $gmain->find_link($iter, $neighborr);
                            $this->copy_lattrs_for_right($tmplink_r, $tmplink_l);
                            unset($tmplink_r->owner->links[array_search($tmplink_r, $tmplink_r->owner->links)]);
                            $tmplink_r->owner->links = array_values($tmplink_r->owner->links);
                        } else {
                            $point = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                            $this->nodes[] = $point;
                            $tmplink_l->destination = $point;    // Set a new destination.
                        }
                    } elseif (count($this->nodes) === 1) {
                        $pointl = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $pointr = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $this->nodes[] = $pointl;
                        $this->nodes[] = $pointr;

                        // Find a link between left neighbor and void.
                        $tmplink_l = $gmain->find_link($neighborl, $iter);
                        $tmplink_l->destination = $pointl;    // Set a new destination.

                        // Find a link between void and right neighbor.
                        $tmplink_r = $gmain->find_link($iter, $neighborr);
                        $tmplink_r->source = $pointr;    // Set a new source.

                        $this->links[] = new qtype_preg_explaining_graph_tool_link('', $pointl, $pointr, $this);
                        $this->links[count($this->links)-1]->tooltip = "Void";
                        $this->links[count($this->links)-1]->id = $iter->id;
                    } else {
                        // Find a link between left neighbor and void.
                        $tmplink_l = $gmain->find_link($neighborl, $iter);
                        $tmplink_l->destination = $neighborr;    // Set a new destination.
                        $tmplink_l->tooltip = "Void";
                        $tmplink_l->id = $iter->id;

                        if ($neighborr !== null) {
                            // Find a link between void and right neighbor and destroy it.
                            $tmplink_r = $gmain->find_link($iter, $neighborr);
                            $this->copy_lattrs_for_right($tmplink_r, $tmplink_l);
                            unset($tmplink_r->owner->links[array_search($tmplink_r, $tmplink_r->owner->links)]);
                            $tmplink_r->owner->links = array_values($tmplink_r->owner->links);
                        } else {
                            $point = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                            $this->nodes[] = $point;
                            $tmplink_l->destination = $point;    // Set a new destination.
                        }
                    }
                } else {
                    // Find a link between left neighbor and void.
                    $tmpneighbol = $gmain->find_link($neighborl, $iter);
                    $tmpneighbol->destination = $neighborr;    // Set a new destination.
                    $tmpneighbol->tooltip = "Void";
                    $tmpneighbol->id = $iter->id;

                    $tmpneighbor = $gmain->find_link($iter, $neighborr);
                    $this->copy_lattrs_for_right($tmpneighbor, $tmpneighbol);
                    if ($tmpneighbor->owner !== $this && !$this->is_parent_for($tmpneighbor->owner)) {
                        unset($tmpneighbol->owner->links[array_search($tmpneighbol, $tmpneighbor->owner->links)]);
                        $tmpneighbol->owner->links = array_values($tmpneighbol->owner->links);
                        $tmpneighbol->owner = $tmpneighbor->owner;
                        $tmpneighbor->owner->links[] = $tmpneighbol;
                    }
                    unset($tmpneighbor->owner->links[array_search($tmpneighbor, $tmpneighbor->owner->links)]);
                    $tmpneighbor->owner->links = array_values($tmpneighbor->owner->links);
                }

                // Destroy void-node.
                unset($this->nodes[array_search($iter, $this->nodes)]);
                $this->nodes = array_values($this->nodes);

                // Start loop again.
                reset($this->nodes);
            }
        }
    }

    /**
     * Checks if this is parent of $child (recursive for children of children).
     */
    private function is_parent_for($child) {
        foreach ($this->subgraphs as $iter) {
            if ($iter === $child) {
                return true;
            }
        }
        foreach ($this->subgraphs as $iter) {
            if ($iter->is_parent_for($child, false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns link with source = $src and destination = $dst.
     * Searches recursively in $this.
     * @param qtype_preg_explaining_graph_tool_node $src Source of link.
     * @param qtype_preg_explaining_graph_tool_node $dst Destination of link.
     * @return qtype_preg_explaining_graph_tool_link Found link.
     */
    private function find_link($src, $dst) {
        foreach ($this->links as $iter) {
            if ($iter->destination === $dst && $iter->source === $src) {
                return $iter;
            }
        }
        foreach ($this->subgraphs as $iter) {
            $result = $iter->find_link($src, $dst);
            if (!is_null($result)) {
                return $result;
            }
        }
        return null;
    }

    /**
     * Creates text with dot instructions.
     * @return string Dot instructions of this subgraph.
     */
    public function create_dot() {
        $this->regenerate_id();
        $instr = "digraph \"explaining graph\" {\n" .
                  "tooltip=\"" . $this->tooltip ."\";\n" .
                  "id=\"explaining_graph\";\n" .
                  "compound=true;\n" .
                  "rankdir = LR;\n" . ($this->isexact ? 'graph [bgcolor=lightgray];' : '') . "\n";

        foreach ($this->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' . $iter->id . '" [shape=' . $iter->shape . ', color=' . $iter->color . ',id="graphid_' . $iter->id .
                    '", label=' . $this->compute_html($iter->label, $iter->invert) . ', fillcolor=' . $iter->fillcolor .
                    ',tooltip="character class"' ."];\n";
            } else {
                $instr .= '"nd' . $iter->id . '" [shape=' . $iter->shape . ', ' . 'id="graphid_' . $iter->id .
                    '", color=' . $iter->color . ', ' . 'style=' . $iter->style . ', ' .
                    'label="' . str_replace(chr(10), '', qtype_preg_authoring_tool::string_to_html($iter->label[0])) . '"' .
                    ', fillcolor=' . $iter->fillcolor . ',tooltip="'.str_replace(chr(10), '', qtype_preg_authoring_tool::string_to_html($iter->label[0])).'"' ."];\n";
            }
        }

        foreach ($this->subgraphs as $iter) {
            $this->process_subgraph($iter, $instr);
        }

        foreach ($this->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd' . $iter->destination->id . '"';
            $instr .= "[";
            if ($iter->id !== -1) $instr .= "id=\"graphid_" . $iter->id . "\", ";
            $instr .= "label=\"" . $iter->label . "\", ";
            $instr .= "arrowhead=\"" . $iter->style . "\", ";
            $instr .= "color=\"" . $iter->color . "\", ";
            $instr .= $iter->lhead !== null ? 'lhead="cluster_' . $iter->lhead->id . '", ' : '';
            $instr .= $iter->ltail !== null ? 'ltail="cluster_' . $iter->ltail->id . '", ' : '';
            if ($iter->id !== -1) $instr .= "tooltip=\"" . $iter->tooltip . "\", ";
            $instr .= "];\n";
        }

        $instr .= "}\n";

        return $instr;
    }

    /**
     * Creates html of character class for dot instructions.
     * @param string $lbl Label of node in graph.
     * @param bool $invert Flag of inverted charclass.
     * @return string Html of character class.
     */
    private function compute_html($lbl, $invert) {
        $elements = array();
        $result = '';
        if (count($lbl)) {
            if (count($lbl) == 1) {
                if ($invert || core_text::strlen($lbl[0]) != 1) {
                    $elements[] = $lbl[0];
                } else {
                    return '"' . $lbl[0] . '"';
                }
            } else {
                for ($i = 0; $i < count($lbl); ++$i) {
                        $elements[] = $lbl[$i];
                }
            }

            $result .= '<<TABLE BORDER="0" CELLBORDER="1" CELLSPACING="0" CELLPADDING="4"><TR><TD COLSPAN="';
            $result .= count($elements);
            if ($invert) {
                $result .= '"><font face="Arial">' . get_string('explain_any_char_except', 'qtype_preg') . '</font></TD></TR><TR>';
            } else {
                $result .= '"><font face="Arial">' . get_string('explain_any_char', 'qtype_preg') . '</font></TD></TR><TR>';
            }

            for ($i = 0; $i != count($elements); ++$i) {
                if ($elements[$i][0] == chr(10)) {
                    $result .= '<TD>' . substr($elements[$i], 1) . '</TD>';
                } else {
                    $elements[$i] = $elements[$i];
                    $result .= '<TD>' . $elements[$i] . '</TD>';
                }
            }

            $result .= '</TR></TABLE>>';
        }

        return $result;
    }

    /**
     * Creates dot instructions for subgraph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gr Subgraph.
     * @param string $instr Current dot instructions.
     */
    private function process_subgraph($gr, &$instr) {
        $instr .= 'subgraph "cluster_' . $gr->id . '" {';
        $instr .= 'style=' . $gr->style . ';';
        $instr .= 'color=' . $gr->color . ';';
        $instr .= 'bgcolor=' . $gr->bgcolor . ';';
        $instr .= 'label="' . qtype_preg_authoring_tool::string_to_html($gr->label) . '";';
        $instr .= 'id="graphid_' . $gr->id . '";';
        $instr .= 'tooltip="' . $gr->tooltip . '";';
        $instr .= $gr->edge;
        $instr .= $gr->node;

        foreach ($gr->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' .$iter->id . '" [shape=' . $iter->shape . ', color=' . $iter->color . ',id="graphid_' . $iter->id .
                    '", label=' . $this->compute_html($iter->label, $iter->invert) . ', fillcolor=' . $iter->fillcolor .',tooltip="character class"' ."];\n";
            } else {
                $instr .= '"nd' . $iter->id . '" [shape=' . $iter->shape . ', ' . 'id="graphid_' . $iter->id .
                    '",color=' . $iter->color . ', ' . 'style=' . $iter->style . ', ' .
                    'label="' . str_replace(chr(10), '', qtype_preg_authoring_tool::string_to_html($iter->label[0])) . '"' .
                    ', fillcolor=' . $iter->fillcolor . ',tooltip="'.str_replace(chr(10), '', qtype_preg_authoring_tool::string_to_html($iter->label[0])).'"' ."];\n";
            }
        }

        foreach ($gr->subgraphs as $iter) {
            $this->process_subgraph($iter, $instr);
        }

        foreach ($gr->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd' . $iter->destination->id . '"';
            $instr .= "[";
            if ($iter->id !== -1) $instr .= "id=\"graphid_" . $iter->id . "\", ";
            $instr .= "label=\"" . $iter->label . "\", ";
            $instr .= "arrowhead=\"" . $iter->style . "\", ";
            $instr .= "color=\"" . $iter->color . "\", ";
            $instr .= $iter->lhead !== null ? 'lhead="cluster_' . $iter->lhead->id . '", ' : '';
            $instr .= $iter->ltail !== null ? 'ltail="cluster_' . $iter->ltail->id . '", ' : '';
            if ($iter->id !== -1) $instr .= "tooltip=\"" . $iter->tooltip . "\", ";
            $instr .= "]\n";
        }

        $instr .= "}\n";
    }

    /**
     * Finds a maximum id of node in the graph.
     * @return int A maximum id.
     */
    private function find_max_id() {
        $maxid = -1;
        foreach ($this->nodes as $node) {
            $id = $node->id;
            if (is_string($id)) {
                $temp = explode(',', $id);
                $id = (int)$temp[0];
            }
            $maxid = max($maxid, $id);
        }

        foreach ($this->links as $link) {
            $id = $link->id;
            if (is_string($id)) {
                $temp = explode(',', $id);
                $id = (int)$temp[0];
            }
            $maxid = max($maxid, $id);
        }

        foreach ($this->subgraphs as $subgraph) {
            $tmpid = $subgraph->find_max_id();
            $maxid = max($maxid, $tmpid);
        }

        return $maxid;
    }

    /**
     * Fix all identifiers with value -1.
     * @param int $maxid Maximum id of node in graph.
     * @return int A new maximum id.
     */
    private function regenerate_id($maxid = -1) {
        $maxid = $maxid == -1 ? $this->find_max_id() : $maxid;

        foreach ($this->nodes as $node) {
            if (!is_string($node->id)) {
                if ($node->id == -1) {
                    $node->id = ++$maxid;
                }
            }
        }

        foreach ($this->links as $link) {
            if (!is_string($link->id)) {
                if ($link->id == -1) {
                    $link->id = ++$maxid;
                }
            }
        }

        foreach ($this->subgraphs as $subgraph) {
            if (!is_string($subgraph->id)) {
                if ($subgraph->id == -1) {
                    $subgraph->id = ++$maxid;
                }
            }
            $maxid = $subgraph->regenerate_id($maxid);
        }

        return $maxid;
    }

    /**
     * If $old is right.
     * @param $old
     * @param $new
     */
    private function copy_lattrs_for_right($old, $new) {
        $old->lhead = $new->lhead;
    }

    /**
     * If $old is left.
     * @param $old
     * @param $new
     */
    private function copy_lattrs_for_left($old, $new) {
        $old->ltail = $new->ltail;
    }
}

