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
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');

/**
 * Abstract class for both nodes (operators) and leafs (operands).
 */
abstract class qtype_preg_fa_node {

    public $pregnode;    // Corresponding AST node.

    public $matcher;     // FA matcher.

    /**
     * Returns true if this node is supported by the engine, rejection string otherwise.
     */
    public function accept() {
        return true; // Accepting anything by default.
    }

    /**
     * Creates an automaton corresponding to this node.
     * @param automaton - a reference to the automaton being built.
     * @param stack - a stack of arrays in the form of array('start' => $ref1, 'end' => $ref2, 'breakpos' => nullable qtype_preg_position),
     *                start state
     *                end state
     *                position in the regex leading to the broken FA (when assertions merging is turned on)
     * @param transform - if true, perform transformations such as assertion merging
     */
    abstract protected function create_automaton_inner(&$automaton, &$stack, $transform);

    public function __construct($node, $matcher) {
        $this->pregnode = $node;
        $this->matcher = $matcher;
    }

    /**
     * Adds the opening tag for this node. Tricky when $transform === true.
     */
    protected function add_open_tag($transition, $transform) {
        //echo "\nthis node: {$this->pregnode->subpattern}\n";
        //echo "main transition: {$transition->pregleaf->subpattern}\n";
        $thetransition = $transition;

        if ($transform) {
            $thedelta = null;

            $search = $transition->consumeschars
                    ? array_merge($transition->mergedbefore, array($transition))
                    : $transition->mergedafter;

            // Look through all merged transitions and find one with minimal subpattern number.
            foreach ($search as $merged) {
                if (/*$merged->pregleaf->subpattern < $this->pregnode->subpattern/*/$merged->pregleaf->subpattern === -1) {
                   // continue;
                }
                $newdelta = $merged->pregleaf->subpattern - $this->pregnode->subpattern;
                if ($thedelta === null || $newdelta < $thedelta) {
                    $thetransition = $merged;
                    $thedelta = $newdelta;
                }
            }
        }

        $thetransition->opentags[] = $this->pregnode;

        if ($this->pregnode->subpattern !== -2 &&
            ($thetransition->minopentag === null || $this->pregnode->subpattern < $thetransition->minopentag->subpattern)) {
            $thetransition->minopentag = $this->pregnode;
        }
    }

    /**
     * Adds the closing tag for this node. Tricky when $transform === true.
     */
    protected function add_close_tag($transition, $transform) {
        $thetransition = $transition;

        if ($transform) {
            $thedelta = null;

            $search = $transition->consumeschars
                    ? array_merge(array($transition), $transition->mergedafter)
                    : $transition->mergedbefore;

            // Look through all merged transitions and fine one with maximal subpattern number.
            foreach ($search as $merged) {
                if (/*$merged->pregleaf->subpattern > */$merged->pregleaf->subpattern === -1) {
                  //  continue;
                }
                $newdelta = $merged->pregleaf->subpattern - $this->pregnode->subpattern;
                if ($thedelta === null || $newdelta > $thedelta) {
                    $thetransition = $merged;
                    $thedelta = $newdelta;
                }
            }
        }

        $thetransition->closetags[] = $this->pregnode;
    }

    public function create_automaton(&$automaton, &$stack, $transform) {
        $this->create_automaton_inner($automaton, $stack, $transform);

        // Don't augment transition if the node is not a subpattern.
        if ($this->pregnode->subpattern === -1) {
            return;
        }

        $body = array_pop($stack);

        // Copy this node to the starting transitions.
        foreach ($automaton->get_adjacent_transitions($body['start'], true) as $transition) {
            $this->add_open_tag($transition, $transform);
        }

        // Copy this node to the ending transitions.
        foreach ($automaton->get_adjacent_transitions($body['end'], false) as $transition) {
            $this->add_close_tag($transition, $transform);
        }

        $stack[] = $body;
    }

