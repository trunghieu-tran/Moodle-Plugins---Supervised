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
 * Defines FA node classes.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_fa.php');

/**
 * Abstract class for both nodes (operators) and leafs (operands).
 */
abstract class qtype_preg_fa_node {

    public $pregnode;    // Reference to the corresponding qtype_preg_node.

    /**
     * Returns true if this node is supported by the engine, rejection string otherwise.
     */
    public function accept() {
        return true; // Accepting anything by default.
    }

    /**
     * Creates an automaton corresponding to this node.
     * @param automaton - a reference to the automaton being built.
     * @param stack - a stack of arrays in the form of array('start' => $ref1, 'end' => $ref2),
     *                start and end states of parts of the resulting automaton.
     */
    abstract protected function create_automaton_inner(&$automaton, &$stack);

    public function __construct($node, $matcher) {
        $this->pregnode = $node;
    }

    public function create_automaton(&$automaton, &$stack) {
        $this->create_automaton_inner($automaton, $stack);

        // Don't augment transition if the node is not a subpattern.
        if ($this->pregnode->subpattern == -1) {
            return;
        }

        $body = array_pop($stack);

        // Copy this node to the starting transitions.
        foreach ($automaton->get_adjacent_transitions($body['start'], true) as $transition) {
            $tagset = $transition->tagsets[0];
            $tagset->tags[] = new qtype_preg_fa_tag(qtype_preg_fa_tag::TYPE_OPEN, $this->pregnode);
        }

        // Copy this node to the ending transitions.
        foreach ($automaton->get_adjacent_transitions($body['end'], false) as $transition) {
            if ($transition->to === $body['end']) {
                $tagset = end($transition->tagsets);
                $tagset->tags[] = new qtype_preg_fa_tag(qtype_preg_fa_tag::TYPE_CLOSE, $this->pregnode);
            }
        }

        $stack[] = $body;
    }

