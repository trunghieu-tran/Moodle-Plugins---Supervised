<?php
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

    public $shape  = 'ellipse';  // shape of node on image
    public $color  = 'black';    // color of node on image
    public $owner  = NULL;       // owner of node
    public $label  = '';         // data of node on image
    public $id     = -1;         // id of node
    public $fill   = '';         // filling of node on image
    public $invert = FALSE;      // flag of inversion of node

    /**
     * Counts a number of links in which node is. Searching executes in owner of node.
     * @param bool $type Flag parameter: true - node is destination, false - node is source.
     * @return int A number of links.
     */
    public function links_count($type) {
        $cx = 0; // links counter
        foreach ($this->owner->links as $link) {
            if ($type) {
                if ($link->destination === $this)
                    ++$cx;
            } else {
                if ($link->source === $this)
                    ++$cx;
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
        // look over links...
        foreach ($gr->links as $iter) { 
            if ($iter->source === $this) {   // if source of link is $nd
                return $iter->destination; // then we found what we were looking for
            }
        }

        // if we found nothing, then do the same with subgraphs
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_dst($iter);
            if ($result->id != -2) {    // if result is valid
                return $result;         // then the right neighbor of $nd is child of current subgraph
            }
        }

        // overwise return an invalid node
        $result = new qtype_preg_explaining_graph_tool_node(array('error'),'','', $gmain, -2);

        return $result;
    }

    /**
     * Returns node which is left neighbor of $this.
     * Searches recursively in subgraph $gr.
     * @param qtype_preg_explaining_graph_tool_subgraph $gr Graph in which searching will occurs.
     * @return qtype_preg_explaining_graph_tool_node Found node or special 'error' node.
     */
    public function &find_neighbor_src(&$gr) {
        // look over links...
        foreach ($gr->links as $iter) {
            if ($iter->destination === $this) {   // if destination of link is $nd
                return $iter->source;           // then we found what we were looking for
            }
        }

        // if we found nothing, then do the same with subgraphs
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_src($iter);
            if ($result->id != -2) {        // if result is valid
                return $result;             // then the left neighbor of $nd is child of current subgraph
            }
        }

        // overwise return an invalid node
        $result = new qtype_preg_explaining_graph_tool_node(array('error'),'','', $gmain, -2);

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

    public function __construct($lbl, $shp, $clr, &$ownr, $id, $fll = '') {
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

    public $source = NULL;      // source of link
    public $destination = NULL; // destination of link
    public $label = '';         // label of link on image
    public $style = '';         // visual style of link (for image)
    public $owner = NULL;       // subgraph which has this link

    public function __construct($lbl, $src, $dst, $ownr = NULL, $stl = 'normal') {
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
        if ($lbl1 == $empty && $lbl2 == $empty)
            return $empty;
        else if ($lbl1 == $empty)
            return $lbl2;
        else if ($lbl2 == $empty)
            return $lbl1;
        else
            return $lbl1 . '\n' . $lbl2;
    }
}

/**
 * A subgraph of explaining graph.
 */
class qtype_preg_explaining_graph_tool_subgraph {

    public $label       = '';           // label of subgraph on image
    public $style       = 'solid';      // style of subgraph on image
    public $nodes       = array();      // array of nodes in subgraph
    public $links       = array();      // array of links between nodes in subgraph
    public $subgraphs   = array();      // array of subgraphs in subgraph
    public $entries     = array();      // array if nodes "entries"
    public $exits       = array();      // array of nodes "exits"
    public $id          = -1;           // identifier of subgraph

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

        foreach ($donor->subgraphs as $subgraph)
            $this->subgraphs[] = $subgraph;
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

        foreach ($this->subgraphs as $subgraph)
            $subgraph->optimize_graph($this, $gmain);
    }

    /**
     * Second part of optimization - processing sequences of simple characters in graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $gmain Main subgraph.
     */
    public function process_simple_characters(&$gmain) {
        for ($i = 0; $i < count($this->nodes); $i++) {
            $neighbor = NULL;   // no neighbor yet

            $tmpdnode = $this->nodes[$i];  // remember current node

            // if it is simple node with text...
            if ($tmpdnode->color == 'black' && $tmpdnode->shape == 'ellipse') {
                // find a right neighbor of node
                $neighbor = $tmpdnode->find_neighbor_dst($gmain);
                // if neighbor is simple node with text too and it's a child of the same subgraph AND it has the same register attribute
                // then we need to join this two nodes.
                if ($neighbor->color == 'black' && $neighbor->shape == 'ellipse' && $neighbor->owner === $this && $neighbor->fill == $tmpdnode->fill) {
                    // create the new joined node
                    $tmp = new qtype_preg_explaining_graph_tool_node(array($tmpdnode->label[0] . $neighbor->label[0]), $neighbor->shape, $neighbor->color, $this, $tmpdnode->id, $tmpdnode->fill);

                    //find a link between left neighbor and current node, then change destination to new node
                    $tmpneighbor = $tmpdnode->find_neighbor_src($gmain);
                    $tmpneighbor = $gmain->find_link($tmpneighbor, $tmpdnode);
                    $tmpneighbor->destination = $tmp;

                    //find a link between neighbor and his right neighbor, then change source to new node
                    $tmpneighbor = $neighbor->find_neighbor_dst($gmain);
                    if (!($tmpneighbor->label[0] == 'error' and $tmpneighbor->id == -2)) {
                        $tmpneighbor = $gmain->find_link($neighbor, $tmpneighbor);
                        $tmpneighbor->source = $tmp;
                    }

                    // destroy old link
                    $tmpneighbor = $gmain->find_link($tmpdnode, $neighbor);
                    unset($tmpneighbor->owner->links[array_search($tmpneighbor, $tmpneighbor->owner->links)]);
                    $tmpneighbor->owner->links = array_values($tmpneighbor->owner->links);

                    // destroy old node
                    unset($this->nodes[array_search($neighbor, $this->nodes)]);
                    $this->nodes = array_values($this->nodes);

                    $this->nodes[array_search($tmpdnode, $this->nodes)] = $tmp;

                    $i = -1; // start this loop again
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
        // lets find an assert
        foreach ($this->nodes as $iter) {
            $neighbor = null;

            $tmpdnode = $iter; // let copy current node

            $tmplabel1;
            $tmplabel2;

            // assert should has a red color
            if ($iter->color == 'red') {
                // find its neighbors (left and right)
                $neighborR = $tmpdnode->find_neighbor_dst($gmain);
                $neighborL = $tmpdnode->find_neighbor_src($gmain);

                // first case - both neighbors are in same subgraph
                if ($neighborR->owner === $neighborL->owner && $neighborL->owner === $this) {
                    // find labels of links between neighbors and assert
                    $tmplabel1 = $gmain->find_link($neighborL, $tmpdnode)->label;
                    $tmplabel2 = $gmain->find_link($tmpdnode, $neighborR)->label;

                    // create a new link between neighbors with new label
                    $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                        qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $tmplabel2), $neighborL, $neighborR, $this);
                // second case - neighbors are not in the same subgraphs, but right neighbor is in same as assert
                } else if ($neighborR->owner !== $neighborL->owner && $neighborL->owner !== $this && $neighborR->owner === $this) {
                    // find a label of link between assert and right neighbor
                    $tmplabel2 = $gmain->find_link($tmpdnode, $neighborR)->label;

                    // if current subgraph is parent of left neighbor's owner...
                    if ($this->is_child($neighborL->owner)) {
                        // if left neighbor is just a point...
                        if ($neighborL->shape != 'point') {
                            // create a new point-node
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            // link left neighbor with it
                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $neighborL->owner->nodes[count($neighborL->owner->nodes) - 1], $neighborL->owner);

                            // create new link between point-node and right neighbor
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                                $tmpdnode->label[0], $tmplabel2), $neighborL->owner->nodes[count($neighborL->owner->nodes) - 1], $neighborR, $this);
                        } else {
                            // create new link between left neighbor and right neighbor
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2), $neighborL, $neighborR, $this);
                        }
                    } else {
                        // create a new point-node in current subgraph
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                        // link left neighbor with it
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $this->nodes[count($this->nodes) - 1], $parent);

                        // link it with right neighbor
                        $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                            $tmpdnode->label[0], $tmplabel2), $this->nodes[count($this->nodes) - 1], $neighborR, $this);
                    }
                // third case - neighbors are not in the same subgraphs, but left neighbor is in same as assert
                } else if ($neighborR->owner !== $neighborL->owner && $neighborL->owner === $this && $neighborR->owner !== $this) {
                    // find a label of link between left neighbor and assert
                    $tmplabel1 = $gmain->find_link($neighborL, $tmpdnode)->label;
                    // if current subgraph is parent of right neighbor's owner...
                    if ($this->is_child($neighborR->owner)) {
                        // if right neighbor is just a point...
                        if ($neighborR->shape != 'point') {
                            // create a new point-node
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            // link it with right neighbor
                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborR->owner->nodes[count($neighborR->owner->nodes) - 1], $neighborR, $neighborR->owner);

                            // create new link between left neighbor and point-node 
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                                $tmplabel1, $tmpdnode->label[0]), $neighborL, $neighborR->owner->nodes[count($neighborR->owner->nodes) - 1], $this);
                        } else {
                            // create new link between left neighbor and right neighbor
                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $neighborL, $neighborR, $this);
                        }
                    } else {
                        // create a new point-node in current subgraph
                        $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                        // link it with right neighbor
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', end($this->nodes), $neighborR, $parent);

                        // link right neighbor with it
                        $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $neighborL, end($this->nodes), $this);
                    }
                } else { // fourth case - neighbors are not in the same subgraphs and no one in current subgraph
                    $leftborder = $neighborL;
                    $rightborder = $neighborR;

                    $tmplabel2 = $gmain->find_link($tmpdnode, $neighborR)->label;
                    $tmplabel1 = $gmain->find_link($neighborL, $tmpdnode)->label;

                    // if owners of neighbors are children of current subgraph...
                    if ($this->is_child($neighborL->owner) && $this->is_child($neighborR->owner)) {
                        if ($neighborR->shape != 'point') {
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborR->owner->nodes), $neighborR, $neighborR->owner);
                            $rightborder = end($neighborR->owner->nodes);
                        }
                        if ($neighborL->shape != 'point') {
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($neighborL->owner->nodes), $neighborL->owner);
                            $leftborder = end($neighborL->owner->nodes);
                        }

                        $this->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder, $this);
                    } else {
                        // if only subgraph of left neighbor is child
                        if ($this->is_child($neighborL->owner)) {
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($neighborL->owner->nodes), $neighborL->owner);
                            $leftborder = end($neighborL->owner->nodes);

                            $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($this->nodes), $neighborR, $neighborR->owner);

                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label($tmplabel1, $tmpdnode->label[0]), $leftborder, end($this->nodes), $this);
                        
                        // if only subgraph of right neighbor is child
                        } else if ($this->is_child($neighborR->owner)) {
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborR->owner->nodes), $neighborR, $neighborR->owner);
                            $rightborder = end($neighborR->owner->nodes);

                            $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($this->nodes), $neighborL->owner);

                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2), end($this->nodes), $rightborder, $this);
                        } else {
                            $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $this->nodes[count($this->nodes) - 1], $neighborR, $neighborR->owner);
                            $rightborder = end($this->nodes);

                            $this->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $this->nodes[count($this->nodes) - 1], $neighborL->owner);

                            $this->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool_link::compute_label(
                                $tmplabel1, qtype_preg_explaining_graph_tool_link::compute_label($tmpdnode->label[0], $tmplabel2)), $this->nodes[count($this->nodes) - 1], $rightborder, $this);
                        }
                    }
                }

                // destroy links between nighbors and assert
                $tmplink = $gmain->find_link($neighborL, $tmpdnode);
                unset($tmplink->owner->links[array_search($tmplink, $tmplink->owner->links)]);
                $tmplink->owner->links = array_values($tmplink->owner->links);
                $tmplink = $gmain->find_link($tmpdnode, $neighborR);
                unset($tmplink->owner->links[array_search($tmplink, $tmplink->owner->links)]);
                $tmplink->owner->links = array_values($tmplink->owner->links);

                // destroy assert
                unset($this->nodes[array_search($tmpdnode, $this->nodes)]);
                $this->nodes = array_values($this->nodes);

                reset($this->nodes); //start loop again
            }
        }
    }

    /**
     * Fourth part of optimization - processing sequences of voids in graph.
     * @param qtype_preg_explaining_graph_tool_subgraph $graph Processed graph.
     */
    public function process_voids(&$gmain) {
        foreach ($this->nodes as $iter) {
            // void should has an orange color
            if ($iter->color == 'orange') {
                // find neighbors of void
                $neighborL = $iter->find_neighbor_src($gmain);
                $neighborR = $iter->find_neighbor_dst($gmain);

                if ($this->style != 'solid; color=darkgreen') {
                    // find a link between left neighbor and void
                    $tmpneighbor = $gmain->find_link($neighborL, $iter);
                    $tmpneighbor->destination = $neighborR;    // set a new destination

                    // find a link between void and right neighbor and destroy it
                    $tmpneighbor = $gmain->find_link($iter, $neighborR);
                    unset($tmpneighbor->owner->links[array_search($tmpneighbor, $tmpneighbor->owner->links)]);
                    $tmpneighbor->owner->links = array_values($tmpneighbor->owner->links);
                } else {
                    $pointl = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                    $pointr = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $this, -1);
                    $this->nodes[] = $pointl;
                    $this->nodes[] = $pointr;

                    // find a link between left neighbor and void
                    $tmpneighbor = $gmain->find_link($neighborL, $iter);
                    $tmpneighbor->destination = $pointl;    // set a new destination

                    // find a link between void and right neighbor
                    $tmpneighbor = $gmain->find_link($iter, $neighborR);
                    $tmpneighbor->source = $pointr;    // set a new source

                    $this->links[] = new qtype_preg_explaining_graph_tool_link('', $pointl, $pointr, $this);
                }

                // destroy void-node
                unset($this->nodes[array_search($iter, $this->nodes)]);
                $this->nodes = array_values($this->nodes);

                // start loop again
                reset($this->nodes);
            }
        }
    }

    /**
     * Checks relationship between $this and $child.
     * @param qtype_preg_explaining_graph_tool_subgraph $child Inner subraph.
     * @return bool True if child is subgraph of parent.
     */
    private function is_child(&$child) {
        foreach ($this->subgraphs as $iter) {
            if ($iter === $child)
                return true;
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
        $result = NULL;
        // look over links...
        foreach ($this->links as $iter) {
            // if source and destination is right then set linkowner and return a link
            if ($iter->destination === $dst && $iter->source === $src) {
                return $iter;
            }
        }

        // nothing has found ? look the aim in subgraphs!
        foreach ($this->subgraphs as $iter) {
            $result = $iter->find_link($src, $dst);
            if (!is_null($result)) // if we found something then end this loop!
                return $result;
        }

        return $result;
    }

    /**
     * Creates text with dot instructions.
     * @return string Dot instructions of this subgraph.
     */
    public function create_dot() {
        $this->regenerate_id();
        $instr = 'digraph { compound=true; rankdir = LR;';

        foreach ($this->nodes as $iter) {
            if ($iter->shape == 'record') {
                $instr .= '"nd' .$iter->id . '" [shape=record, color=black, label=' . $this->compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            } else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') . 
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') . 
                    'label="' . str_replace(chr(10), '', $iter->label[0]) . '"' . $iter->fill . '];';
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
                if ($invert || strlen($lbl[0]) != 1)
                    $elements[] = $lbl[0];
                else
                    return '"' . $lbl[0] . '"';
            } else {
                for ($i = 0; $i < count($lbl); ++$i) {
                        $elements[] = $lbl[$i];
                }
            }

            $result .= '<<TABLE BORDER="0" CELLBORDER="1" CELLSPACING="0" CELLPADDING="4"><TR><TD COLSPAN="';
            $result .= count($elements);
            if ($invert)
                $result .= '"><font face="Arial">' . get_string('explain_any_char_except', 'qtype_preg') . '</font></TD></TR><TR>';
            else
                $result .= '"><font face="Arial">' . get_string('explain_any_char', 'qtype_preg') . '</font></TD></TR><TR>';

            for ($i = 0; $i != count($elements); ++$i) {
                if ($elements[$i][0] == chr(10))
                    $result .= '<TD><font color="blue">' . substr($elements[$i], 1) . '</font></TD>';
                else
                    $elements[$i] = qtype_preg_authoring_tool::escape_string($elements[$i]);
                    $result .= '<TD>' . $elements[$i] . '</TD>';
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
            if ($iter->shape == 'record')
                $instr .= '"nd' . $iter->id . '" [shape=record, color=black, label=' . $this->compute_html($iter->label, $iter->invert) . $iter->fill . '];';
            else {
                $instr .= '"nd' . $iter->id . '" [' . ($iter->shape == 'ellipse' ? '' : 'shape=' . $iter->shape . ', ') . 
                    ($iter->color == 'black' ? '' : 'color=' . $iter->color . ', ') . 
                    'label="' . str_replace(chr(10), '', $iter->label[0]) . '"' . $iter->fill . '];';
            }
        }

        foreach ($gr->subgraphs as $iter)
            $this->process_subgraph($iter, $instr);

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

?>