    protected static function check_connection($automaton, $fromstates, $tostates) {
        foreach ($fromstates as $from) {
            foreach ($tostates as $to) {
                if ($automaton->has_transition($from, $to)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Merging transitions without merging states.
     *
     * @param del - uncapturing transition for deleting.
     */
    static public function merge_transitions($automaton, $del, &$stackitem, $back = null) {
        $clonetransitions = array();
        $tagsets = array();
        $fromstates = array();
        $tostates = array();
        $oppositetransitions = array();
        $intersection = null;
        $transitionadded = false;
        $flag = new qtype_preg_charset_flag();
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion_string("\n"));
        $charset = new qtype_preg_leaf_charset();
        $charset->flags = array(array($flag));
        $charset->userinscription = array(new qtype_preg_userinscription("\n"));
        $righttran = new qtype_preg_fa_transition(0, $charset, 1);
        $outtransitions = $automaton->get_adjacent_transitions($del->to, true);
        $endstates = array($stackitem['end']);

        // Cycled last states.
        if ((in_array($del->to, $endstates) && $del->is_eps()) || !$del->consumeschars) {
            return false;
        }

        if ($back === null) {

            // Get transitions for merging back.
            if (($del->is_unmerged_assert() && $del->is_start_anchor()) || ($del->is_eps() && in_array($del->to, $endstates))) {
                $transitions = $automaton->get_adjacent_transitions($del->from, false);
                $tostates[] = $del->to;
                $back = true;
            } else {
                // Get transitions for merging forward.
                $transitions = $automaton->get_adjacent_transitions($del->to, true);
                $fromstates[] = $del->from;
                $back = false;
            }
        } else {

            if ($back) {
                $transitions = $automaton->get_adjacent_transitions($del->from, false);
            } else {
                $transitions = $automaton->get_adjacent_transitions($del->to, true);
            }
        }

        // Changing leafs in case of merging.
        foreach ($transitions as $transition) {
            if (!($transition->from === $transition->to && ($transition->is_unmerged_assert() || $transition->is_eps()))) {
                $tran = clone $transition;
                $delclone = clone $del;
                $tran->loopsback = $transition->loopsback || $del->loopsback;
                $tran->greediness = qtype_preg_fa_transition::min_greediness($tran->greediness, $del->greediness);
                $merged = array_merge($delclone->mergedbefore, array($delclone), $delclone->mergedafter);
                // Work with tags.
                if (!$tran->consumeschars && $del->is_eps() && $del->from !== $del->to) {
                    if ($back) {
                        $tran->mergedbefore = array_merge($tran->mergedbefore, $merged);
                    } else {
                        $tran->mergedafter = array_merge($merged, $tran->mergedafter);
                    }
                } else if ($back) {
                    $tran->mergedafter = array_merge($tran->mergedafter, $merged);
                } else {
                    $tran->mergedbefore = array_merge($merged, $tran->mergedbefore);
                }

                $clonetransitions[] = $tran;
            }

        }
        // Has deleting or changing transitions.
        if (empty($transitions)) {
            return false;
        }

        $breakpos = null;
        if (!$back) {
            foreach ($clonetransitions as &$tran) {
                $tostates[] = $tran->to;
                if ($del->is_end_anchor() && !$tran->is_unmerged_assert() && !$tran->is_eps()) {
                    $righttran->pregleaf->position = $tran->pregleaf->position;
                    $intersection = $tran->intersect($righttran);
                    if ($intersection !== null) {
                        $tran->pregleaf = $intersection->pregleaf;
                    }
                }

                if (($del->pregleaf->subtype !== qtype_preg_leaf_assert::SUBTYPE_SMALL_ESC_Z && $intersection !== null) ||
                    !$del->is_end_anchor() || $tran->is_unmerged_assert() || $tran->is_eps()) {
                    $tran->from = $del->from;
                    $tran->redirect_merged_transitions();
                    $automaton->add_transition($tran);
                    $transitionadded = true;
                } else if ($breakpos === null) {
                    $breakpos = $del->pregleaf->position->compose($tran->pregleaf->position);
                }
            }
        } else {
            foreach ($clonetransitions as &$tran) {
                $fromstates[] = $tran->from;
                if ($del->is_start_anchor() && !$tran->is_unmerged_assert() && !$tran->is_eps()) {
                    $righttran->pregleaf->position = $tran->pregleaf->position;
                    $intersection = $tran->intersect($righttran);
                    if ($intersection !== null) {
                        $tran->pregleaf = $intersection->pregleaf;
                    }
                }
                if (($del->pregleaf->subtype !== qtype_preg_leaf_assert::SUBTYPE_ESC_A && $intersection !== null) ||
                    !$del->is_start_anchor() || $tran->is_unmerged_assert() || $tran->is_eps()) {
                    $tran->to = $del->to;
                    $tran->redirect_merged_transitions();
                    $automaton->add_transition($tran);
                    $transitionadded = true;
                } else if ($breakpos === null) {
                    $breakpos = $tran->pregleaf->position->compose($del->pregleaf->position);
                }
            }
        }

        if (!($del->is_end_anchor() && in_array($del->to, $endstates)) && !($transition->from === $transition->to && ($transition->is_unmerged_assert() || $transition->is_eps()))) {
            $automaton->remove_transition($del);
        }

        $hastransitions = self::check_connection($automaton, $fromstates, $tostates);
        $stackitem['breakpos'] = ($transitionadded || $hastransitions) ? null : $breakpos;

        return true;
    }


    public static function get_wordbreaks_transitions($pregleaf, $isinto) {
        $result = array();
        // Create transitions which can replace \b and \B.
        // Create \w.
        $flagw = new qtype_preg_charset_flag();
        $flagw->set_data(qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_W);
        $charsetw = new qtype_preg_leaf_charset();
        $charsetw->flags = array(array($flagw));
        $charsetw->set_user_info($pregleaf->position, array(new qtype_preg_userinscription("\w", qtype_preg_charset_flag::SLASH_W)));
        $tranw = new qtype_preg_fa_transition(0, $charsetw, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);
        // Create \W.
        $flagbigw = clone $flagw;
        $flagbigw->negative = true;
        $charsetbigw = new qtype_preg_leaf_charset();
        $charsetbigw->flags = array(array($flagbigw));
        $charsetbigw->set_user_info($pregleaf->position, array(new qtype_preg_userinscription("\W", qtype_preg_charset_flag::SLASH_W)));
        $tranbigw = new qtype_preg_fa_transition(0, $charsetbigw, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);
        // Create ^.
        $assertcircumflex = new qtype_preg_leaf_assert_circumflex();
        $assertcircumflex->set_user_info($pregleaf->position);
        $transitioncircumflex = new qtype_preg_fa_transition(0, $assertcircumflex, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);
        // Create $.
        $assertdollar = new qtype_preg_leaf_assert_dollar();
        $assertdollar->set_user_info($pregleaf->position);
        $transitiondollar = new qtype_preg_fa_transition(0, $assertdollar, 1, qtype_preg_fa_transition::ORIGIN_TRANSITION_FIRST, false);

        if ($isinto) {
            // Incoming transitions.
            $result[] = $tranw;
            $result[] = $tranbigw;
            $result[] = $transitioncircumflex;
            if ($pregleaf->negative) {
                // Case \B.
                $result[] = $tranbigw;
            } else {
                // Case \b.
                $result[] = $tranw;
            }
        } else {
            // Outcoming transitions.
            if ($pregleaf->negative) {
                // Case \B.
                $result[] = $tranw;
                $result[] = $tranbigw;
                $result[] = clone $tranbigw;
            } else {
                // Case \b.
                $result[] = $tranbigw;
                $result[] = $tranw;
                $result[] = clone $tranw;
            }
            $result[] = $transitiondollar;
        }
        return $result;
    }

    public static function merge_wordbreaks($tran, $automaton, &$stack_item, $changeend = true) {
        $fromdel = true;
        $todel = true;
        $wasmerged = false;
        $outtransitions = $automaton->get_adjacent_transitions($tran->to, true);
        $intotransitions = $automaton->get_adjacent_transitions($tran->from, false);

        // Add empty transitions if ot's nessesaary.
        if (empty($outtransitions)) {
            $state = $automaton->add_state();
            $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition($tran->to, $pregleaf, $state, $tran->origin, $tran->consumeschars);
            $outtransitions[] = $transition;
            $todel = false;
            if ($changeend) {
                $stack_item['end'] = $state;
            }
        }
        if (empty($intotransitions)) {
            $state = $automaton->add_state();
            $pregleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $transition = new qtype_preg_fa_transition($state, $pregleaf, $tran->from, $tran->origin, $tran->consumeschars);
            $intotransitions[] = $transition;
            $fromdel = false;
            $stack_item['start'] = $state;
        }

        $wordbreakinto = self::get_wordbreaks_transitions($tran->pregleaf, true);
        $wordbreakout = self::get_wordbreaks_transitions($tran->pregleaf, false);
        foreach ($wordbreakinto as $wordbreak) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epstran = new qtype_preg_fa_transition($wordbreak->from, $epsleaf, $wordbreak->to);
            $epstran->opentags = $tran->opentags;
            $wordbreak->mergedafter[] = $epstran;
        }
        foreach ($wordbreakout as $wordbreak) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epstran = new qtype_preg_fa_transition($wordbreak->from, $epsleaf, $wordbreak->to);
            $epstran->closetags = $tran->closetags;
            $wordbreak->mergedbefore[] = $epstran;
        }

        // Intersect transitions.
        for ($i = 0; $i < count($wordbreakinto); $i++) {
            foreach ($intotransitions as $intotran) {
                $resultinto = $intotran->intersect($wordbreakinto[$i]);
                if ($resultinto !== null) {
                    foreach ($outtransitions as $outtran) {
                        $clone = clone $resultinto;
                        $resultout = $wordbreakout[$i]->intersect($outtran);
                        if ($resultout !== null) {
                            $state = $automaton->add_state();
                            $clone->from = $intotran->from;
                            $clone->to = $state;
                            $resultout->from = $state;
                            $resultout->to = $outtran->to;
                            $clone->redirect_merged_transitions();
                            $resultout->redirect_merged_transitions();
                            $automaton->add_transition(clone $clone);
                            $automaton->add_transition(clone $resultout);
                        }
                    }
                }
            }
        }
        // Remove repeated uncapturing transitions.
        $automaton->remove_transition($tran);
    }
}

/**
 * Class for leafs. They contruct trivial FAs with two states and one transition between them.
 */
class qtype_preg_fa_leaf extends qtype_preg_fa_node {

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        // Create start and end states of the resulting automaton.
        $start = $automaton->add_state();
        $end = $automaton->add_state();