    /**
     * Merging transitions without merging states.
     *
     * @param del - uncapturing transition for deleting.
     */
    static public function go_round_transitions($automaton, $del, $endstates) {
        $clonetransitions = array();
        $tagsets = array();
        $oppositetransitions = array();
        $outtransitions = $automaton->get_adjacent_transitions($del->to, true);
        if ($del->from == $del->to && in_array($del->to, $endstates))
        {
            return false;
        }
        if (($del->is_unmerged_assert() && $del->pregleaf->is_start_anchor()) || ($del->is_eps() && in_array($del->to, $endstates))) {
            $transitions = $automaton->get_adjacent_transitions($del->from, false);
        } else {
            $transitions = $automaton->get_adjacent_transitions($del->to, true);
            if ($del->is_unmerged_assert() && $del->pregleaf->is_both_anchor()) {
                $oppositetransitions = $automaton->get_adjacent_transitions($del->from, false);
                if (empty($oppositetransitions)) {
                    return false;
                }
                // Copy after-tags.
                $sets = array();
                foreach ($del->tagsets as &$set) {
                    $copytags = array();
                    foreach ($set->tags as $tag) {
                        if ($tag->pos == qtype_preg_fa_tag_set::POS_AFTER_TRANSITION) {
                            $copytags[] = $tag;
                            unset($set->tags[array_search($tag, $set->tags)]);
                        }
                    }
                    $newset = new qtype_preg_fa_tag_set(qtype_preg_fa_tag_set::POS_AT_TRANSITION);  // TODO!!!
                    $newset->tags = $copytags;
                    $sets[] = $newset;
                    if (empty($set->tags)) {
                        unset($del->tagsets[array_search($set, $del->tagsets)]);
                    }
                }
                foreach ($oppositetransitions as $opposite) {
                    // Copy after assertions.
                    $leaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
                    $leaf->assertionsafter = $del->pregleaf->assertionsafter;
                    $newleaf = $opposite->pregleaf->intersect_asserts($leaf);
                    $opposite->pregleaf = $newleaf;
                    $del->pregleaf->assertionsafter = array();

                    $tagsets = array_merge($opposite->tagsets, $sets);
                    $opposite->tagsets = $tagsets;
                }
            }
        }
        // Changing leafs in case of merging.
        foreach ($transitions as $transition) {
            //if ($transition->from != $transition->to || $transition->is_merged() == false) {
                $tran = clone($transition);
                $delclone = clone $del;
                $tran->greediness = qtype_preg_fa_transition::min_greediness($tran->greediness, $del->greediness);
                $tagsets = array();
                // Work with tags.
                if ($del->is_unmerged_assert() && $del->pregleaf->is_start_anchor() || ($del->is_eps() && in_array($del->to, $endstates))) {
                    foreach ($delclone->tagsets as &$set) {
                        //$del->get_label_for_dot($del->from, $del->to);
                        $set->set_tags_position(qtype_preg_fa_tag_set::POS_AFTER_TRANSITION);
                    }
                    $tagsets = array_merge($tran->tagsets, $delclone->tagsets);
                } else {
                    foreach ($delclone->tagsets as &$set) {
                        //$del->get_label_for_dot($del->from, $del->to);
                        $set->set_tags_position(qtype_preg_fa_tag_set::POS_BEFORE_TRANSITION);
                    }
                    $tagsets = array_merge($delclone->tagsets, $tran->tagsets);
                }
                $newleaf = $tran->pregleaf->intersect_asserts($del->pregleaf);
                $tran->pregleaf = $newleaf;
                $tran->tagsets = $tagsets;

                $clonetransitions[] = $tran;
            //}
        }
        // Has deleting or changing transitions.
        if (count($transitions) != 0) {
            if (!($del->is_unmerged_assert() && $del->pregleaf->is_start_anchor()) && !($del->is_eps() && in_array($del->to, $endstates))) {
                foreach ($clonetransitions as $tran) {
                    $tran->from = $del->from;
                    $tran->make_merged();
                    $automaton->add_transition($tran);
                }
            } else {
                foreach ($clonetransitions as $tran) {
                    $tran->to = $del->to;
                    $tran->make_merged();
                    $automaton->add_transition($tran);

                }
            }
            if (!($del->is_end_anchor() && in_array($del->to, $endstates))) {
                $automaton->remove_transition($del);
            }
            return true;
        }
        return false;
    }


    public static function get_wordbreaks_transitions($negative, $isinto) {
        $result = array();
        // Create transitions which can replace \b and \B.
        // Create \w.
        $flagw = new qtype_preg_charset_flag();
        $flagw->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_W);
        $charsetw = new qtype_preg_leaf_charset();
        $charsetw->flags = array(array($flagw));
        $charsetw->userinscription = array(new qtype_preg_userinscription("\w", qtype_preg_charset_flag::SLASH_W));
        $tranw = new qtype_preg_fa_transition(0, $charsetw, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);
        // Create \W.
        $flagbigw = clone $flagw;
        $flagbigw->negative = true;
        $charsetbigw = new qtype_preg_leaf_charset();
        $charsetbigw->flags = array(array($flagbigw));
        $charsetbigw->userinscription = array(new qtype_preg_userinscription("\W", qtype_preg_charset_flag::SLASH_W));
        $tranbigw = new qtype_preg_fa_transition(0, $charsetbigw, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);
        // Create ^.
        $assertcircumflex = new qtype_preg_leaf_assert_circumflex();
        $transitioncircumflex = new qtype_preg_fa_transition(0, $assertcircumflex, 1);
        // Create $.
        $assertdollar = new qtype_preg_leaf_assert_dollar();
        $transitiondollar = new qtype_preg_fa_transition(0, $assertdollar, 1);

