<?php
/**
 * Defines explain graph's handler class.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_explaining_graph_misc.php');

/**
 * Class "handler" for regular expression's graph.
 */
class qtype_preg_explaining_graph_tool extends qtype_preg_dotbased_authoring_tool {

    /**
     * Creates graph which explaining regular expression.
     * @param id - identifier of node which will be picked out in image.
     * @return explainning graph of regular expression.
     */
    public function create_graph($id = -1) {
        $graph = $this->dst_root->create_graph($id);

        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array('begin'), 'box, style=filled', 'purple', $graph, -1);
        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array('end'), 'box, style=filled', 'purple', $graph, -1);

        if (count($graph->nodes) == 2 && count($graph->subgraphs) == 0) {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[0], $graph->nodes[count($graph->nodes) - 1]);
        } else {
            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 2], $graph->entries[count($graph->entries) - 1]);

            $graph->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->exits[count($graph->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->entries = array();
            $graph->exits = array();

            $this->gmain = $graph;

            $this->optimize_graph($graph, $graph);

            $this->linkowner = null;
        }

        return $graph;
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    public function name() {
        return 'explaining_graph_tool';
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function node_infix() {
        // Nodes should be named like qtype_preg_authoring_tool_node_concat.
        // This allows us to use the inherited get_engine_node_name() method.
        return 'authoring_tool';
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function get_engine_node_name($nodetype) {

        if ($nodetype == qtype_preg_node::TYPE_NODE_FINITE_QUANT ||
            $nodetype == qtype_preg_node::TYPE_NODE_INFINITE_QUANT)
            return 'qtype_preg_authoring_tool_node_quant';

        return parent::get_engine_node_name($nodetype);
    }

    /**
     * Overloaded from preg_regex_handler.
     */
    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
        case qtype_preg_node::TYPE_ABSTRACT:
        case qtype_preg_node::TYPE_LEAF_CONTROL:
        case qtype_preg_node::TYPE_NODE_ERROR:
            return false;
        default:
            return true;
        }
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function json_key() {
        return 'graph_src';
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function generate_json_for_empty_regex(&$json_array, $id) {
        $dotscript = 'digraph { }';
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json_array[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);
    }

    /**
     * Overloaded from preg_authoring_tool.
     */
    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
        $dotscript = 'digraph { "Ooops! Your regex contains errors, so I can\'t build the explaining graph!" [color=white]; }';
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json_array[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);
    }

    /**
     * Generate image for explain graph.
     *
     * @param array $json_array contains link on image of explain graph.
     */
    protected function generate_json_for_accepted_regex(&$json_array, $id) {
        $graph = $this->create_graph($id);
        $dotscript = $graph->create_dot();
        $rawdata = qtype_preg_regex_handler::execute_dot($dotscript, 'svg');
        $json_array[$this->json_key()] = 'data:image/svg+xml;base64,' . base64_encode($rawdata);
    }

    public function __construct ($regex = null, $options = null) {
        // Options should exist at least as a default object.
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }
        $options->preserveallnodes = TRUE;
        parent::__construct($regex, $options);
        if ($regex === null) {
            return;
        }
    }

    /**
     * Merges two subgraphs, where acceptor is a main subgraph.
     * @param acceptor - accumulated graph.
     * @param donor - who gives.
     */
    public static function assume_subgraph(&$acceptor, &$donor) {
        foreach ($donor->nodes as $node) {
            $node->owner = $acceptor;
            $acceptor->nodes[] = $node;
        }

        foreach ($donor->links as $link)
            $acceptor->links[] = $link;

        foreach ($donor->subgraphs as $subgraph)
            $acceptor->subgraphs[] = $subgraph;
    }

    private $gmain = null; // the main subgraph for building graph from tree

    /**
     * Optimizes explaining graph.
     * @param graph - optimized graph.
     * @param parent - ancestor of $graph.
     */
    private function optimize_graph(&$graph, &$parent) {

        $this->process_simple($graph);

        $this->process_asserts($graph, $parent);

        $this->process_voids($graph);

        foreach ($graph->subgraphs as $subgraph)
            $this->optimize_graph($subgraph, $graph);
    }

    /**
     * Returns node which is right neighbor for $nd.
     * Searches recursively in subgraph $gr.
     * @param nd - node of graph (subgraph).
     * @param gr - graph in which searching will occurs.
     * @return found node or special 'error' node.
     */
    private function &find_neighbor_dst(&$nd, &$gr) {
        // look over links...
        foreach ($gr->links as $iter) { 
            if ($iter->source === $nd) {   // if source of link is $nd
                return $iter->destination; // then we found what we were looking for
            }
        }

        // if we found nothing, then do the same with subgraphs
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_dst($nd, $iter);
            if ($result->id != -2) {    // if result is valid
                return $result;         // then the right neighbor of $nd is child of current subgraph
            }
        }

        // overwise return an invalid node
        $result = new qtype_preg_explaining_graph_tool_node(array('error'),'','', $this->gmain, -2);

        return $result;
    }

    /**
     * Returns node which is left neighbor for $nd.
     * Searches recursively in subgraph $gr.
     * @param nd - node of graph (subgraph).
     * @param gr - graph in which searching will occurs.
     * @return found node or special 'error' node.
     */
    private function &find_neighbor_src(&$nd, &$gr) {
        // look over links...
        foreach ($gr->links as $iter) {
            if ($iter->destination === $nd) {   // if destination of link is $nd
                return $iter->source;           // then we found what we were looking for
            }
        }

        // if we found nothing, then do the same with subgraphs
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_neighbor_src($nd, $iter);
            if ($result->id != -2) {        // if result is valid
                return $result;             // then the left neighbor of $nd is child of current subgraph
            }
        }

        // overwise return an invalid node
        $result = new qtype_preg_explaining_graph_tool_node(array('error'),'','', $this->gmain, -2);

        return $result;
    }

    private $linkowner = null; // temporary link which is filled by function find_link and uses by various checkings

    /**
     * Returns link with source = $src and destination = $dst.
     * Searches recursively in subgraph $gr.
     * @param src - source of link.
     * @param dst - destination of link.
     * @param gr - graph in which searching will occurs.
     * @return found link.
     */
    private function &find_link(&$src, &$dst, &$gr) {
        $this->linkowner = null;    // at the beginning we found nothing yet
        $result = NULL;                                         // so, no linkowner and result
        // look over links...
        foreach ($gr->links as $iter) {
            // if source and destination is right then set linkowner and return a link
            if ($iter->destination === $dst && $iter->source === $src) {
                $this->linkowner = $gr;
                return $iter;
            }
        }

        // nothing has found ? look the aim in subgraphs!
        foreach ($gr->subgraphs as $iter) {
            $result = $this->find_link($src, $dst, $iter);
            if ($this->linkowner !== null) // if we found something then ok!
                return $result;
        }

        return $result;
    }

    /**
     * Second part of optimization - processing sequences of simple characters in graph.
     * @param graph - processed graph.
     */
    private function process_simple(&$graph) {
        for ($i = 0; $i < count($graph->nodes); $i++) {
            $neighbor = null;   // no neighbor yet

            $tmpdnode = $graph->nodes[$i];  // remember current node

            // if it is simple node with text...
            if ($tmpdnode->color == 'black' && $tmpdnode->shape == 'ellipse') {
                // find a right neighbor of node
                $neighbor = $this->find_neighbor_dst($tmpdnode, $this->gmain);
                // if neighbor is simple node with text too and it's a child of the same subgraph AND it has the same register attribute
                // then we need to join this two nodes.
                if ($neighbor->color == 'black' && $neighbor->shape == 'ellipse' && $neighbor->owner === $graph && $neighbor->fill == $tmpdnode->fill) {
                    // create the new joined node
                    $tmp = new qtype_preg_explaining_graph_tool_node(array($tmpdnode->label[0] . $neighbor->label[0]), $neighbor->shape, $neighbor->color, $graph, $tmpdnode->id, $tmpdnode->fill);

                    //find a link between left neighbor and current node, then change destination to new node
                    $tmpneighbor = $this->find_neighbor_src($tmpdnode, $this->gmain);
                    $tmpneighbor = $this->find_link($tmpneighbor, $tmpdnode, $this->gmain);
                    $tmpneighbor->destination = $tmp;

                    //find a link between neighbor and his right neighbor, then change source to new node
                    $tmpneighbor = $this->find_neighbor_dst($neighbor, $this->gmain);
                    $tmpneighbor = $this->find_link($neighbor, $tmpneighbor, $this->gmain);
                    $tmpneighbor->source = $tmp;

                    // destroy old link
                    $tmpneighbor = $this->find_link($tmpdnode, $neighbor, $this->gmain);
                    unset($this->linkowner->links[array_search($tmpneighbor, $this->linkowner->links)]);
                    $this->linkowner->links = array_values($this->linkowner->links);

                    // destroy old node
                    unset($graph->nodes[array_search($neighbor, $graph->nodes)]);
                    $graph->nodes = array_values($graph->nodes);

                    $graph->nodes[array_search($tmpdnode, $graph->nodes)] = $tmp;

                    $i = -1; // start this loop again
                }
            }
        }
    }

    /**
     * Third part of optimization - processing sequences of asserts in graph and something more.
     * @param graph - processed graph.
     * @param parent - ancestor of $graph.
     */
    private function process_asserts(&$graph, &$parent) {
        // lets find an assert
        foreach ($graph->nodes as $iter) {
            $neighbor = null;

            $tmpdnode = $iter; // let copy current node

            $tmplabel1;
            $tmplabel2;

            // assert should has a red color
            if ($iter->color == 'red') {
                // find its neighbors (left and right)
                $neighborR = $this->find_neighbor_dst($tmpdnode, $this->gmain);
                $neighborL = $this->find_neighbor_src($tmpdnode, $this->gmain);

                // first case - both neighbors are in same subgraph
                if ($neighborR->owner === $neighborL->owner && $neighborL->owner === $graph) {
                    // find labels of links between neighbors and assert
                    $tmplabel1 = $this->find_link($neighborL, $tmpdnode, $this->gmain)->label;
                    $tmplabel2 = $this->find_link($tmpdnode, $neighborR, $this->gmain)->label;

                    // create a new link between neighbors with new label
                    $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, $tmpdnode->label[0]), $tmplabel2), $neighborL, $neighborR);
                // second case - neighbors are not in the same subgraphs, but right neighbor is in same as assert
                } else if ($neighborR->owner !== $neighborL->owner && $neighborL->owner !== $graph && $neighborR->owner === $graph) {
                    // find a label of link between assert and right neighbor
                    $tmplabel2 = $this->find_link($tmpdnode, $neighborR, $this->gmain)->label;

                    // if current subgraph is parent of left neighbor's owner...
                    if ($this->is_child($graph, $neighborL->owner)) {
                        // if left neighbor is just a point...
                        if ($neighborL->shape != 'point') {
                            // create a new point-node
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            // link left neighbor with it
                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $neighborL->owner->nodes[count($neighborL->owner->nodes) - 1]);

                            // create new link between point-node and right neighbor
                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmpdnode->label[0], $tmplabel2), $neighborL->owner->nodes[count($neighborL->owner->nodes) - 1], $neighborR);
                        } else {
                            // create new link between left neighbor and right neighbor
                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmpdnode->label[0], $tmplabel2), $neighborL, $neighborR);
                        }
                    } else {
                        // create a new point-node in current subgraph
                        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                        // link left neighbor with it
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $graph->nodes[count($graph->nodes) - 1]);

                        // link it with right neighbor
                        $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmpdnode->label[0], $tmplabel2), $graph->nodes[count($graph->nodes) - 1], $neighborR);
                    }
                // third case - neighbors are not in the same subgraphs, but left neighbor is in same as assert
                } else if ($neighborR->owner != $neighborL->owner && $neighborL->owner == $graph && $neighborR->owner != $graph) {
                    // find a label of link between left neighbor and assert
                    $tmplabel1 = $this->find_link($neighborL, $tmpdnode, $this->gmain)->label;

                    // if current subgraph is parent of right neighbor's owner...
                    if ($this->is_child($graph, $neighborR->owner)) {
                        // if right neighbor is just a point...
                        if ($neighborR->shape != 'point') {
                            // create a new point-node
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            // link it with right neighbor
                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborR->owner->nodes[count($neighborR->owner->nodes) - 1], $neighborR);

                            // create new link between left neighbor and point-node 
                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, $tmpdnode->label[0]), $neighborL, $neighborR->owner->nodes[count($neighborR->owner->nodes) - 1]);
                        } else {
                            // create new link between left neighbor and right neighbor
                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, $tmpdnode->label[0]), $neighborL, $neighborR);
                        }
                    } else {
                        // create a new point-node in current subgraph
                        $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                        // link it with right neighbor
                        $parent->links[] = new qtype_preg_explaining_graph_tool_link('', end($graph->nodes), $neighborR);

                        // link right neighbor with it
                        $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, $tmpdnode->label[0]), $neighborL, end($graph->nodes));
                    }
                } else { // fourth case - neighbors are not in the same subgraphs and no one in current subgraph
                    $leftborder = $neighborL;
                    $rightborder = $neighborR;

                    $tmplabel2 = $this->find_link($tmpdnode, $neighborR, $this->gmain)->label;
                    $tmplabel1 = $this->find_link($neighborL, $tmpdnode, $this->gmain)->label;

                    // if owners of neighbors are children of current subgraph...
                    if ($this->is_child($graph, $neighborL->owner) && qtype_preg_explaining_graph_tool::is_child($graph, $neighborR->owner)) {
                        if ($neighborR->shape != 'point') {
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborR->owner->nodes), $neighborR);
                            $rightborder = end($neighborR->owner->nodes);
                        }
                        if ($neighborL->shape != 'point') {
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($neighborL->owner->nodes));
                            $leftborder = end($neighborL->owner->nodes);
                        }

                        $graph->links[] = new qtype_preg_explaining_graph_tool_link($tmpdnode->label[0], $leftborder, $rightborder);
                    } else {
                        // if only subgraph of left neighbor is child
                        if ($this->is_child($graph, $neighborL->owner)) {
                            $neighborL->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborL->owner, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($neighborL->owner->nodes));
                            $leftborder = end($neighborL->owner->nodes);

                            $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($graph->nodes), $neighborR);

                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, $tmpdnode->label[0]), $leftborder, end($graph->nodes));
                        
                        // if only subgraph of right neighbor is child
                        } else if ($this->is_child($graph, $neighborR->owner)) {
                            $neighborR->owner->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $neighborR->owner, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', end($neighborR->owner->nodes), $neighborR);
                            $rightborder = end($neighborR->owner->nodes);

                            $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, end($graph->nodes));

                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmpdnode->label[0], $tmplabel2), end($graph->nodes), $rightborder);
                        } else {
                            $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                            $neighborR->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $graph->nodes[count($graph->nodes) - 1], $neighborR);
                            $rightborder = end($graph->nodes);

                            $graph->nodes[] = new qtype_preg_explaining_graph_tool_node(array(''), 'point', 'black', $graph, -1);

                            $neighborL->owner->links[] = new qtype_preg_explaining_graph_tool_link('', $neighborL, $graph->nodes[count($graph->nodes) - 1]);

                            $graph->links[] = new qtype_preg_explaining_graph_tool_link(qtype_preg_explaining_graph_tool::compute_label($tmplabel1, qtype_preg_explaining_graph_tool::compute_label($tmpdnode->label[0], $tmplabel2)), $graph->nodes[count($graph->nodes) - 1], $rightborder);
                        }
                    }
                }

                // destroy links between nighbors and assert
                unset($this->linkowner->links[array_search($this->find_link($neighborL, $tmpdnode, $this->gmain), $this->linkowner->links)]);
                $this->linkowner->links = array_values($this->linkowner->links);
                unset($this->linkowner->links[array_search($this->find_link($tmpdnode, $neighborR, $this->gmain), $this->linkowner->links)]);
                $this->linkowner->links = array_values($this->linkowner->links);

                // destroy assert
                unset($graph->nodes[array_search($tmpdnode, $graph->nodes)]);
                $graph->nodes = array_values($graph->nodes);

                reset($graph->nodes); //start loop again
            }
        }
    }

    /**
     * Checks relationship between $this and $child.
     * @param child - inner subraph.
     * @return true if child is subgraph of parent.
     */
    private function is_child(&$graph, &$child) {
        foreach ($graph->subgraphs as $iter) {
            if ($iter === $child)
                return true;
        }

        return false;
    }

    /**
     * Integrates two labels of nodes or links.
     * @param lbl1 - first label (left side).
     * @param lbl2 - second label (right side).
     * @return integrated label.
     */
    private static function compute_label($lbl1, $lbl2) {
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

    /**
     * Fourth part of optimization - processing sequences of voids in graph.
     * @param graph - processed graph.
     */
    private function process_voids(&$graph) {
        foreach ($graph->nodes as $iter) {
            // void should has an orange color
            if ($iter->color == 'orange') {
                // find neighbors of void
                $neighborL = $this->find_neighbor_src($iter, $this->gmain);
                $neighborR = $this->find_neighbor_dst($iter, $this->gmain);

                // find a link between left neighbor and void
                $tmpneighbor = $this->find_link($neighborL, $iter, $this->gmain);
                $tmpneighbor->destination = $neighborR;    // set a new destination

                // find a link between void and right neighbor and destroy it
                $tmpneighbor = $this->find_link($iter, $neighborR, $this->gmain);
                unset($this->linkowner->links[array_search($tmpneighbor, $this->linkowner->links)]);
                $this->linkowner->links = array_values($this->linkowner->links);

                // destroy void-node
                unset($graph->nodes[array_search($iter, $graph->nodes)]);
                $graph->nodes = array_values($graph->nodes);

                // start loop again
                reset($graph->nodes);
            }
        }
    }
}

?>