        // Add a corresponding transition between them.
        $automaton->add_transition(new qtype_preg_fa_transition($start, $this->pregnode, $end));

        $stack[] = array('start' => $start, 'end' => $end, 'breakpos' => null);
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

    protected static function add_ending_eps_transition_if_needed(&$automaton, &$stack_item, $transform) {
        $outgoing = $automaton->get_adjacent_transitions($stack_item['end'], true);
        if (!empty($outgoing)) {
            $end = $automaton->add_state();
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $automaton->add_transition(new qtype_preg_fa_transition($stack_item['end'], $epsleaf, $end));
            $stack_item['end'] = $end;
        }
        if ($transform) {
            $incoming = $automaton->get_adjacent_transitions($stack_item['end'], false);
            foreach ($incoming as $transition) {
                $transition->set_transition_type();
                if ($transition->is_eps()) {
                    qtype_preg_fa_node::merge_transitions($automaton, $transition, $stack_item);
                }
            }
        }
    }

    private static function merge_before_intersection(&$automaton, &$stack_item, $borderstate) {
        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        foreach ($incoming as $transition) {
            if ($transition->is_eps() && $transition->from != $stack_item['start']) {
                qtype_preg_fa_node::merge_transitions($automaton, $transition, $stack_item, true);
            }
        }
    }