        // Incoming transitions.
        if ($isinto) {
            $result[] = $tranw;
            $result[] = $tranbigw;
            $result[] = $transitioncircumflex;
            // Case \b.
            if (!$negative) {
                $result[] = $tranw;
            } else {
                // Case \B.
                $result[] = $tranbigw;
            }
        } else {
            // Outcoming transitions.
            // Case \b.
            if (!$negative) {
                $result[] = $tranbigw;
                $result[] = $tranw;
                $result[] = $tranw;
            } else {
                // Case \B.
                $result[] = $tranw;
                $result[] = $tranbigw;
                $result[] = $tranbigw;
            }
            $result[] = $transitiondollar;
        }
        return $result;
    }

    public static function merge_wordbreaks($tran, $automaton) {
        printf($tran->get_label_for_dot($tran->from, $tran->to));
        printf($automaton->fa_to_dot());
        $fromdel = true;
        $todel = true;
        $outtransitions = $automaton->get_adjacent_transitions($tran->to, true);
        $intotransitions = $automaton->get_adjacent_transitions($tran->from, false);
        //$startstates = $this->start_states();

        // Add empty transitions if ot's nessesaary.
        if (count($outtransitions) == 0) {
            $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition($tran->to, $pregleaf, 1, $tran->origin, $tran->consumeschars);
            $outtransitions[] = $transition;
            $todel = false;
        }
        if (count($intotransitions) == 0) {
            $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition(0, $pregleaf, $tran->from, $tran->origin, $tran->consumeschars);
            $intotransitions[] = $transition;
            $fromdel = false;
        }

        $wordbreakinto = self::get_wordbreaks_transitions($tran->pregleaf->negative, true);
        $wordbreakout = self::get_wordbreaks_transitions($tran->pregleaf->negative, false);

        // Intersect transitions.
        for ($i = 0; $i < count($wordbreakinto); $i++) {
            foreach ($intotransitions as $intotran) {
                var_dump($intotran->get_label_for_dot($intotran->from, $intotran->to));
                var_dump($wordbreakinto[$i]->get_label_for_dot($wordbreakinto[$i]->from, $wordbreakinto[$i]->to));
                $resultinto = $intotran->intersect($wordbreakinto[$i]);
                if ($resultinto !== null) {
                    var_dump(true);
                    var_dump($resultinto->get_label_for_dot($resultinto->from, $resultinto->to));
                    foreach ($outtransitions as $outtran) {
                        $resultout = $outtran->intersect($wordbreakout[$i]);
                        if ($resultout !== null) {
                            var_dump('aaaaaaaaaa');
                            // Add state and transition
                            $state = $automaton->add_state();
                            $resultinto->from = $intotran->from;
                            $resultinto->to = $intotran->to;
                            $resultout->from = $tran->from;
                            $resultout->to = $outtran->to;
                            $automaton->add_transition($resultinto);
                            $automaton->add_transition($resultout);
                            /*if ($fromdel) {
                                // Copy transitions from deleting states.
                                $copiedout = $automaton->get_adjacent_transitions($tran->from, true);
                                foreach ($copiedout as $copytran) {
                                    if ($copytran !== $tran) {
                                        $copytran->from = $state;
                                        $automaron->add_transition($copytran);
                                    }
                                }
                            }
                            if ($todel) {
                                // Copy transitions from deleting states.
                                $copiedinto = $automaton->get_adjacent_transitions($tran->to, false);
                                foreach ($copiedinto as $copytran) {
                                    if ($copytran !== $tran) {
                                        $copytran->to = $state;
                                        $automaton->add_transition($copytran);
                                    }
                                }
                            }*/
                            // If result should be one cycled state.
                            /*if ($intotran->from == $tran->to) {
                                $resulttran = new qtype_preg_fa_transition($state, $resultinto->pregleaf, $state, $tran->origin, $tran->consumeschars);
                                $automaton->add_transition($resulttran);
                            } else {
                                $resulttran = new qtype_preg_fa_transition($intotran->from, $resultinto->pregleaf, $state, $tran->origin, $tran->consumeschars);
                                $automaton->add_transition($resulttran);
                                $resulttran = new qtype_preg_fa_transition($state, $resultout->pregleaf, $outtran->to, $tran->origin, $tran->consumeschars);
                                $automaton->add_transition($resulttran);
                            }*/
                        }
                    }
                }
            }
        }
        $automaton->remove_transition($tran);
    }
}

