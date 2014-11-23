<?php
/**
 * Defines explain graph's handler class.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */
 
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/authors_tool/explain_graph_misc.php');
require_once($CFG->dirroot . '/question/type/preg/authors_tool/preg_authors_tool.php');

/**
 * Class "handler" for regular expression's graph.
 */
class qtype_preg_author_tool_explain_graph extends qtype_preg_author_tool {

    /**
     * Access function to DST root.
     * Used mainly for unit-testing and avoiding re-parsing
     */
    public function get_dst_root() {
        return $this->dst_root;
    }

    /**
     * Creates graph which explaining regular expression.
     */
    public function create_graph($id = -1) {
        $graph = $this->dst_root->create_graph($id);
       
        $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('begin'), 'box, style=filled', 'purple', $graph, -2);
        $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node(array('end'), 'box, style=filled', 'purple', $graph, -3);
        
        if (count($graph->nodes) == 2 && count($graph->subgraphs) == 0) {
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[0], $graph->nodes[count($graph->nodes) - 1]);
        } else {
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 2], $graph->entries[count($graph->entries) - 1]);
            
            $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->exits[count($graph->exits) - 1], $graph->nodes[count($graph->nodes) - 1]);
            $graph->entries = array();
            $graph->exits = array();

            qtype_preg_author_tool_explain_graph::$gmain = $graph;

            $this->optimize_graph($graph);
            
            qtype_preg_author_tool_explain_graph::$gmain = null;
            qtype_preg_author_tool_explain_graph::$linkowner = null;
        }
        
        return $graph;
    }
    
    public function name() {
        return 'author_tool_explain_graph';
    }
    
    protected function get_engine_node_name($pregname) {
        switch($pregname) {
        case 'node_finite_quant':
        case 'node_infinite_quant':
        case 'node_concat':
        case 'node_alt':
        case 'node_subpatt':
            return 'qtype_preg_author_tool_operator';

        case 'leaf_charset':
        case 'leaf_meta':
        case 'leaf_assert':
        case 'leaf_backref':
        case 'leaf_recursion:':
            return 'qtype_preg_author_tool_leaf';
        }

        return parent::get_engine_node_name($pregname);
    }

    protected function is_preg_node_acceptable($pregnode) {
        switch ($pregnode->type) {
            case qtype_preg_node::TYPE_ABSTRACT:
            case qtype_preg_node::TYPE_LEAF_CONTROL:
            case qtype_preg_node::TYPE_LEAF_OPTIONS:
            case qtype_preg_node::TYPE_NODE_COND_SUBPATT:
            case qtype_preg_node::TYPE_NODE_ERROR:
            case qtype_preg_node::TYPE_NODE_ASSERT:
                return false;

            default:
                return true;
        }
    }
    
    public function __construct ($regex = null, $modifiers = null) {
        parent::__construct($regex, $modifiers);
        if ($regex === null) {
            return;
        }
    }
    
    /**
     * Merge two subgraphs, where acceptor is main subgraph.
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
    
    private static $gmain = null; // the main subgraph for building graph from tree
    
    /**
     * Optimizes explaining graph.
     */
    private static function optimize_graph(&$graph) {
        
        qtype_preg_author_tool_explain_graph::process_alters($graph);

        qtype_preg_author_tool_explain_graph::process_simple($graph);
        
        qtype_preg_author_tool_explain_graph::process_asserts($graph);
        
        qtype_preg_author_tool_explain_graph::process_voids($graph);

        foreach ($graph->subgraphs as $subgraph)
            qtype_preg_author_tool_explain_graph::optimize_graph($subgraph);
    }
    
    /**
     * First part of optimization - processing alternatives in graph.
     */
    private static function process_alters(&$graph) {
        begin:
        foreach ($graph->nodes as $iter) {
            $neighbor = null;

            if ($iter->shape == 'point') {
                $tmplinks = array();
                $neighbor = qtype_preg_author_tool_explain_graph::find_neighbor_src($iter, qtype_preg_author_tool_explain_graph::$gmain);
                
                if ($neighbor->shape == 'point' && $neighbor->owner == $iter->owner) {
                    if ($neighbor->links_count(false) == 2) {
                        $tmplinks = $iter->links();
                        foreach ($tmplinks as $iter2) {
                            if ($iter2->source === $iter) {
                                $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor, $iter2->destination);
                                
                                $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($iter2->source, $iter2->destination, qtype_preg_author_tool_explain_graph::$gmain);
                                unset($graph->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                                qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);
                            }
                        }
                        
                        $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($neighbor, $iter, qtype_preg_author_tool_explain_graph::$gmain);
                        unset($graph->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                        qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);
                        unset($graph->nodes[array_search($iter, $graph->nodes)]);
                        $graph->nodes = array_values($graph->nodes);
                        
                        reset($graph->nodes);
                        continue;
                    }
                }
        
                $neighbor = qtype_preg_author_tool_explain_graph::find_neighbor_dst($iter, qtype_preg_author_tool_explain_graph::$gmain);
 
                if ($neighbor->shape == 'point' && $neighbor->owner == $iter->owner) {
                    if ($neighbor->links_count(true) == 2) {
                        $tmplinks = $iter->links();
                        foreach ($tmplinks as $iter2) {
                            if ($iter2->destination === $iter) {
                                $graph->links[] = new qtype_preg_author_tool_explain_graph_link('', $iter2->source, $neighbor);
                                
                                $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($iter2->source, $iter2->destination, qtype_preg_author_tool_explain_graph::$gmain);
                                unset($graph->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                                qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);
                            }
                        }

                        $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($iter, $neighbor, qtype_preg_author_tool_explain_graph::$gmain);
                        unset(qtype_preg_author_tool_explain_graph::$linkowner->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                        qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);
                        
                        unset($graph->nodes[array_search($iter, $graph->nodes)]);
                        $graph->nodes = array_values($graph->nodes);

                        reset($graph->nodes);
                    }
                }
            }
        }
    }
    
    /**
     * Returns node which is right neighbor for $dn.
     * Searches recursively in subgraph $gr
     */
    private static function &find_neighbor_dst(&$dn, &$gr) {
        foreach ($gr->links as $iter) {
            if ($iter->source === $dn) {
                return $iter->destination;
            }
        }

        foreach ($gr->subgraphs as $iter) {
            $result = qtype_preg_author_tool_explain_graph::find_neighbor_dst($dn, $iter);
            return $result;
        }
        
        $result = new qtype_preg_author_tool_explain_graph_node('error','','', qtype_preg_author_tool_explain_graph::$gmain, -1);
        
        return $result;
    }

    /**
     * Returns node which is left neighbor for $dn.
     * Searches recursively in subgraph $gr
     */
    private static function &find_neighbor_src(&$dn, &$gr) {
        foreach ($gr->links as $iter) {
            if ($iter->destination === $dn) {
                return $iter->source;
            }
        }

        foreach ($gr->subgraphs as $iter) {
            $result = qtype_preg_author_tool_explain_graph::find_neighbor_src($dn, $iter);
            return $result;
        }
        
        $result = new qtype_preg_author_tool_explain_graph_node('error','','', qtype_preg_author_tool_explain_graph::$gmain, -1);
        
        return $result;
    }
    
    private static $linkowner = null; // temporary link which is filled by function find_link and uses by various checkings  
    
    /**
     * Returns link with source = $src and destination = dst.
     * Searches recursively in subgraph $gr
     */
    private static function &find_link(&$src, &$dst, &$gr) {
        qtype_preg_author_tool_explain_graph::$linkowner = null;

        foreach ($gr->links as $iter) {
            if ($iter->destination === $dst && $iter->source === $src) {
                qtype_preg_author_tool_explain_graph::$linkowner = $gr;
                return $iter;
            }
        }

        foreach ($gr->subgraphs as $iter) {
            $result = qtype_preg_author_tool_explain_graph::find_link($src, $dst, $iter);
            if (qtype_preg_author_tool_explain_graph::$linkowner !== null)
                return $result;
        }

        return $result;
    }
    
    /**
     * Second part of optimization - processing sequences of simple characters in graph.
     */
    private static function process_simple(&$graph) {
        for ($i = 0; $i < count($graph->nodes); $i++) {
            $neighbor = null;

            $tmpdnode = $graph->nodes[$i];

            if ($tmpdnode->color == 'black' && $tmpdnode->shape == 'ellipse' && $tmpdnode->fill == '') {
                $neighbor = qtype_preg_author_tool_explain_graph::find_neighbor_dst($tmpdnode, qtype_preg_author_tool_explain_graph::$gmain);
                if ($neighbor->color == 'black' && $neighbor->shape == 'ellipse' && $neighbor->owner === $graph && $neighbor->fill == '') {
                    //create the new unioned node
                    $tmp = new qtype_preg_author_tool_explain_graph_node(array($tmpdnode->label[0] . $neighbor->label[0]), $neighbor->shape, $neighbor->color, $graph, $tmpdnode->id);

                    //find link between left neighbor and current node, then change destination to new node
                    $tmpneighbor = qtype_preg_author_tool_explain_graph::find_neighbor_src($tmpdnode, qtype_preg_author_tool_explain_graph::$gmain);
                    $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($tmpneighbor, $tmpdnode, qtype_preg_author_tool_explain_graph::$gmain);
                    $tmpneighbor->destination = $tmp;
                    
                    $tmpneighbor = qtype_preg_author_tool_explain_graph::find_neighbor_dst($neighbor, qtype_preg_author_tool_explain_graph::$gmain);
                    $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($neighbor, $tmpneighbor, qtype_preg_author_tool_explain_graph::$gmain);
                    $tmpneighbor->source = $tmp;

                    $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($tmpdnode, $neighbor, qtype_preg_author_tool_explain_graph::$gmain);
                    unset(qtype_preg_author_tool_explain_graph::$linkowner->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                    qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);

                    unset($graph->nodes[array_search($neighbor, $graph->nodes)]);
                    $graph->nodes = array_values($graph->nodes);
                    
                    $graph->nodes[array_search($tmpdnode, $graph->nodes)] = $tmp;

                    $i = -1;
                }
            }
        }
    }
    
    /**
     * Third part of optimization - processing sequences of asserts in graph and something more.
     */
    private static function process_asserts(&$graph) {
        nodes_cycle:
        foreach ($graph->nodes as $iter) {
            $neighbor = null;

            $tmpdnode = $iter;

            $tmplabel1;
            $tmplabel2;

            if ($iter->color == 'red') {
                $neighbor_r = qtype_preg_author_tool_explain_graph::find_neighbor_dst($tmpdnode, qtype_preg_author_tool_explain_graph::$gmain);
                $neighbor_l = qtype_preg_author_tool_explain_graph::find_neighbor_src($tmpdnode, qtype_preg_author_tool_explain_graph::$gmain);

                if ($neighbor_r->owner == $neighbor_l->owner && $neighbor_l->owner == $graph) {
                    $tmplabel1 = qtype_preg_author_tool_explain_graph::find_link($neighbor_l, $tmpdnode, qtype_preg_author_tool_explain_graph::$gmain)->label;
                    $tmplabel2 = qtype_preg_author_tool_explain_graph::find_link($tmpdnode, $neighbor_r, qtype_preg_author_tool_explain_graph::$gmain)->label;
                    
                    $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, $tmpdnode->label), $tmplabel2), $neighbor_l, $neighbor_r);
                } else if ($neighbor_r->owner != $neighbor_l->owner && $neighbor_l->owner != $graph && $neighbor_r->owner == $graph) {
                    $tmplabel2 = qtype_preg_author_tool_explain_graph::find_link($tmpdnode, $neighbor_r, qtype_preg_author_tool_explain_graph::$gmain)->label;

                    if (qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_l->owner)) {
                        if ($neighbor_l->shape != 'point') {
                            $neighbor_l->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_l->owner, $iter->id + 0.05);

                            $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, $neighbor_l->owner->nodes[count($neighbor_l->owner->nodes) - 1]);

                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmpdnode->label, $tmplabel2), $neighbor_l->owner->nodes[count($neighbor_l->owner->nodes) - 1], $neighbor_r);
                        } else {
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmpdnode->label, $tmplabel2), $neighbor_l, $neighbor_r);
                        }
                    } else {
                        $graph->nodes[] = new qtype_preg_author_tool_graph_node('', 'point', 'black', $graph, $iter->id - 0.05);
                        
                        $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, $graph->nodes[count($graph->nodes) - 1]);
                        
                        $graph->links[] = new qtype_preg_author_tool_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmpdnode->label, $tmplabel2), $graph->nodes[count($graph->nodes) - 1], $neighbor_r);
                    }
                } else if ($neighbor_r->owner != $neighbor_l->owner && $neighbor_l->owner == $graph && $neighbor_r->owner != $graph) {
                    $tmplabel1 = qtype_preg_author_tool_explain_graph::find_link($neighbor_l, $tmpdnode, qtype_preg_author_tool_explain_graph::$gmain)->label; 

                    if (qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_r->owner)) {
                        if ($neighbor_r->shape != 'point') {
                            $neighbor_r->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_r->owner, $iter->id + 0.05);
                            
                            $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_r->owner->nodes[count($neighbor_r->owner->nodes) - 1], $neighbor_r);
                            
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, $tmpdnode->label), $neighbor_l, $neighbor_r->owner->nodes[count($neighbor_r->owner->nodes) - 1]);
                        } else {
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, $tmpdnode->label), $neighbor_l, $neighbor_r);
                        }
                    } else {
                        $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $iter->id - 0.05);
                        
                        $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', end($graph->nodes), $neighbor_r);
                        
                        $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, $tmpdnode->label), $neighbor_l, end($graph->nodes));
                    }
                } else {
                    $leftborder = $neighbor_l;
                    $rightborder = $neighbor_r;

                    $tmplabel2 = qtype_preg_author_tool_explain_graph::find_link($tmpdnode, $neighbor_r, qtype_preg_author_tool_explain_graph::$gmain)->label;
                    $tmplabel1 = qtype_preg_author_tool_explain_graph::find_link($neighbor_l, $tmpdnode, qtype_preg_author_tool_explain_graph::$gmain)->label; 

                    if (qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_l->owner) && qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_r->owner)) {
                        if ($neighbor_r->shape != 'point') {
                            $neighbor_r->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_r->owner, $neighbor_l->id + 0.05);
                            
                            $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', end($neighbor_r->owner->nodes), $neighbor_r);
                            $rightborder = end($neighbor_r->owner->nodes);
                        }
                        if ($neighbor_l->shape != 'point') {
                            $neighbor_l->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_l->owner, $neighbor_l->id - 0.05);
                            
                            $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, end($neighbor_l->owner->nodes));
                            $leftborder = end($neighbor_l->owner->nodes);
                        }

                        $graph->links[] = new qtype_preg_author_tool_explain_graph_link($tmpdnode->label, $leftborder, $rightborder);
                    } else {
                        if (qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_l->owner)) {
                            $neighbor_l->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_l->owner, $neighbor_l->id + 0.05);
                            
                            $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, end($neighbor_l->owner->nodes));
                            $leftborder = end($neighbor_l->owner->nodes);

                            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $neighbor_l->id - 0.05);
                            
                            $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', end($graph->nodes), $neighbor_r);
                            
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, $tmpdnode->label), $leftborder, end($graph->nodes));
                        } else if (qtype_preg_author_tool_explain_graph::is_child($graph, $neighbor_r->owner)) {
                            $neighbor_r->owner->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $neighbor_r->owner, $neighbor_l->id + 0.05);
                            
                            $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', end($neighbor_r->owner->nodes), $neighbor_r);
                            $rightborder = end($neighbor_r->owner->nodes);

                            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $neighbor_l->id - 0.05);
                            
                            $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, end($graph->nodes));
                            
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmpdnode->label, $tmplabel2), end($graph->nodes), $rightborder);
                        } else {
                            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $neighbor_l->id + 0.05);
                            
                            $neighbor_r->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $graph->nodes[count($graph->nodes) - 1], $neighbor_r);
                            $rightborder = end($graph->nodes);

                            $graph->nodes[] = new qtype_preg_author_tool_explain_graph_node('', 'point', 'black', $graph, $neighbor_l->id - 0.05);
                            
                            $neighbor_l->owner->links[] = new qtype_preg_author_tool_explain_graph_link('', $neighbor_l, $graph->nodes[count($graph->nodes) - 1]);
                            
                            $graph->links[] = new qtype_preg_author_tool_explain_graph_link(qtype_preg_author_tool_explain_graph::compute_label($tmplabel1, qtype_preg_author_tool_explain_graph::compute_label($tmpdnode->label, $tmplabel2)), $graph->nodes[count($graph->nodes) - 1], $rightborder);
                        }
                    }
                }
                
                unset(qtype_preg_author_tool_explain_graph::$linkowner->links[array_search(qtype_preg_author_tool_explain_graph::find_link($neighbor_l, $tmpdnode, qtype_preg_author_tool_explain_graph::$gmain), qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);
                unset(qtype_preg_author_tool_explain_graph::$linkowner->links[array_search(qtype_preg_author_tool_explain_graph::find_link($tmpdnode, $neighbor_r, qtype_preg_author_tool_explain_graph::$gmain), qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);

                unset($graph->nodes[array_search($tmpdnode, $graph->nodes)]);
                $graph->nodes = array_values($graph->nodes);

                reset($graph->nodes);
            }
        }
    }
    
    /**
     * Returns true if child is subgraph of parent
     */
    private static function is_child(&$parent, &$child) {
        foreach ($parent->subgraphs as $iter) {
            if ($iter === $child)
                return true;
        }

        return false;
    }
    
    /**
     * Integrate two labels of nodes or links
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
     */
    private static function process_voids(&$graph) {
        foreach ($graph->nodes as $iter) {
            if ($iter->color == 'orange') {
                $neighbor_l = qtype_preg_author_tool_explain_graph::find_neighbor_src($iter, qtype_preg_author_tool_explain_graph::$gmain);
                $neighbor_r = qtype_preg_author_tool_explain_graph::find_neighbor_dst($iter, qtype_preg_author_tool_explain_graph::$gmain);
                
                $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($neighbor_l, $iter, qtype_preg_author_tool_explain_graph::$gmain);
                $tmpneighbor->destination = $neighbor_r;

                $tmpneighbor = qtype_preg_author_tool_explain_graph::find_link($iter, $neighbor_r, qtype_preg_author_tool_explain_graph::$gmain);
                unset(qtype_preg_author_tool_explain_graph::$linkowner->links[array_search($tmpneighbor, qtype_preg_author_tool_explain_graph::$linkowner->links)]);
                qtype_preg_author_tool_explain_graph::$linkowner->links = array_values(qtype_preg_author_tool_explain_graph::$linkowner->links);

                unset($graph->nodes[array_search($iter, $graph->nodes)]);
                $graph->nodes = array_values($graph->nodes);

                reset($graph->nodes);
            }
        }
    }
    
    /**
     * Returns true if two nodes of graph are equal
     */
    public static function cmp_nodes(&$n1, &$n2) {
        if ($n1->color != $n2->color) {
            print(chr(10));
            print('Colors of nodes failed!');
            print(chr(10));
            return false;
        }
        if ($n1->label != $n2->label) {
            print(chr(10));
            print('Labels of nodes failed!');
            return false;
        }
        if ($n1->shape != $n2->shape) {
            print(chr(10));
            print('Shapes of nodes failed!');
            return false;
        }

        return true;
    }
    
    /**
     * Returns true if two subgraphs of graph are equal
     */
    public static function cmp_graphs(&$g1, &$g2)
    {
        if ($g1->label != $g2->label) {
            print(chr(10));
            print('Labels of subgraphs failed!');
            return false;
        }
        if ($g1->style != $g2->style) {
            print(chr(10));
            print('Styles of subgraphs failed!');
            return false;
        }

        if (count($g1->nodes) == count($g2->nodes)) {
            for ($i = 0; $i < count($g1->nodes); ++$i) {
                if (!qtype_preg_author_tool_explain_graph::cmp_nodes($g1->nodes[$i], $g2->nodes[$i]))
                    return false;
            }
        } else {		
			return false;
		}

        if (count($g1->entries) == count($g2->entries)) {
            for ($i = 0; $i < count($g1->entries); ++$i) {
                if (!qtype_preg_author_tool_explain_graph::cmp_nodes($g1->entries[$i], $g2->entries[$i]))
                    return false;
            }
        }
        else return false;

        if (count($g1->exits) == count($g2->exits)) {
            for ($i = 0; $i < count($g1->exits); ++$i) {
                if (!qtype_preg_author_tool_explain_graph::cmp_nodes($g1->exits[$i], $g2->exits[$i]))
                    return false;
            }
        } else {
            return false;
        }

        if (count($g1->links) == count($g2->links)) {
            for ($i = 0; $i < count($g1->links); ++$i) {
                if ($g1->links[$i]->label != $g2->links[$i]->label)
                    return false;
                if (!qtype_preg_author_tool_explain_graph::cmp_nodes($g1->links[$i]->destination, $g2->links[$i]->destination))
                    return false;
                if (!qtype_preg_author_tool_explain_graph::cmp_nodes($g1->links[$i]->source, $g2->links[$i]->source))
                    return false;
            }
        }
        else {
            return false;
        }

        if (count($g1->subgraphs) == count($g2->subgraphs)) {
            for ($i = 0; $i < count($g1->subgraphs); ++$i) {
                if (!qtype_preg_author_tool_explain_graph::cmp_graphs($g1->subgraphs[$i], $g2->subgraphs[$i]))
                    return false;
            }
        }
        else {
            return false;
        }

        return true;
    }
    
    public function get_html() {
        return null;
    }

    public function get_errors() {
        $res = array();
        foreach($this->errors as $error) {
            $res[] = $error;
        }
        return $res;
    }
}

?>