    protected static function merge_after_concat(&$automaton, &$stack_item, $borderstate, $changeend) {
        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($incoming as $transition) {
            $transition->set_transition_type();
            if ($transition->is_wordbreak()) {
                qtype_preg_fa_node::merge_wordbreaks($transition, $automaton, $stack_item, $changeend);
            }
        }
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($outgoing as $transition) {
            $transition->set_transition_type();

            if ($transition->is_wordbreak()) {
                qtype_preg_fa_node::merge_wordbreaks($transition, $automaton, $stack_item, $changeend);
            }
        }

        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        $breakpos = $stack_item['breakpos'];

        foreach ($outgoing as $transition) {
            if (!$transition->consumeschars) {
                self::merge_before_intersection($automaton, $stack_item, $borderstate);
                break;
            }
        }
        foreach ($incoming as $transition) {
            $transition->set_transition_type();
            if ($transition->is_eps() || $transition->is_unmerged_assert()) {
                qtype_preg_fa_node::merge_transitions($automaton, $transition, $stack_item);
            }

        }
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($outgoing as $transition) {
            $transition->set_transition_type();
            if ($transition->is_eps() || $transition->is_unmerged_assert()) {
                qtype_preg_fa_node::merge_transitions($automaton, $transition, $stack_item);
            }

        }
        if ($breakpos !== null) {
            $stack_item['breakpos'] = $breakpos;
        }
        self::intersect($borderstate, $automaton, $stack_item);
    }

