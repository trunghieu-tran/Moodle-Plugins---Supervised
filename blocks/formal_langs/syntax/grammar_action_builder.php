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
 * Describes an algorithm for building an action parser table
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_closure.php');

/**
 * Builds an action table builder
 */
class block_formal_langs_grammar_action_builder {
    /** An action table, which defined as a number of LR(1)-item state mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, stdClass action>, described as array
     *  @var array
     */
    protected $action;
    /**
     * A grammar, for which action table is built
     * @var block_formal_langs_grammar
     */
    protected $g;
    /**
     * A LR(1)-items, after same-kernel items is built
     * @var array of block_formal_langs_grammar_lr_one_item
     */
    protected $items;
    /** A LR(1)-item goto. It is stored as number of LR(1)-item set mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, number of set I for goto into>, described as array
     * LR(1)-item sets are stored in $lr1items
     * @var array
     */
    protected $lr1goto;

    /**
     * Constructs an action table
     * @param block_formal_langs_grammar $g a specified grammar
     * @param array $items superset of LR(1) items
     * @param array $lr1goto  goto table, build in tables
     */
    public function __construct($g, $items, $lr1goto) {
        $this->g = $g;
        $this->items = $items;
        $this->lr1goto = $lr1goto;
        if ($g->valid()) {
            $this->build_action_table();
        }
    }
    /**
     * Returns an action table, , which defined as a number of LR(1)-item state mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, stdClass action>, described as array
     * @return array
     */
    public function action() {
        return $this->action;
    }

    /** Creates a new accept action
     *  @param block_formal_langs_grammar_production_rule $rule starting rule
     *  @return stdClass
     */
    protected function create_accept_action($rule) {
        $a = new stdClass();
        $a->type = 'accept';
        $a->rule = $rule;
        return $a;
    }
    /** Creates a new shift action to state
     *  @param int $j new state, which parser must go to
     *  @return stdClass shift action
     */
    protected function create_shift_action($j) {
        $a = new stdClass();
        $a->type = 'shift';
        $a->goto = $j;
        return $a;
    }
    /** Creates a new reduce action
     *  @param block_formal_langs_grammar_production_rule $rule starting rule
     *  @return stdClass
     */
    protected function create_reduce_action($rule) {
        $a = new stdClass();
        $a->type = 'reduce';
        $a->rule = $rule;
        return $a;
    }
    /** Builds action table
     */
    protected function build_action_table() {
        $this->action = array();
        for($i = 0; $i < count($this->items); $i++) {
            $this->action[$i] = $this->build_actions_for_state($i, $this->items[$i], $this->lr1goto[$i]);
        }
    }
    /** Builds action table row for specified state
     *  @param int   $state state
     *  @param array $row LR(1) items for state
     *  @param array $goto goto table row for state
     *  @return array of actions as <block_formal_langs_production_symbol symbol,stdClass action>
     */
    protected function build_actions_for_state($state, $row, $goto) {
        // We define copyable container with referenced array
        // which a defined as followed
        $edefault = new stdClass();
        $edefault->a = array();
        $map = new block_formal_langs_grammar_symbol_map(array(),array(),$edefault,
                                                         block_formal_langs_grammar_symbol_map::$INSERT
                                                        );
        $actions = array();
        for($i = 0; $i < count($row); $i++) {
            $action = null;
            // Compute action, depending on row
            /** @var block_formal_langs_grammar_lr_one_item $rowi  */
            $rowi = $row[$i];
            $rule = $rowi->item();
            if ($rule->position() != $rule->rightcount()) {
                $sym = $rule->dotpart();
                $hasgoto = false;
                $gotostate = null;
                for($j = 0; $j < count($goto); $j++) {
                    /** @var block_formal_langs_grammar_production_symbol $gotojsymbol  */
                    $gotojsymbol = $goto[$j]['symbol'];
                    if ($gotojsymbol->is_same($sym)) {
                        $hasgoto = true;
                        $gotostate = $goto[$j]['goto'];
                    }
                }
                if ($hasgoto) {
                    $action = array('symbol' => $sym, 'action' => $this->create_shift_action($gotostate));
                }
            } else {
                if ($rowi->is_same($this->g->accept_lr1_item())) {
                    $action = $this->create_accept_action($rule->rule());
                } else {
                    $action = $this->create_reduce_action($rule->rule());
                }
                $action = array('symbol' => $rowi->symbol(), 'action' => $action);
            }
            if ($action != null) {
                $array = $map->get($action['symbol']);
                $contains = false;
                if (count($array->a)) {
                    /**
                     * @var stdClass $paction
                     */
                    foreach($array->a as $key => $paction) {
                        if ($this->is_same_action($action, $paction)) {
                            $contains = true;
                        }
                    }
                }
                if ($contains == false) {
                    $array->a[] = $action;
                }
            }
        }
        // Flatten list and resolve conflicts
        $flattenedactions = array();
        $keys = $map->keys();
        if (count($keys)) {
            foreach($keys as $iindex => $symbol) {
                $value = $map->get($symbol);
                // If some conflicts found, we must resolve them
                if (count($value->a) > 1) {
                    $actions = $value->a;
                    while(count($actions) > 1) {
                        $action1 = array_shift($actions);
                        $action2 = array_shift($actions);
                        $ok = false;
                        $resolveaction = $this->try_resolve_conflict($action1, $action2, $ok);
                        if ($ok == false) {
                            $this->create_grammar_conflict($state, $action1, $action2);
                        }  else {
                            if ($resolveaction != null) {
                                $actions[] = $resolveaction;
                            }
                        }
                    }
                    if (count($actions) != 0) {
                        $flattenedactions[] = $actions[0];
                    }
                }  else {
                    $flattenedactions[] = $value->a[0];
                }
            }
        }

        return $flattenedactions;
    }