/**
 * Class for leafs. They contruct trivial FAs with two states and one transition between them.
 */
class qtype_preg_fa_leaf extends qtype_preg_fa_node {

    public function accept() {
        if ($this->pregnode->subtype == qtype_preg_leaf_assert::SUBTYPE_ESC_G) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        // Create start and end states of the resulting automaton.
        $start = $automaton->add_state();
        $end = $automaton->add_state();

        // Add a corresponding transition between them.
        $automaton->add_transition(new qtype_preg_fa_transition($start, $this->pregnode, $end));

        $stack[] = array('start' => $start, 'end' => $end);
    }
}

/**
 * Abstract class for nodes, they construct FAs by combining existing FAs.
 */
abstract class qtype_preg_fa_operator extends qtype_preg_fa_node {

    public $operands = array();    // Array of operands.

    public function __construct($node, $matcher) {
        parent::__construct($node, $matcher);
        foreach ($this->pregnode->operands as $operand) {
            $this->operands[] = $matcher->from_preg_node($operand);
        }
    }

    protected static function add_ending_eps_transition_if_needed(&$automaton, &$stack_item) {
        $outgoing = $automaton->get_adjacent_transitions($stack_item['end'], true);
        if (!empty($outgoing)) {
            $end = $automaton->add_state();
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $automaton->add_transition(new qtype_preg_fa_transition($stack_item['end'], $epsleaf, $end));
            $stack_item['end'] = $end;
        }
        $incoming = $automaton->get_adjacent_transitions($stack_item['end'], false);
        foreach ($incoming as $transition) {
            $transition->set_transition_type(); 
            if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS)
            {
                qtype_preg_fa_node::go_round_transitions($automaton, $transition, array($stack_item['end']));
            }
        }
    }

    protected static function merge_after_concat(&$automaton, &$stack_item, $borderstate) {
        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);

        foreach ($incoming as $transition) {
            $transition->set_transition_type();
            if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT) {
                qtype_preg_fa_node::go_round_transitions($automaton, $transition, array($stack_item['end']));
            }
            if ($transition->is_wordbreak()) {
                qtype_preg_fa_node::merge_wordbreaks($transition, $automaton);
            }
        }
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($outgoing as $transition) {
            $transition->set_transition_type();
            if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT) {
                qtype_preg_fa_node::go_round_transitions($automaton, $transition, array($stack_item['end']));
            }
            if ($transition->is_wordbreak()) {
                qtype_preg_fa_node::merge_wordbreaks($transition, $automaton);
            }
        }
    }

    protected static function concatenate(&$automaton, &$stack, $count) {
        if ($count < 2) {
            return;
        }
        $result = array_pop($stack);
        for ($i = 0; $i < $count - 1; $i++) {
            $cur = array_pop($stack);
            $automaton->redirect_transitions($cur['end'], $result['start']);
            $borderstate = $result['start'];
            $result = array('start' => $cur['start'], 'end' => $result['end']);
            self::merge_after_concat($automaton, $result, $borderstate);
        }
        $stack[] = $result;
    }
}

/**
 * Class for concatenation.
 */
class qtype_preg_fa_node_concat extends qtype_preg_fa_operator {

    protected function create_automaton_inner(&$automaton, &$stack) {
        $count = count($this->operands);
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack);
        }
        self::concatenate($automaton, $stack, $count);
    }
}

/**
 * Class for alternation.
 */
class qtype_preg_fa_node_alt extends qtype_preg_fa_operator {

    protected function create_automaton_inner(&$automaton, &$stack) {
        $count = count($this->operands);
        $result = null;

        // Create automata for operands and alternate them.
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack);
            $cur = array_pop($stack);
            self::add_ending_eps_transition_if_needed($automaton, $cur);  // Necessary if there's a quantifier in the end.
            if ($result === null) {
                $result = $cur;
            } else {
                // Merge start and end states.
                $automaton->redirect_transitions($cur['start'], $result['start']);
                $automaton->redirect_transitions($cur['end'], $result['end']);
            }
        }

        $stack[] = $result;
    }

}