    protected static function intersect($borderstate, $automaton, &$stackitem, $del = true) {
        $uncapturing = array();
        $hasintersect = false;
        $hastransitions = false;
        $changed = array();
        $tostates = array();
        $fromstates = array();
        // Uncapturing transitions are outgoing.
        // If one transition doesn't consume chars intersect it with other.
        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($outgoing as $tran) {
            if (!$tran->consumeschars) {
                $uncapturing[] = clone $tran;
            }
        }
        if (!empty($uncapturing)) {
            $breakpos = null;
            foreach ($incoming as $intran) {
                if ($intran->consumeschars) {
                    $fromstates[] = $intran->from;
                    foreach ($uncapturing as $tran) {
                        if (count($fromstates === 1)) {
                            $tostates[] = $tran->to;
                        }
                        $resulttran = $intran->intersect($tran);
                        if ($resulttran !== null) {
                            $hasintersect = true;
                            $resulttran->from = $intran->from;
                            $resulttran->to = $tran->to;
                            if ($del) {
                                $automaton->remove_transition($tran);
                            }
                            $resulttran->redirect_merged_transitions();
                            $automaton->add_transition($resulttran);
                            $changed[] = $resulttran->to;
                        } else if ($del) {
                            if ($breakpos === null) {

                                $breakpos = $intran->pregleaf->position->compose($tran->pregleaf->position);
                            }
                            $automaton->remove_transition($tran);
                        }
                    }
                    if (count($outgoing) === count($uncapturing)) {
                        $automaton->remove_transition($intran);
                    }
                }
            }
            $hastransitions = qtype_preg_fa_node::check_connection($automaton, $fromstates, $tostates);
            if (!$hasintersect && $breakpos !== null && !$hastransitions) {
                $stackitem['breakpos'] = $breakpos;
            }
            return $changed;
        }

        // Uncapturing transitions are incoming.
        // If one transition doesn't consume chars intersect it with other.
        $incoming = $automaton->get_adjacent_transitions($borderstate, false);
        $outgoing = $automaton->get_adjacent_transitions($borderstate, true);
        foreach ($incoming as $tran) {
            if (!$tran->consumeschars) {
                $uncapturing[] = clone $tran;
            }
        }
        if (!empty($uncapturing)) {
            $breakpos = null;
            foreach ($uncapturing as $tran) {
                $fromstates[] = $tran->from;
                foreach ($outgoing as $outtran) {
                    if ($outtran->consumeschars) {
                        if (count($fromstates === 1)) {
                            $tostates[] = $outtran->to;
                        }
                        $resulttran = $tran->intersect($outtran);
                        if ($resulttran !== null) {
                            $hasintersect = true;
                            $resulttran->from = $tran->from;
                            $resulttran->to = $outtran->to;
                            if ($del) {
                                $automaton->remove_transition($tran);
                            }
                            $resulttran->redirect_merged_transitions();
                            $automaton->add_transition($resulttran);
                            $changed[] = $resulttran->from;
                        } else if ($del) {
                            if ($breakpos === null) {
                                $breakpos = $tran->pregleaf->position->compose($outtran->pregleaf->position);
                            }
                            $automaton->remove_transition($tran);
                        }
                    }
                    if (count($incoming) === count($uncapturing)) {
                        $automaton->remove_transition($outtran);
                    }
                }
            }
            $hastransitions = qtype_preg_fa_node::check_connection($automaton, $fromstates, $tostates);
            if (!$hasintersect && $breakpos !== null && !$hastransitions) {
                $stackitem['breakpos'] = $breakpos;
            }
            return $changed;
        }
        return $changed;
    }