    /**
     * Tries to resolve shift-reduce conflict between actions
     * @param array $action1  first action
     * @param array $action2  second action
     * @param bool $ok success flag
     * @return array action
     */
    protected function try_resolve_conflict($action1, $action2, &$ok) {
        $type1 = $action1['action']->type;
        $type2 = $action2['action']->type;
        $result = null;
        if (($type1 == 'shift') && ($type2 == 'shift') ||
            ($type1 == 'reduce') && ($type2 == 'reduce')
            ) {
            $ok = false;
            $result = $action1;
        } else {
            $shiftaction = $action1;
            $reduceaction = $action2;
            if ($type2=='shift' && $type1=='reduce') {
                $shiftaction = $action2;
                $reduceaction = $action1;
            }
            /**
             * @var block_formal_langs_grammar_production_symbol $symbol
             */
            $symbol = $shiftaction['symbol'];
            /**
             * @var block_formal_langs_grammar_production_rule $rule
             */
            $rule = $reduceaction['action']->rule;
            $result = $this->try_resolve_shift_reduce_conflict($shiftaction, $reduceaction,
                                                               $symbol, $rule, $ok);
        }
        return $result;
    }

    /**
     * Tries to resolve shift-reduce action
     * @param array $shiftaction  shift action
     * @param array $reduceaction reduce action
     * @param block_formal_langs_grammar_production_symbol $symbol  symbol of lookahead
     * @param block_formal_langs_grammar_production_rule  $rule   rule to reduce to
     * @param bool $ok whether resolve was successfull
     * @return array|null chosen action
     */
    protected function try_resolve_shift_reduce_conflict($shiftaction, $reduceaction, $symbol, $rule, &$ok) {
        $ruleprec = $rule->precedence();
        $symbolprec = $this->g->precedence_for($symbol);
        $result = null;
        if ($ruleprec == null && $symbolprec == null) {
            $ok = false;
            $result = $shiftaction;
        } else {
            if ($ruleprec == $symbolprec) {
                $result = $this->try_resolve_shift_reduce_using_associativity($shiftaction, $reduceaction,
                                                                              $symbol, $ok);
            } else {
                $ok = true;
                if ($ruleprec > $symbolprec) {
                    $result = $reduceaction;
                } else {
                    $result = $shiftaction;
                }
            }
        }
        return $result;
    }
    /**
     * Tries to resolve shift-reduce action, using operator associativity
     * @param array $shiftaction  shift action
     * @param array $reduceaction reduce action
     * @param block_formal_langs_grammar_production_symbol $symbol  symbol of lookahead
     * @param bool $ok whether resolve was successfull
     * @return array|null chosen action
     */
    public function try_resolve_shift_reduce_using_associativity($shiftaction, $reduceaction, $symbol, &$ok) {
        $assoc = $this->g->associativity_for($symbol);
        $result = null;
        if ($assoc == null) {
            $ok = false;
            $result =  $shiftaction;
        }  else {
            // Handle each kind of associativity
            $ok = true;
            $nonassoc = block_formal_langs_grammar_associativity::$nonassoc;
            $left = block_formal_langs_grammar_associativity::$left;
            if ($assoc == $nonassoc) {
                $result = null;
            }  else {
                if ($assoc == $left) {
                    $result = $reduceaction;
                } else {
                    $result = $shiftaction;
                }
            }
        }
        return $result;
    }
    /**
     * Removes an action data
     * @param array $actions action data
     * @param int $j
     * @return array resulting data
     */
    protected function remove_action($actions, $j) {
        $result = array();
        for($i = 0; $i < count($actions); $i++) {
            if ($i != $j) {
                $result[] = $actions[$i];
            }
        }
        return $result;
    }
    /**
     * Determines, whether two pairs symbol, action based on the same symbol
     * @param array $action1 first action transition
     * @param array $action2  second action transition
     * @return bool true, if same based
     */
    protected function is_same_symbol_action($action1, $action2) {
        /** @var block_formal_langs_grammar_production_symbol $fs  */
        $fs = $action1['symbol'];
        return $fs->is_same($action2['symbol']);
    }