/**
 * Class containing common methods for both finite and infinite quantifiers.
 */
abstract class qtype_preg_fa_node_quant extends qtype_preg_fa_operator {

    public function accept() {
        if ($this->pregnode->possessive) {
            return get_string('possessivequant', 'qtype_preg');
        }
        return true;
    }

    protected function mark_transitions($automaton, $startstate, $endstate) {
        $outgoing = $automaton->get_adjacent_transitions($startstate, true);
        $incoming = $automaton->get_adjacent_transitions($endstate, false);
        foreach ($outgoing as $transition) {
            $transition->startsquantifier = true;
        }
        foreach ($incoming as $transition) {
            $transition->endsquantifier = true;
        }
    }
}

/**
 * Class for infinite quantifiers (*, +, {m,}).
 */
class qtype_preg_fa_node_infinite_quant extends qtype_preg_fa_node_quant {

    /**
     * Creates an automaton for * or {0,} quantifier.
     */
    private function create_aster(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);
        $body = array_pop($stack);

        // Now, clone all transitions from the start state to the end state.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;        // Set this field for transitions, including original body.
            $newtransition = clone $transition;
            $newtransition->from = $body['end'];
            $newtransition->loopsback = true;
            $newtransition->set_transition_type();
            $automaton->add_transition($newtransition);
            if ($newtransition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $newtransition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT)
            {
                qtype_preg_fa_node::go_round_transitions($automaton, $newtransition, array($body['end']));
            }
        }

        $prevtrans = $automaton->get_adjacent_transitions($body['end'], false);
        foreach ($prevtrans as $transition) {
            $transition->set_transition_type();
            if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT) {
                qtype_preg_fa_node::go_round_transitions($automaton, $transition,  array($body['end']));
            }
        }
            
        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_fa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m,} quantifier
     */
    private function create_brace(&$automaton, &$stack) {
        // Operand creates its automaton m times.
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack);
            // The last block is repeated.
            if ($i == $leftborder - 1) {
                $cur = array_pop($stack);
                $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
                $outgoing = $automaton->get_adjacent_transitions($cur['start'], true);
                foreach ($outgoing as $transition) {
                    $newtransition = clone $transition;
                    $realgreediness = qtype_preg_fa_transition::min_greediness($newtransition->greediness, $greediness);
                    $newtransition->greediness = $realgreediness; // Set this field only for the last repetition.
                    $newtransition->from = $cur['end'];
                    $newtransition->loopsback = true;
                    $automaton->add_transition($newtransition);
                    $newtransition->set_transition_type();
                    if ($newtransition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $newtransition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT)
                        qtype_preg_fa_node::go_round_transitions($automaton, $newtransition, array($cur['end']));
                }

                $prevtrans = $automaton->get_adjacent_transitions($cur['end'], false);
                foreach ($prevtrans as $transition) {
                    $transition->set_transition_type();
                    if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT) {
                        qtype_preg_fa_node::go_round_transitions($automaton, $transition, array($cur['end']));
                    }
                }

                $stack[] = $cur;
            }
        }
        // Concatenate operands.
        self::concatenate($automaton, $stack, $leftborder);
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        if ($this->pregnode->leftborder == 0) {
            $this->create_aster($automaton, $stack);
        } else {
            $this->create_brace($automaton, $stack);
        }
        $body = array_pop($stack);
        $this->mark_transitions($automaton, $body['start'], $body['end']);

        $stack[] = $body;
    }
}

/**
 * Class for finite quantifiers {m,n}.
 */
class qtype_preg_fa_node_finite_quant extends qtype_preg_fa_node_quant {

    /**
     * Creates an automaton for ? quantifier.
     */
    private function create_qu(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);
        $body = array_pop($stack);