    protected static function concatenate(&$automaton, &$stack, $count, $transform) {
        if ($count < 2) {
            return;
        }
        $result = array_pop($stack);
        for ($i = 0; $i < $count - 1; $i++) {
            $cur = array_pop($stack);
            $before = false;
            $automaton->redirect_transitions($cur['end'], $result['start']);
            $borderstate = $result['start'];
            $breakpos = $result['breakpos'];
            if ($breakpos === null) {
                $breakpos = $cur['breakpos'];
            }

            $result = array('start' => $cur['start'], 'end' => $result['end'], 'breakpos' => $breakpos);
            $incoming = $automaton->get_adjacent_transitions($result['end'], false);
            foreach ($incoming as $tran) {
                if ($tran->is_wordbreak()) {
                    $before = true;
                }
            }

            if ($transform) {
                self::merge_after_concat($automaton, $result, $borderstate, $before);
            }
        }

        $stack[] = $result;
    }
}

/**
 * Class for concatenation.
 */
class qtype_preg_fa_node_concat extends qtype_preg_fa_operator {

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        $count = count($this->operands);
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack, $transform);
        }
        self::concatenate($automaton, $stack, $count, $transform);
    }
}

/**
 * Class for alternation.
 */
class qtype_preg_fa_node_alt extends qtype_preg_fa_operator {

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        $count = count($this->operands);
        $result = null;

        // Create automata for operands and alternate them.
        for ($i = 0; $i < $count; $i++) {
            $this->operands[$i]->create_automaton($automaton, $stack, $transform);
            $cur = array_pop($stack);
            self::add_ending_eps_transition_if_needed($automaton, $cur, $transform);  // Necessary if there's a quantifier in the end.
            if ($result === null) {
                $result = $cur;
            } else {
                // Merge start and end states.
                $automaton->redirect_transitions($cur['start'], $result['start']);
                $automaton->redirect_transitions($cur['end'], $result['end']);
                if ($cur['breakpos'] === null) {
                    $result['breakpos'] = null;
                }
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
    private function create_aster(&$automaton, &$stack, $transform) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack, $transform);
        $body = array_pop($stack);
        $prevtrans = $automaton->get_adjacent_transitions($body['end'], false);
        $redirectedtransitions = array();
        // Now, clone all transitions from the start state to the end state.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;        // Set this field for transitions, including original body.
            $newtransition = clone $transition;
            $newtransition->from = $body['end'];
            $newtransition->redirect_merged_transitions();
            $newtransition->loopsback = true;
            $newtransition->set_transition_type();
            $automaton->add_transition($newtransition);
            $redirectedtransitions[] = $transition;
            if ($transform && ($newtransition->is_eps() || $newtransition->is_unmerged_assert())) {
                qtype_preg_fa_node::merge_transitions($automaton, $newtransition, $body);
            }
        }

        $modified = $transform
                  ? self::intersect($body['end'], $automaton, $body, false)
                  : false;

