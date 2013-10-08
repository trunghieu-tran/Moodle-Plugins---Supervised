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
 * Defines classes relates with graph.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

/**
 * A node of explaining graph.
 */
class qtype_preg_explaining_graph_tool_node {

    public $shape  = 'ellipse';  // Shape of node on image.
    public $color  = 'black';    // Color of node on image.
    public $owner  = null;       // Owner of node.
    public $label  = '';         // Data of node on image.
    public $id     = -1;         // Id of node.
    public $fill   = '';         // Filling of node on image.
    public $invert = false;      // Flag of inversion of node.

    /**
     * Counts a number of links in which node is. Searching executes in owner of node.
     * @param bool $type Flag parameter: true - node is destination, false - node is source.
     * @return int A number of links.
     */
    public function links_count($type) {
        $cx = 0; // Links counter.
        foreach ($this->owner->links as $link) {
            if ($type) {
                if ($link->destination === $this) {
                    ++$cx;
                }
            } else {
                if ($link->source === $this) {
                    ++$cx;
                }
            }
        }
        return $cx;
    }

    /**
     * Returns node which is right neighbor of $this.
     * Searches recursively in subgraph $gr.
     * @param qtype_preg_explaining_graph_tool_subgraph $gr Graph in which searching will occurs.
     * @return qtype_preg_explaining_graph_tool_node Found node or special 'error' node.
     */
    public function &find_neighbor_dst(&$gr) {
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
    public function &find_neighbor_src(&$gr) {
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

    /**
     * Searches links in which node is as any instance.
     * @return array Links in which this node is.
     */
    public function links() {
        $result = array();
        foreach ($this->owner->links as $link) {
            if ($link->destination == $this || $link->source == $this) {
                $result[] = $link;
            }
        }
        return $result;
    }

    public function __construct($lbl, $shp, $clr, $ownr, $id, $fll = '') {
        $this->label = $lbl;
        $this->shape = $shp;
        $this->color = $clr;
        $this->fill = $fll;
        $this->owner = $ownr;
        $this->id = $id;
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
    public $nodes       = array();      // Array of nodes in subgraph.
    public $links       = array();      // Array of links between nodes in subgraph.
    public $subgraphs   = array();      // Array of subgraphs in subgraph.
    public $entries     = array();      // Array if nodes "entries".
    public $exits       = array();      // Array of nodes "exits".
    public $id          = -1;           // Identifier of subgraph.
    public $isexact     = false;

    public function __construct($lbl, $stl, $id = -1) {
        $this->label = $lbl;
        $this->style = $stl;
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
     * @param qtype_preg_explaining_graph_tool_subgraph $parent Processed graph of $this.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function optimize_graph(&$parent, &$gmain) {

        $this->process_simple_characters($gmain);

        $this->process_asserts($parent, $gmain);

        $this->process_voids($gmain);

        foreach ($this->subgraphs as $subgraph) {
            $subgraph->optimize_graph($this, $gmain);
        }
    }

    /**
     * Second part of optimization - processing sequences of simple characters in graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function process_simple_characters(&$gmain) {
        for ($i = 0; $i < count($this->nodes); $i++) {
            $neighbor = null;   // No neighbor yet.

            $tmpdnode = $this->nodes[$i];  // Remember current node.

            // If it is simple node with text...
            if ($tmpdnode->color == 'black' && $tmpdnode->shape == 'ellipse') {
                // Find a right neighbor of node.
                $neighbor = $tmpdnode->find_neighbor_dst($gmain);
                // If neighbor is simple node with text too and it's a child of the same subgraph AND it has the same register attribute,
                // then we need to join this two nodes.
                if ($neighbor !== null and $neighbor->color == 'black' && $neighbor->shape == 'ellipse' && $neighbor->owner === $this && $neighbor->fill == $tmpdnode->fill) {
                    // Create the new joined node.
                    $tmp = new qtype_preg_explaining_graph_tool_node(
                                array($tmpdnode->label[0] . $neighbor->label[0]),
                                $neighbor->shape,
                                $neighbor->color,
                                $this, $tmpdnode->id,
                                $tmpdnode->fill
                            );

                    // Find a link between left neighbor and current node, then change destination to new node.
                    $tmpneighbor = $tmpdnode->find_neighbor_src($gmain);
                    $tmpneighbor = $gmain->find_link($tmpneighbor, $tmpdnode);
                    $tmpneighbor->destination = $tmp;

                    // Find a link between neighbor and his right neighbor, then change source to new node.
                    $tmpneighbor = $neighbor->find_neighbor_dst($gmain);
                    if ($tmpneighbor !== null) {
                        $tmpneighbor = $gmain->find_link($neighbor, $tmpneighbor);
                        $tmpneighbor->source = $tmp;
                    }

                    // Destroy old link.
                    $tmpneighbor = $gmain->find_link($tmpdnode, $neighbor);
                    unset($tmpneighbor->owner->links[array_search($tmpneighbor, $tmpneighbor->owner->links)]);
                    $tmpneighbor->owner->links = array_values($tmpneighbor->owner->links);

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
     * @param qtype_preg_explaining_graph_tool_subgraph $parent Processed graph of $this.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function process_asserts(&$parent, &$gmain) {
        // Lets find an assert.
        foreach ($this->nodes as $iter) {
            $neighbor = null;

            $tmpdnode = $iter; // Let copy current node.

            $tmplabel1;
            $tmplabel2;

            // Assert should has a red color.
            if ($iter->color == 'red') {
                // Find its neighbors (left and right).
                $neighborr = $tmpdnode->find_neighbor_dst($gmain);
                $neighborl = $tmpdnode->find_neighbor_src($gmain);

                // First case - both neighbors are in same subgraph.
                if ($neighborr !== null and $neighborr->owner === $neighborl->owner && $neighborl->owner === $this) {
                    // Find labels of links between neighbors and assert.
                    $tmplabel1 = $gmain->find_link($neighborl, $tmpdnode)->label;
                    $tmplabel2 = $gmain->find_link($tmpdnode, $neighborr)->label;

                    // Create a new link between neighbors with new label.
                    $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                        qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $tmplabel2), $neighborl, $neighborr, $this);
                    // Second case - neighbors are not in the same subgraphs, but right neighbor is in same as assert.
                } else if ($neighborr !== null and $neighborr->owner !== $neighborl->owner && $neighborl->owner !== $this && $neighborr->owner === $this) {
                    // Find a label of link between assert and right neighbor.
                    $tmplabel2 = $gmain->find_link($tmpdnode, $neighborr)->label;

                    // If current subgraph is parent of left neighbor's owner...
                    if ($this->is_child($neighborl->owner)) {
                        // If left neighbor is just a point...
                        if ($neighborl->shape != 'point') {
                            // Create a new point-node.
                            $neighborl->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborl->owner, -1);

                            // Link left neighbor with it.
                            $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link(
                                                                '',
                                                                $neighborl,
                                                                $neighborl->owner->nodes[count($neighborl->owner->nodes) - 1],
                                                                $neighborl->owner
                                                            );

                            // Create new link between point-node and right neighbor.
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                                $tmpdnode->label[0], $tmplabel2), $neighborl->owner->nodes[count($neighborl->owner->nodes) - 1], $neighborr, $this);
                        } else {
                            // Create new link between left neighbor and right neighbor.
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                    qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2),
                                                    $neighborl,
                                                    $neighborr,
                                                    $this
                                                );
                        }
                    } else {
                        // Create a new point-node in current subgraph.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                        // Link left neighbor with it.
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, $this->nodes[count($this->nodes) - 1], $parent);

                        // Link it with right neighbor.
                        $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                            $tmpdnode->label[0], $tmplabel2), $this->nodes[count($this->nodes) - 1], $neighborr, $this);
                    }
                    // Third case - neighbors are not in the same subgraphs, but left neighbor is in same as assert.
                } else if ($neighborr !== null and $neighborr->owner !== $neighborl->owner && $neighborl->owner === $this && $neighborr->owner !== $this) {
                    // Find a label of link between left neighbor and assert.
                    $tmplabel1 = $gmain->find_link($neighborl, $tmpdnode)->label;
                    // If current subgraph is parent of right neighbor's owner...
                    if ($this->is_child($neighborr->owner)) {
                        // If right neighbor is just a point...
                        if ($neighborr->shape != 'point') {
                            // Create a new point-node.
                            $neighborr->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborr->owner, -1);

                            // Link it with right neighbor.
                            $neighborr->owner->links[] = new qtype_preg_explaining_graph_tool_link(
                                                                '',
                                                                $neighborr->owner->nodes[count($neighborr->owner->nodes) - 1],
                                                                $neighborr,
                                                                $neighborr->owner
                                                            );

                            // Create new link between left neighbor and point-node.
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                                $tmplabel1, $tmpdnode->label[0]), $neighborl, $neighborr->owner->nodes[count($neighborr->owner->nodes) - 1], $this);
                        } else {
                            // Create new link between left neighbor and right neighbor.
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                    qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]),
                                                    $neighborl,
                                                    $neighborr,
                                                    $this
                                                );
                        }
                    } else {
                        // Create a new point-node in current subgraph.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                        // Link it with right neighbor.
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', end($this->nodes), $neighborr, $parent);

                        // Link right neighbor with it.
                        $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]),
                                                $neighborl,
                                                end($this->nodes),
                                                $this
                                            );
                    }
                } else { // Fourth case - neighbors are not in the same subgraphs and no one in current subgraph.
                    $leftborder = $neighborl;
                    $rightborder = $neighborr;

                    // If right neighbor is existing...
                    if ($neighborr !== null) {
                        // Get labels of links incidence to current assert-node.
                        $tmplabel2 = $gmain->find_link($tmpdnode, $neighborr)->label;
                        $tmplabel1 = $gmain->find_link($neighborl, $tmpdnode)->label;

                        // If owners of neighbors are children of current subgraph...
                        if ($this->is_child($neighborl->owner) && $this->is_child($neighborr->owner)) {
                            // If right neighbor isn't point...
                            if ($neighborr->shape != 'point') {
                                // Create point-node and link it with right neighbor.
                                $neighborr->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborr->owner, -1);

                                $neighborr->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborr->owner->nodes), $neighborr, $neighborr->owner);
                                $rightborder = end($neighborr->owner->nodes);   // Now right neighbor of assert is point.
                            }
                            // If left neighbor isn't point...
                            if ($neighborl->shape != 'point') {
                                // Create point-node and link it with left neighbor.
                                $neighborl->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborl->owner, -1);

                                $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, end($neighborl->owner->nodes), $neighborl->owner);
                                $leftborder = end($neighborl->owner->nodes);    // Now left neighbor of assert is point.
                            }

                            // Link left and right neighbors with corresponding label.
                            $this->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder, $this);
                        } else {
                            // If only subgraph of left neighbor is child.
                            if ($this->is_child($neighborl->owner)) {
                                // Create point-node and link it with left neighbor.
                                $neighborl->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborl->owner, -1);

                                $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, end($neighborl->owner->nodes), $neighborl->owner);
                                $leftborder = end($neighborl->owner->nodes);    // Now left neighbor of assert is point.

                                // Create point-node and link it with right neighbor.
                                $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                                $neighborr->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($this->nodes), $neighborr, $neighborr->owner);

                                // Link left and right neighbors with corresponding label.
                                $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                        qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]),
                                                        $leftborder,
                                                        end($this->nodes),
                                                        $this
                                                    );

                                // If only subgraph of right neighbor is child.
                            } else if ($this->is_child($neighborr->owner)) {
                                // Create point-node and link it with right neighbor.
                                $neighborr->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborr->owner, -1);

                                $neighborr->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborr->owner->nodes), $neighborr, $neighborr->owner);
                                $rightborder = end($neighborr->owner->nodes);   // Now right neighbor of assert is point.

                                // Create point-node and link it with left neighbor.
                                $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                                $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, end($this->nodes), $neighborl->owner);

                                // Link left and right neighbors with corresponding label.
                                $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                        qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2),
                                                        end($this->nodes),
                                                        $rightborder,
                                                        $this
                                                    );
                            } else {
                                // Create point-node and link it with right neighbor.
                                $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                                $neighborr->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $this->nodes[count($this->nodes) - 1], $neighborr, $neighborr->owner);
                                $rightborder = end($this->nodes);   // Now right neighbor of assert is point.

                                // Create point-node and link it with left neighbor.
                                $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                                $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, $this->nodes[count($this->nodes) - 1], $neighborl->owner);

                                // Link left and right neighbors with corresponding label.
                                $this->links[] = new qtype_preg_explaining_graph_tool_link(
                                                        qtype_preg_explaining_graph_tool_link::compute_label(
                                                            $tmplabel1,
                                                            qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2)
                                                        ),
                                                        $this->nodes[count($this->nodes) - 1],
                                                        $rightborder,
                                                        $this
                                                    );
                            }
                        }
                    } else {
                        // Right neighbor is not existing, so we just replace it with point-node.
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $rightborder = end($this->nodes);

                        // If left neighbor isn't point-node then create it and link them.
                        if ($neighborl->shape != 'point') {
                            $neighborl->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborl->owner, -1);

                            $neighborl->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborl, end($neighborl->owner->nodes), $neighborl->owner);
                            $leftborder = end($neighborl->owner->nodes);    // Now left neighbor of assert is point.
                        }

                        // Link left and right neighbors with corresponding label.
                        $this->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder, $this);
                    }
                }

                // Destroy links between nighbors and assert.
                $tmplink = $gmain->find_link($neighborl, $tmpdnode);
                unset($tmplink->owner->links[array_search($tmplink, $tmplink->owner->links)]);
                $tmplink->owner->links = array_values($tmplink->owner->links);
                // If right neighbor isn't existing then there is no to destroy.
                if ($neighborr !== null) {
                    $tmplink = $gmain->find_link($tmpdnode, $neighborr);
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
            // Void should has an orange color.
            if ($iter->color == 'orange') {
                // Find neighbors of void.
                $neighborl = $iter->find_neighbor_src($gmain);
                $neighborr = $iter->find_neighbor_dst($gmain);

                if ($iter->shape != 'box') {
                    if ($this->style != 'solid; color=darkgreen') {
                        // Find a link between left neighbor and void.
                        $tmpneighbor = $gmain->find_link($neighborl, $iter);
                        $tmpneighbor->destination = $neighborr;    // Set a new destination.

                        if ($neighborr !== null) {
                            // Find a link between void and right neighbor and destroy it.
                            $tmpneighbor = $gmain->find_link($iter, $neighborr);
                            unset($tmpneighbor->owner->links[array_search($tmpneighbor, $tmpneighbor->owner->links)]);
                            $tmpneighbor->owner->links = array_values($tmpneighbor->owner->links);
                        } else {
                            $point = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                            $this->nodes[] = $point;
                            $tmpneighbor->destination = $point;    // Set a new destination.
                        }
                    } else {
                        $pointl = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $pointr = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                        $this->nodes[] = $pointl;
                        $this->nodes[] = $pointr;

                        // Find a link between left neighbor and void.
                        $tmpneighbor = $gmain->find_link($neighborl, $iter);
                        $tmpneighbor->destination = $pointl;    // Set a new destination.

                        // Find a link between void and right neighbor.
                        $tmpneighbor = $gmain->find_link($iter, $neighborr);
                        $tmpneighbor->source = $pointr;    // Set a new source.

                        $this->links[] = new qtype_preg_explaining_graph_tool_link('', $pointl, $pointr, $this);
                    }
                } else {
                    // Find a link between left neighbor and void.
                    $tmpneighbor = $gmain->find_link($neighborl, $iter);
                    $tmpneighbor->destination = $neighborr;    // Set a new destination.

                    $tmpneighbor = $gmain->find_link($iter, $neighborr);
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
     * Checks relationship between $this and $child.
     * @param qtype_preg_explaining_graph_tool_subgraph $child Inner subraph.
     * @return bool true if child is subgraph of parent.
     */
    private function is_child(&$child) {
        foreach ($this->subgraphs as $iter) {
            if ($iter === $child) {
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
    private function &find_link(&$src, &$dst) {
        $result = null;
        // Look over links...
        foreach ($this->links as $iter) {
            // If source and destination is right then set linkowner and return a link.
            if ($iter->destination === $dst && $iter->source === $src) {
                return $iter;
            }
        }

        // Nothing has found? Look the aim in subgraphs!
        foreach ($this->subgraphs as $iter) {
            $result = $iter->find_link($src, $dst);
            if (!is_null($result)) { // If we found something then end this loop!
                return $result;
            }
        }

        return $result;
    }

    /**
     * Creates text with dot instructions.
     * @return string Dot instructions of this subgraph.
     */
    public function create_dot() {
        $this->regenerate_id();
        $instr = 'digraph { compound=true; rankdir = LR;' . ($this->isexact ?  'graph [bgcolor=lightgray];' : '');

        foreach ($this->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' .$iter->id . '" [shape=record, color=black, label=' . $this->compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            } else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') .
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') .
                    'label="' . str_replace(chr(10), '', qtype_preg_authoring_tool::escape_string($iter->label[0])) . '"' . $iter->fill . '];';
            }
        }

        foreach ($this->subgraphs as $iter) {
            $this->process_subgraph($iter, $instr);
        }

        foreach ($this->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd';

            $instr .= $iter->destination->id . '" [label="' . $iter->label . '", arrowhead=' . $iter->style . '];';
        }

        $instr .= '}';

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
                if ($invert || textlib::strlen($lbl[0]) != 1) {
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
                    $result .= '<TD><font color="blue">' . substr($elements[$i], 1) . '</font></TD>';
                } else {
                    $elements[$i] = qtype_preg_authoring_tool::escape_string($elements[$i]);
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
    private function process_subgraph(&$gr, &$instr) {
        $instr .= 'subgraph "cluster_' . $gr->id . '" {';
        $instr .= 'style=' . $gr->style . ';';
        $instr .= 'label="' . $gr->label . '";';

        foreach ($gr->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' . $iter->id . '" [shape=record, color=black, label=' . $this->compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            } else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') .
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') .
                    'label="' . str_replace(chr(10), '', qtype_preg_authoring_tool::escape_string($iter->label[0])) . '"' . $iter->fill . '];';
            }
        }

        foreach ($gr->subgraphs as $iter) {
            $this->process_subgraph($iter, $instr);
        }

        foreach ($gr->links as $iter) {
            $instr .= '"nd' . $iter->source->id . '" -> "nd';

            $instr .= $iter->destination->id . '" [label="' . $iter->label . '", arrowhead=' . $iter->style . '];';
        }

        $instr .= '}';
    }

    /**
     * Finds a maximum id of node in the graph.
     * @return int A maximum id.
     */
    private function find_max_id() {
        $maxid = -1;
        foreach ($this->nodes as $node) {
            $maxid = max($maxid, $node->id);
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
            if ($node->id == -1) {
                $node->id = ++$maxid;
            }
        }

        foreach ($this->subgraphs as $subgraph) {
            $maxid = $subgraph->regenerate_id($maxid);
        }

        return $maxid;
    }
}