    /**
     * Creates a new based on context grammar conflict
     * @param int $state  state where error is occured
     * @param array $action1  pair, that describes a first action
     * @param array $action2  pair, that describes a second action
     */
    protected function create_grammar_conflict($state, $action1, $action2) {
        if ($action1['action']->type == 'shift') {
            if ($action2['action']->type == 'reduce' || $action2['action']->type == 'accept') {
                $this->g->add_error($this->create_shiftreduce_conflict($state,$action1, $action2));
            }
        }
        if ($action1['action']->type == 'reduce' || $action1['action']->type == 'accept') {
            if ($action2['action']->type == 'shift') {
                $this->g->add_error($this->create_shiftreduce_conflict($state,$action2, $action1));
            } else {
                $this->g->add_error($this->create_reducereduce_conflict($state,$action1, $action2));
            }
        }
    }
    /**
     * Creates a new shift-reduce grammar conflict
     * @param int $state  state where error is occured
     * @param array $action1  pair, that describes a first action
     * @param array $action2  pair, that describes a second action
     * @return block_formal_langs_grammar_error
     */
    protected function create_shiftreduce_conflict($state, $action1, $action2) {
        /** @var block_formal_langs_grammar_production_symbol $s  */
        $s = $action1['symbol'];
        $type = block_formal_langs_grammar_error::$SHIFT_REDUCE_CONFLICT;
        return new block_formal_langs_grammar_error($type,$state,$s,$action1['action'],$action2['action']);
    }
    /**
     * Creates a new reduce-reduce grammar conflict
     * @param int $state  state where error is occured
     * @param array $action1  pair, that describes a first action
     * @param array $action2  pair, that describes a second action
     * @return block_formal_langs_grammar_error
     */
    protected function create_reducereduce_conflict($state, $action1, $action2) {
        /** @var block_formal_langs_grammar_production_symbol $s  */
        $s = $action1['symbol'];
        $type = block_formal_langs_grammar_error::$REDUCE_REDUCE_CONFLICT;
        return new block_formal_langs_grammar_error($type,$state,$s,$action1['action'],$action2['action']);
    }

    /**
     * Checks whether two pairs <symbol, action> , <symbol, action> actions are the same
     * @param array $action1 first action
     * @param array $action2  second action
     * @return bool whether they are the same
     */
    protected function is_same_action($action1, $action2) {
        $result = false;
        if ($this->is_same_symbol_action($action1, $action2)) {
            if ($action1['action']->type == $action2['action']->type) {
                if ($action1['action']->type == 'accept') {
                    $result = true;
                }
                if ($action1['action']->type == 'shift') {
                    $result = ($action1['action']->goto == $action2['action']->goto);
                }
                if ($action2['action']->type == 'reduce') {
                    /** @var block_formal_langs_grammar_production_rule $wrule  */
                    $wrule = $action1['action']->rule;
                    $result = $wrule->is_same($action2['action']->rule);
                }

            }
        }
        return $result;
    }
}