        foreach ($prevtrans as $transition) {
            $transition->set_transition_type();
            if ($transform && ($transition->is_eps() || $transition->is_unmerged_assert())) {
                qtype_preg_fa_node::merge_transitions($automaton, $transition,  $body);
            }
        }

        // Change end states if automaton was rebuilt with intersection.
        if (!empty($modified)) {
            foreach ($prevtrans as $prev) {
                // Add transitions for coming from cycle without intersection with wordbreak.
                $newend = $automaton->add_state();
                $prev->to = $newend;
                $prev->redirect_merged_transitions();
                $automaton->add_transition($prev);
            }
            $body['end'] = $newend;
        }

        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body, $transform);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_fa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);
        $body['breakpos'] = null;
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m,} quantifier
     */
    private function create_brace(&$automaton, &$stack, $transform) {
        // Operand creates its automaton m times.
        $leftborder = $this->pregnode->leftborder;
        for ($i = 0; $i < $leftborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack, $transform);
            // The last block is repeated.
            if ($i === $leftborder - 1) {
                $cur = array_pop($stack);
                $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
                $outgoing = $automaton->get_adjacent_transitions($cur['start'], true);
                foreach ($outgoing as $transition) {
                    $newtransition = clone $transition;
                    $realgreediness = qtype_preg_fa_transition::min_greediness($newtransition->greediness, $greediness);
                    $newtransition->greediness = $realgreediness; // Set this field only for the last repetition.
                    $newtransition->from = $cur['end'];
                    $newtransition->loopsback = true;
                    $newtransition->redirect_merged_transitions();
                    $automaton->add_transition($newtransition);
                    $newtransition->set_transition_type();
                    if ($transform && $newtransition->is_wordbreak()) {
                        qtype_preg_fa_node::merge_wordbreaks($newtransition, $automaton, $cur, false);
                    }
                    if ($transform && ($newtransition->is_eps() || $newtransition->is_unmerged_assert())) {
                        qtype_preg_fa_node::merge_transitions($automaton, $newtransition, $cur);

                    }
                }

                $modified = $transform
                          ? self::intersect($cur['end'], $automaton, $cur, false)
                          : false;
                $prevtrans = $automaton->get_adjacent_transitions($cur['end'], false);
                foreach ($prevtrans as $transition) {
                    $transition->set_transition_type();
                    if ($transform && $newtransition->is_wordbreak()) {
                        qtype_preg_fa_node::merge_wordbreaks($newtransition, $automaton, $cur, false);
                    }
                    if ($transform && ($transition->is_eps() || $transition->is_unmerged_assert())) {
                        qtype_preg_fa_node::merge_transitions($automaton, $transition, $cur);
                        if ($transition->is_end_anchor()) {
                            $cur['breakpos'] = null;
                        }
                    }
                }

                $stack[] = $cur;
            }
        }
        // Concatenate operands.
        self::concatenate($automaton, $stack, $leftborder, $transform);
    }

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        if ($this->pregnode->leftborder === 0) {
            $this->create_aster($automaton, $stack, $transform);
        } else {
            $this->create_brace($automaton, $stack, $transform);
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
    private function create_qu(&$automaton, &$stack, $transform) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack, $transform);
        $body = array_pop($stack);

        // Set the greediness.
        $greediness = $this->pregnode->lazy ? qtype_preg_fa_transition::GREED_LAZY : qtype_preg_fa_transition::GREED_GREEDY;
        $outgoing = $automaton->get_adjacent_transitions($body['start'], true);
        foreach ($outgoing as $transition) {
            $realgreediness = qtype_preg_fa_transition::min_greediness($transition->greediness, $greediness);
            $transition->greediness = $realgreediness;
        }
        // The body automaton can be skipped by an eps-transition.
        self::add_ending_eps_transition_if_needed($automaton, $body, $transform);
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $transition = new qtype_preg_fa_transition($body['start'], $epsleaf, $body['end']);
        $automaton->add_transition($transition);
        $body['breakpos'] = null;
        $stack[] = $body;
    }

    /**
     * Creates an automaton for {m, n} quantifier.
     */
    private function create_brace(&$automaton, &$stack, $transform) {
        // Operand creates its automaton n times.
        $leftborder = $this->pregnode->leftborder;
        $rightborder = $this->pregnode->rightborder;

        for ($i = 0; $i < $rightborder; $i++) {
            $this->operands[0]->create_automaton($automaton, $stack, $transform);
            if ($i >= $leftborder) {
                $cur = array_pop($stack);
                self::add_ending_eps_transition_if_needed($automaton, $cur, $transform);
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
            if ($transform && ($transition->is_eps() || $transition->is_unmerged_assert())) {
                qtype_preg_fa_node::merge_transitions($automaton, $transition, $cur);
            }
            $quantified[] = $cur;
        }
        for ($i = 0; $i < $rightborder - $leftborder; $i++) {
            $cur = array_pop($quantified);
            $stack[] = $cur;
        }
        self::concatenate($automaton, $stack, $rightborder, $transform);
    }

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        if ($this->pregnode->rightborder === 0) {
            // Create start and end states of the resulting automaton.
            $start = $automaton->add_state();
            $end = $automaton->add_state();
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $automaton->add_transition(new qtype_preg_fa_transition($start, $epsleaf, $end));
            $stack[] = array('start' => $start, 'end' => $end, 'breakpos' => null);
        } else if ($this->pregnode->leftborder === 0 && $this->pregnode->rightborder === 1) {
            $this->create_qu($automaton, $stack, $transform);
        } else {
            $this->create_brace($automaton, $stack, $transform);
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
        if ($this->pregnode->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        // Operand creates its automaton.
        $this->operands[0]->create_automaton($automaton, $stack, $transform);

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
        parent::__construct($node, $matcher);

        $shift = (int)$node->is_condition_assertion();

        // Form the assertion nodes.
        switch ($this->pregnode->subtype) {
            case qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR:
                $assertpos = new qtype_preg_leaf_assert_subexpr(false, $node->number, $node->name);
                $assertneg = new qtype_preg_leaf_assert_subexpr(true, $node->number, $node->name);
                break;
            case qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION:
                $assertpos = new qtype_preg_leaf_assert_recursion(false, $node->number, $node->name);
                $assertneg = new qtype_preg_leaf_assert_recursion(true, $node->number, $node->name);
                break;
            case qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE:
                $assertpos = new qtype_preg_leaf_assert_truefalse(true);   // Positive branch should fail
                $assertneg = new qtype_preg_leaf_assert_truefalse(false);
                break;
            default:
                // WTF?
                $assertpos = null;
                $assertneg = null;
                break;
        }

        $concatpos = new qtype_preg_node_concat();
        $concatpos->operands[] = $assertpos;
        $concatpos->operands[] = $node->operands[0 + $shift];

        $concatneg = new qtype_preg_node_concat();
        $concatneg->operands[] = $assertneg;
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
        if ($this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR &&
            $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION &&
            $this->pregnode->subtype != qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        $this->operands[0]->create_automaton($automaton, $stack, $transform);
    }
}

/**
 * Class for lookaround assertions.
 */
class qtype_preg_fa_node_assert extends qtype_preg_fa_operator {

    public function accept() {
        // TODO; assertions are not supported yet.
        if ($this->pregnode->subtype === qtype_preg_node_assert::SUBTYPE_PLA ||
            $this->pregnode->subtype === qtype_preg_node_assert::SUBTYPE_NLA ||
            $this->pregnode->subtype === qtype_preg_node_assert::SUBTYPE_PLB ||
            $this->pregnode->subtype === qtype_preg_node_assert::SUBTYPE_NLB) {
            return get_string($this->pregnode->subtype, 'qtype_preg');
        }
        return true;
    }

    protected function create_automaton_inner(&$automaton, &$stack, $transform) {
        $automaton = $this->matcher->build_fa($this, $transform);

        // TODO: store it somewhere and intersect
    }
}