        // Set the greediness.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;
        }
        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_fa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);

        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m, n} quantifier.
     */
    private function create_brace(&$automaton, &$stack) {
        // Operand creates its automaton n times.
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;

        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack);
            if ($i >= $leftborder) {
                $cur = array_pop($stack);
                self::add_ending_eps_transition_if_needed($automaton, $cur);
                $stack[] = $cur;
            }
        }
        $endstate = end($stack);
        $endstate = $endstate['end'];

        // Add eps-transitions to the end state. Set greediness.
        $quantified = array();
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        for ($i = 0; $i < $rightborder - $leftborder; $i++) {
            $cur = array_pop($stack);
            $outgoing = $automaton->get_adjacent_transitions($cur['start'], true);
            foreach ($outgoing as $transition) {
                $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
                $transition->greediness = $realgreediness;
            }
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition($cur['start'], $epsleaf, $endstate);
            $automaton->add_transition($transition);
            $transition->set_transition_type();
            if ($transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_EPS || $transition->type == qtype_preg_fa_transition::TYPE_TRANSITION_ASSERT) {
                qtype_preg_fa_node::go_round_transitions($automaton, $transition, array($cur['end']));
            }
            $quantified[] = $cur;
        }
        for ($i = 0; $i < $rightborder - $leftborder; $i++) {
            $cur = array_pop($quantified);
            $stack[] = $cur;
        }
        self::concatenate($automaton, $stack, $rightborder);
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        if ($this->pregnode->rightborder == 0) {
            $this->operands[0]->create_automaton($automaton, $stack);
            $body = array_pop($stack);

            $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
            foreach ($outgoing as $transition) {
                $transition->greediness = qtype_preg_fa_transition::GREED_ZERO;
            }

            // The body automaton can be skipped by a greedy eps-transition.
            self::add_ending_eps_transition_if_needed($automaton, $body);
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition($body['start'], $epsleaf, $body['end']);
            $automaton->add_transition($transition);

            $stack[] = $body;
        } else if ($this->pregnode->leftborder == 0 && $this->pregnode->rightborder == 1) {
            $this->create_qu($automaton, $stack);
        } else {
            $this->create_brace($automaton, $stack);
        }
        $body = array_pop($stack);
        $this->mark_transitions($automaton, $body['start'], $body['end']);

        $stack[] = $body;
    }
}

/**
 * Class for subexpressions.
 */
class qtype_preg_fa_node_subexpr extends qtype_preg_fa_operator {

    public function accept() {
        if ($this->pregnode->subtype == qtype_preg_node_subexpr::SUBTYPE_ONCEONLY) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack);

        if ($this->pregnode->subpattern != -1) {
            $automaton->on_subexpr_added($this->pregnode, end($stack));
        }
    }
}

/**
 * Class for conditional subexpressions.
 */
class qtype_preg_fa_node_cond_subexpr extends qtype_preg_fa_operator {

    public function __construct($node, $matcher) {
        $this->pregnode = $node;

        $shift = (int)$node->is_condition_assertion();

        // Add an eps leaf if there's only positive branch.
        if (count($node->operands) - $shift == 1) {
            $node->operands[] = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        }

        $concatpos = new qtype_preg_node_concat();
        $concatpos->operands[] = new qtype_preg_leaf_assert_subexpr_captured(false, $node->number);
        $concatpos->operands[] = $node->operands[0 + $shift];

        $concatneg = new qtype_preg_node_concat();
        $concatneg->operands[] = new qtype_preg_leaf_assert_subexpr_captured(true, $node->number);
        $concatneg->operands[] = $node->operands[1 + $shift];

        $alt = new qtype_preg_node_alt();
        $alt->operands[] = $concatpos;
        $alt->operands[] = $concatneg;

        $grouping = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $grouping->subpattern = $node->subpattern;
        $grouping->operands[] = $alt;

        $this->operands = array($matcher->from_preg_node($grouping));
    }

    public function accept() {
        if ($this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack) {
        $this->operands[0]->create_automaton($automaton, $stack);
    }
}
