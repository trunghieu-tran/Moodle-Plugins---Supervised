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
 * Describes an rules and operators for building lexical patterns
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
global $CFG;
require_once($CFG->dirroot.'/lib/classes/text.php');
/**
 *  Determines a type enumeration for matching rules of lexer
 */
class  block_formal_langs_lexical_matching_rule_type {
    /**
     * Determines a special zero-width epsilon rule
     * @var int
     */
    public static $EPSILON = 0;
    /**
     * A special kind of trule, which matches all symbols
     * @var int
     */
    public static $ALLMATCH = 1;
    /**
     * A type of rule, which match any symbol from set
     * @var int
     */
    public static $MATCHANYONEFROMSET = 2;
    /**
     * A type of rule, which match any symbol not from set
     * @var int
     */
    public static $MATCHANYONENOTFROMSET = 3;
    /**
     * A set, which matches any symbol
     * @var int
     */
    public static $EOF_SYMBOL = 4;
};

class block_formal_langs_lexical_transition_rule {
    /**
     * Old state for transition
     * @var int
     */
    public $oldstate;
    /**
     * A rule for matching transition
     * @var block_formal_langs_lexical_matching_rule
     */
    public $rule;
    /**
     * A new states array
     * @var array
     */
    public $newstates;

    /**
     * Constructs a new rule
     * @param int $oldstate  old state
     * @param block_formal_langs_lexical_matching_rule $rule rule for moving
     * @param array $newstates  new states
     */
    public function __construct($oldstate, $rule, $newstates) {
        $this->oldstate = $oldstate;
        $this->rule = $rule;
        $this->newstates = $newstates;
    }
    /**
     * Writes a transition rule as dot language string
     * @return string transition dot string
     */
    public function write_as_dot() {
        $label = $this->rule->label();
        $text = '';
        for ($i = 0; $i < count($this->newstates); $i++) {
            $newstate = $this->newstates[$i];
            $text .= '    A_' . $this->oldstate . ' -> A_' . $newstate . ' [ label="' . $label . '" ];';
            $text .= PHP_EOL;
        }
        return $text;
    }
}
/**
 * Describes a transition table for NFA/DFA
 */
class block_formal_langs_lexical_transition_table {
    /**
     * A transition table is described as array of block_formal_langs_lexical_transition_rule
     * @var array
     */
    public $transitions = array();
    /**
     * Array of acceptable states for table. First state is always aceptable
     * In common case, there is only one state, but in lexer case, it is one
     * @var array acceptable state
     */
    public $acceptablestates = array();


    /**
     * Returns a transitions starting from state
     * @param int $oldstate
     * @return array of  block_formal_langs_lexical_transition_rule
     */
    public function transitions_for_state($oldstate) {
        $result = array();
        for($i = 0; $i < count($this->transitions); $i++) {
            /**
             * @var block_formal_langs_lexical_transition_rule $rule
             */
            $rule = $this->transitions[$i];
            if ($rule->oldstate == $oldstate) {
                $result[] = $rule;
            }
        }
        return $result;
    }

    /**
     * Returns an epsilon-closure function for states
     * @param int $oldstate
     * @return array of  int
     */
    public function epsilon_closure_for_state($oldstate) {
        $result = array($oldstate);
        $stack = array($oldstate);
        while(count($stack) != 0) {
            $state = array_shift($stack);
            $transitions = $this->transitions_for_state($state);
            for($i = 0; $i < count($transitions); $i++) {
                /**
                 * @var block_formal_langs_lexical_transition_rule $rule
                 */
                $rule = $transitions[$i];
                if ($rule->rule->is_epsilon()) {
                    foreach($rule->newstates as $u) {
                        if (!in_array($u, $result)) {
                            $result[] = $u;
                            $stack[] = $u;
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Computes epsilon closure for one state or multiple
     * @param int|array $state
     * @return array or int all other states
     */
    public function epsilon_closure($state) {
        $result = null;
        if (is_array($state)) {
            $result = array();
            if (count($state)) {
                foreach($state as $currentstate) {
                    $c = $this->epsilon_closure_for_state($currentstate);
                    $result = block_formal_langs_lexical_matching_rule::union($result, $c);
                }
                sort($result);
            }
        } else {
            $result = $this->epsilon_closure_for_state($state);
        }
        return $result;
    }
    /**
     * Clones a transitions list
     * @param array $transitions
     * @return array
     */
    private function clone_transitions($transitions) {
        $result = array();
        if (count($transitions) != 0) {
            foreach($transitions as $transition) {
                $result[] = clone $transition;
            }
        }
        return $result;
    }

    /**
     * Tries to intersect to rules and unions their returning states
     * @param block_formal_langs_lexical_matching_rule $rule1
     * @param block_formal_langs_lexical_matching_rule $rule2
     * @param array $nstates1
     * @param array $nstates2
     * @param bool $disjoint new disjoint rule
     * @return array of pairs (rule, new states)
     */
    public function try_intersect($rule1, $rule2, $nstates1, $nstates2, &$disjoint) {
        $ok = false;
        $intersectresult = $rule1->intersect($rule2);
        $result = array();
        foreach($intersectresult as $rulestates) {
            $resultentry = array($rulestates[0], array());
            foreach($rulestates[1] as $statesmapping) {
                if ($statesmapping == 0) {
                    $nmap = &$nstates1;
                } else {
                    $nmap = &$nstates2;
                }
                $newkstates = block_formal_langs_lexical_matching_rule::union($resultentry[1], $nmap);
                $resultentry[1] = $newkstates;
            }
            sort($resultentry[1]);
            $result[] = $resultentry;
        }
        if (count($result) == 2) {
            /**
             * @var block_formal_langs_lexical_matching_rule $rrule1
             * @var block_formal_langs_lexical_matching_rule $rrule2
             *
             */
            $rrule1 = $result[0][0];
            $rrule2 = $result[1][0];
            $ok = ($rule1->is_same($rrule1) || $rule1->is_same($rrule2)) &&
                  ($rule2->is_same($rrule1) || $rule2->is_same($rrule2));
        }
        $disjoint = ($disjoint && $ok);
        return $result;
    }

    /**
     * Builds new transitions, disjointing and discarding epsilon transitions
     * @param array $transitions of pairs(rule, array of new states)
     * @return array of  pairs(rule, array of new states)
     */
    protected function build_disjoint_transitions_for_transitions($transitions) {
        $totalresult = array();
        /**
         * @var block_formal_langs_lexical_matching_rule $rule
         */
        foreach($transitions as $transition) {
            $rule = $transition[0];
            if ($rule->is_epsilon() == false) {
                $totalresult[] = $transition;
            }
        }
        do {
            $tempresult = $totalresult;
            $totalresult = array();
            $disjoint = true;
            while(count($tempresult) >= 2) {
                $rule1 = array_shift($tempresult);
                $rule2 = array_shift($tempresult);
                $rules = $this->try_intersect($rule1[0], $rule2[0], $rule1[1], $rule2[1], $disjoint);
                foreach($rules as $rule) {
                    $totalresult[] = $rule;
                }
            }
            if (count($tempresult) == 1) {
                $disjoint = $disjoint && (count($totalresult) == 0);
                $totalresult[] = array_shift($tempresult);
            }
        } while(!$disjoint);
        return $totalresult;
    }
    /**
     * Builds a disjoint transition arrays for states array.
     * This function is useful, when merging multiple NFA states to one giant DFA state
     * Then we should scan some entering characters, but we have some infinite alphabet
     * Also we can discard some epsilon transitions, because we don't need those in DFA
     * and they are already here
     * @param array $states of int
     * @return array of transitions from state with disjoint sets from one state to another
     *         as pair ($transtitions, array(new string))
     */
    public function build_disjoint_transitions($states) {
        $testtransitions = array();
        if (count($states) != 0) {
            foreach($states as $state) {
                $ftransitions = $this->transitions_for_state($state);
                for($i = 0; $i < count($ftransitions); $i++) {
                    /**
                     * @var block_formal_langs_lexical_transition_rule $transition
                     */
                    $transition = $ftransitions[$i];
                    $testtransitions[] = array($transition->rule, $transition->newstates);
                }
            }
        }
        return $this->build_disjoint_transitions_for_transitions($testtransitions);
    }

    public function reenumerate($newstartingstate) {
        for($i = 0; $i < count($this->transitions); $i++) {
            /**
             * @var block_formal_langs_lexical_transition_rule $rule
             */
            $rule = $this->transitions[$i];
            $rule->oldstate += $newstartingstate;
            for($j = 0; $j < count($rule->newstates); $j++) {
                $rule->newstates[$j] += $newstartingstate;
            }
        }
        for($i = 0; $i < count($this->acceptablestates); $i++) {
            $this->acceptablestates[$i] += $newstartingstate;
        }
    }

    /**
     * Creates a transition rule. Other passes arguments are used to form a transition array
     * @param int $oldstate starting state
     * @param block_formal_langs_lexical_matching_rule $rule a transition rule
     * @return block_formal_langs_lexical_transition_rule
     */
    public static function transition_rule($oldstate,$rule) {
        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        $a = new block_formal_langs_lexical_transition_rule(null, null, null);
        $a->oldstate = $oldstate;
        $a->rule = $rule;
        $a->newstates = $args;
        return $a;
    }

    /**
     * Writes a transition rule as dot language string
     * @return string transition dot string
     */
    public function write_as_dot() {
        $result = 'graph G { ' . PHP_EOL;
        $max = max($this->acceptablestates);
        for($i = 0; $i <= $max; $i++) {
            $result .=  '    A_' . $i . ' [label="' . $i . '", shape=ellipse];' . PHP_EOL;
        }
        for($i = 0; $i < count($this->transitions); $i++) {
            /**
             * @var block_formal_langs_lexical_transition_rule  $transition
             */
            $transition = $this->transitions[$i];
            $result .= $transition->write_as_dot();
        }
        $result .= '}' . PHP_EOL;
        return $result;
    }

    /**
     * Converts a table to a digraph to be tested against
     * @return block_formal_langs_language_parser_digraph
     */
    public function to_digraph() {
        $result = array();
        $maxstate = max($this->acceptablestates);
        for($state = 0; $state <= $maxstate; $state++) {
            $result[$state] = array('node' => 1, 'edges' => array());
        }
        for($i = 0; $i < count($this->transitions); $i++) {
            /**
             * @var block_formal_langs_lexical_transition_rule  $transition
             */
            $transition = $this->transitions[$i];
            $label = $transition->rule->label();
            if (count($transition->newstates) > 1) {
               $j = 0;
               foreach($transition->newstates as $state) {
                   $result[$transition->oldstate]['edges'][$label . $j] = $state;
                   $j++;
               }
            } else {
                $result[$transition->oldstate]['edges'][$label] = $transition->newstates[0];
            }
        }
        return new block_formal_langs_language_parser_digraph($result);
    }

    /**
     * Inserts a new transition
     * @param block_formal_langs_lexical_transition_rule  $ntransition
     */
    public function insert($ntransition) {
        $found = false;
        for($i = 0; $i < count($this->transitions); $i++) {
            /**
             * @var block_formal_langs_lexical_transition_rule  $transition
             */
            $transition = $this->transitions[$i];
            if ($transition->oldstate == $ntransition->oldstate
                && $transition->rule->is_same($ntransition->rule)
                )  {
                $transition->newstates = array_unique(array_merge($transition->newstates, $ntransition->newstates));
                $found = true;
            }
        }
        if ($found == false) {
            $this->transitions[] = $ntransition;
        }
    }
}
/**
 *  A common lexical matching rule, which handles most of character style types
 */
class block_formal_langs_lexical_matching_rule  {
    /**
     *  Determines a type of matching rule
     *  @var int as value from block_formal_langs_lexical_matching_rule_type
     */
    protected $type;
    /**
     *  Determines a character set for matching
     *  @var array as set of characters
     */
    protected $characterset;
    /**
     *  Constructs new rule. 
     *  @param int $type type of rule 
     *  @param array $set set of characters to be matched with
     */
    public function __construct($type, $set = array()) {
        $this->type = $type;
        $this->characterset = $set;
    }

    /**
     * Determines, whether rule is same as other
     * @param block_formal_langs_lexical_matching_rule $o other
     * @return bool whether other same
     */
    public function is_same($o) {
        return $this->type == $o->type && $this->characterset == $o->characterset;
    }

    /**
     * Determines, whether rule is in array
     * @param array $a
     * @return bool
     */
    public function is_in($a) {
        if (count($a) != 0) {
            foreach($a as $rule) {
                if ($this->is_same($rule)) {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     *  Creates new epsilon matching rule
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function epsilon_rule() {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$EPSILON
               );
    }
    /**
     *  Creates new rule, which matches all of symbols
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function all_matching_rule() {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$ALLMATCH
               );    
    }
    /**
     *  Creates new rule, which matches only one symbol
     *  @param string $character matching chatacter
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function simple_rule($character) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET,
                   array( $character )
               );      
    }

    /**
     * A rule, which only eof matches to
     * @return block_formal_langs_lexical_matching_rule
     */
    public static function eof_rule() {
        return self::simple_rule(block_formal_langs_lexical_matching_rule_type::$EOF_SYMBOL);
    }
    /**
     *  Creates new character class
     *  @param array $set matching character set
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function charclass_rule($set) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET,
                   $set
               );      
    }
    
    /**
     *  Creates new negative character class
     *  @param array $set matching character set
     *  @return block_formal_langs_lexical_matching_rule
     */
    public static function neg_charclass_rule($set) {
        return new block_formal_langs_lexical_matching_rule(
                   block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET,
                   $set
               );      
    }
    /**
     * Returns a label for matching rule
     * @return string label for drawing a rule
     */
    public function label() {
        $result = '.';
        $denormalizedcset = '[]';
        if (count($this->characterset)) {

            $denormalizedcset = implode('', array_values($this->characterset));
            $denormalizedcset =  '[' . $denormalizedcset  . ']';
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON) {
            $result = 'eps';
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = $denormalizedcset;
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            /** @noinspection PhpUndefinedClassInspection */
            $result = '[^' . core_text::substr($denormalizedcset, 1);
        }        
        return $result;
    }
    /**
     *  Determines, whether rule doesn't consume character
     *  @return boolean
     */
    public function is_zero_width() {
        return $this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON;
    }

    /**
     * Determines, whether rule marked as epsilon
     * @return boolean
     */
    public function is_epsilon() {
        return $this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON;
    }
    /**
     * Determines, whether character matches rule
     * @param string $character character, that is tested against rule
     * @param stdClass $position position for matching
     * @return boolean whether it matched rule
     */
    public function match($character, $position) {
        $result = true;
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = in_array($character, $this->characterset);
        }
        if ($this->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $result = !in_array($character, $this->characterset);
        }
        return $result;
    }
    /**
     * Call methods if one of rules is of specified type
     * @param block_formal_langs_lexical_matching_rule $other other node
     * @param int $type type of rule
     * @param string $method method, used in speified type
     * @param mixed $result result of executed method
     * @param boolean $handled was it handled before or after
     */     
    protected function call_method_if_one_of_spec_type($other, $type, $method, &$result, &$handled) {
        // If method was not handled before
        if ($handled == false) {
            if ($this->type == $type) {
                $handled = true;
                $result = $this->$method($other, array( 0 => 0, 1 => 1));
            } else {
                if ($other->type == $type) {
                    $handled = true;
                    $result = $other->$method($this, array( 0 => 1, 1 => 0));
                }
            }
        }
    }

    /**
     * Intersects epsilon type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_epsilon($other, $mapping) {
       return array( array($this, array( $mapping[0] )),
                     array($other, array( $mapping[1] ))
                   );
    }
    /**
     * Intersects epsilon type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_allmatch($other, $mapping) {
        $result = array();
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $result = array( array( self::charclass_rule($other->characterset), array($mapping[0], $mapping[1]) ),
                             array( self::neg_charclass_rule($other->characterset), array($mapping[0]))
                           );
        }
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $result = array( array( self::charclass_rule($other->characterset), array($mapping[0]) ),
                             array( self::neg_charclass_rule($other->characterset), array($mapping[0], $mapping[1]))
                      );

        }
        return $result;
    }

    /**
     * Performs safe array diff for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    protected static function diff($a1 , $a2) {
        if (count($a1) == 0) {
            return $a2;
        }
        if (count($a2) == 0) {
            return $a1;
        }
        return array_values(array_diff($a1, $a2));
    }

    /**
     * Performs safe array intersect for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    protected static function aintersect($a1, $a2) {
        if (count($a1) == 0 || count($a2) == 0) {
            return array();
        }
        return array_values(array_intersect($a1, $a2));
    }

    /**
     * Performs safe array union for set stuff
     * @param array $a1
     * @param array $a2
     * @return array
     */
    public static function union($a1, $a2) {
        if (count($a1) == 0) {
            return $a2;
        }
        if (count($a2) == 0) {
            return $a1;
        }
        return array_values(array_unique(array_merge($a1, $a2)));
    }

    /**
     * Inserts new created rule, if set is not empty
     * @param array $result result, where set will be pushed
     * @param string $method ruletype
     * @param array $set set which will be checked for emptiness
     * @param array $states
     */
    protected static function insert_rule(&$result, $method, $set, $states) {
        if (count($set) != 0) {
            $methodname = $method . '_rule';
            $result[] = array( self::$methodname($set) , $states );
        }
    }
    /**
     * Intersects a character class type rule with other rule
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_charclass($other, $mapping) {
        $result = array();
        $froute = array($mapping[0]);
        $broute = array($mapping[0], $mapping[1]);
        $sroute = array($mapping[1]);
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONEFROMSET) {
            $common = self::aintersect($this->characterset, $other->characterset);
            $fst = self::diff($this->characterset, $common);
            $snd = self::diff($other->characterset, $common);

            self::insert_rule($result, 'charclass', $fst, $froute);
            self::insert_rule($result, 'charclass', $common, $broute);
            self::insert_rule($result, 'charclass', $snd, $sroute);

        }
        if ($other->type == block_formal_langs_lexical_matching_rule_type::$MATCHANYONENOTFROMSET) {
            $fst = self::aintersect($this->characterset, $other->characterset);
            $both = self::diff($this->characterset, $fst);
            $snd = self::union($this->characterset, $other->characterset);

            self::insert_rule($result, 'charclass', $fst, $froute);
            self::insert_rule($result, 'charclass', $both, $broute);
            self::insert_rule($result, 'neg_charclass', $snd, $sroute);
        }
        return $result;
    }
    /**
     * Intersects a negative character class type rule with other rule, which
     * is negative character class too, because all other cases are handled already
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @param array $mapping mapping of source states to relative states
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     */
    protected function intersect_neg_charclass($other, $mapping) {
        $froute = array($mapping[0]);
        $broute = array($mapping[0], $mapping[1]);
        $sroute = array($mapping[1]);
        $result = array();
        $both = self::union($this->characterset, $other->characterset);
        $intersect = self::aintersect($this->characterset, $other->characterset);
        $fst = self::diff($other->characterset, $intersect);
        $snd = self::diff($this->characterset, $intersect);
        self::insert_rule($result, 'charclass', $fst, $froute);
        self::insert_rule($result, 'neg_charclass', $both, $broute);
        self::insert_rule($result, 'charclass', $snd, $sroute);
        return $result;
    }
    /**
     * And now function, which is reason, why this is one class, that uses a type 
     * and character set to split or union 
     * two edges which can intersect by character set to make one edge for a symbol
     * @param block_formal_langs_lexical_matching_rule $other node, which is tested
     * @return array   <rule, array( index of states, can be [0,1], 0, 1)>
     * @throws Exception if intersection where not properlt handled
     */
     public function intersect($other) {
        $result = array();
        $handled = false;
        if ($this->type == $other->type
            && ($this->type == block_formal_langs_lexical_matching_rule_type::$EPSILON
            ||  $this->type == block_formal_langs_lexical_matching_rule_type::$ALLMATCH)) {
            $result = array( array( clone $other, array(0, 1) ) );
            $handled = true;
        } else {
            $methods = array(
                'EPSILON' => 'intersect_epsilon',
                'ALLMATCH' => 'intersect_allmatch',
                'MATCHANYONEFROMSET' => 'intersect_charclass',
                'MATCHANYONENOTFROMSET' => 'intersect_neg_charclass'
            );
            foreach($methods as $typestring => $method) {
                $type = block_formal_langs_lexical_matching_rule_type::$$typestring;
                $this->call_method_if_one_of_spec_type($other, $type, $method, $result, $handled);
            }
        }
        if ($handled == false) {
            throw new Exception('Rule type was not handled!');
        }
        return $result;
     }

    /**
     * Builds a transition table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
        $result = new block_formal_langs_lexical_transition_table();
        $result->transitions[] = $result->transition_rule(0, $this, 1);
        $result->acceptablestates = array(1);
        return $result;
    }
};

/**
 * Determines an alternative operator
 */
class block_formal_langs_lexical_alternative_operator {
    /**
     * Child nodes
     * @var array of child nodes, which must implement build_table method
     */
    public $children;

    /**
     * Constructs new alternative operator
     * @param $children
     */
    public function __construct($children) {
        $this->children = $children;
    }
    /**
     * Builds a transition table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
        $result = self::build_non_concatenated($this->children);
        // Merge leading acceptable states into one
        $newacceptablestate = max($result->acceptablestates) + 1;
        for($i = 0; $i < count($result->acceptablestates); $i++) {
            $result->insert( $result->transition_rule(
                $result->acceptablestates[$i],
                block_formal_langs_lexical_matching_rule::epsilon_rule(),
                $newacceptablestate) );
        }
        $result->acceptablestates = array( $newacceptablestate );
        return $result;
    }

    /**
     * Builds tansition table for non-concatenated nodes
     * @param array $nodes
     * @return block_formal_langs_lexical_transition_table
     */
    public static function build_non_concatenated($nodes) {
        /**
         * @var  block_formal_langs_lexical_matching_rule $child
         */
        $child = $nodes[0];
        $result = $child->build_table();
        $result->reenumerate(1);
        $epsilon = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $starting = array(1);
        $currentoffset = $result->acceptablestates[0] + 1;
        // Merge transition tables
        for($i = 1; $i < count($nodes); $i++) {
            $child = $nodes[$i];
            $temp = $child->build_table();
            $temp->reenumerate($currentoffset);
            $starting[] = $currentoffset;
            $result->transitions = array_merge($result->transitions, $temp->transitions);
            $result->acceptablestates[] = $temp->acceptablestates[0];
            $currentoffset = $temp->acceptablestates[0] + 1;
        }
        $transition = new block_formal_langs_lexical_transition_rule(0, $epsilon, $starting);
        $result->insert($transition);
        return $result;
    }
}

/**
 * A concatenation operator
 */
class block_formal_langs_lexical_concat_operator {
    /**
     * Nodes for concatenation operator
     * @var array
     */
    protected $nodes;

    /**
     * Constructs a concatenation nodes
     * @param array $nodes
     */
    public function __construct($nodes) {
        $this->nodes = $nodes;
    }

    /**
     * Builds a transition table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
        /**
         * @var  block_formal_langs_lexical_matching_rule $child
         */
        $child = $this->nodes[0];
        $result = $child->build_table();
        $currentoffset = $result->acceptablestates[0] + 1;
        for($i = 1; $i < count($this->nodes); $i++) {
            $child = $this->nodes[$i];
            $temp = $child->build_table();
            $temp->reenumerate($currentoffset);
            $tr = $result->transition_rule($result->acceptablestates[0],
                block_formal_langs_lexical_matching_rule::epsilon_rule(),
                $currentoffset);
            $result->insert($tr);
            $result->transitions = array_merge($result->transitions, $temp->transitions);
            $result->acceptablestates = $temp->acceptablestates;
            $currentoffset = $temp->acceptablestates[0] + 1;
        }
        return $result;
    }
}

/**
 * An infinite repetition for node
 */
class block_formal_langs_lexical_infinite_repetition {
    /**
     * An inifinite repetition node for matching
     * @var block_formal_langs_lexical_matching_rule
     */
    protected $node;

    /**
     * A new node for repetition
     * @param $node
     */
    public function __construct($node) {
        $this->node = $node;
    }

    /**
     * Builds a transition table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
        $result = $this->node->build_table();
        $result->insert($result->transition_rule($result->acceptablestates[0],
            block_formal_langs_lexical_matching_rule::epsilon_rule(),
            0
        ));
        return $result;
    }
}

/**
 * None or infinite repetition of same node
 */
class block_formal_langs_lexical_none_or_infinite_repetition {
    /**
     * A node
     * @var block_formal_langs_lexical_matching_rule
     */
    protected $node;

    /**
     * A new node for repetition
     * @param $node
     */
    public function __construct($node) {
        $this->node = $node;
    }

    /**
     * Builds a transition table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
        $fst = block_formal_langs_lexical_matching_rule::epsilon_rule();
        $snd = new block_formal_langs_lexical_infinite_repetition($this->node);
        $top = new block_formal_langs_lexical_alternative_operator(array($fst, $snd));
        return $top->build_table();
    }
}

/**
 * A quantifier  for counting repeated instances
 */
class block_formal_langs_lexical_from_to_quantifier {
    /**
     * A specified node
     * @var block_formal_langs_lexical_matching_rule
     */
    protected $node;
    /**
     * Minimum repetition of nodes
     * @var int minimum repetition
     */
    protected $min;
    /**
     * Maximum repetition of nodes
     * @var  int maximum repetition
     */
    protected $max;

    /**
     * Constructs a new node
     * @param block_formal_langs_lexical_matching_rule $node  node for repetition
     * @param int|null $min  null - is 0
     * @param int|null $max  null - is infinite
     */
    public function __construct($node, $min = null, $max = null) {
        $this->node = $node;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * Builds alternative concatenations from min to max
     * @param int $min
     * @param int $max
     * @return block_formal_langs_lexical_alternative_operator
     */
    protected function build_alternative_concatenations($min, $max) {
        $alts = array();
        for($i = $min; $i <= $max; $i++) {
            if ($i != 0) {
                $concat = array();
                for($j = 0; $j < $i; $j++) {
                    $concat[] = $this->node;
                }
                $alts[] = new block_formal_langs_lexical_concat_operator($concat);
            } else {
                $alts[] = block_formal_langs_lexical_matching_rule::epsilon_rule();
            }
        }
        $alt = new block_formal_langs_lexical_alternative_operator($alts);
        return $alt;
    }
    /**
     * Builds a new table
     * @return block_formal_langs_lexical_transition_table
     */
    public function build_table() {
       $result = null;
       if ($this->min === 0 && $this->max === 0 || ($this->min > $this->max && is_int($this->max))) {
           $result = block_formal_langs_lexical_matching_rule::epsilon_rule()->build_table();
           return $result;
       }
       if ($this->min === 0 && $this->max === null) {
           $a = new block_formal_langs_lexical_none_or_infinite_repetition($this->node);
           $result = $a->build_table();
           return $result;
       }
       if ($this->max === null) {
           if ($this->min == 1) {
               $resnode = new block_formal_langs_lexical_infinite_repetition($this->node);
           } else {
               $concats = array();
               for ($i = 0; $i < $this->min - 1; $i++) {
                   $concats[] = $this->node;
               }
               $rep = new block_formal_langs_lexical_infinite_repetition($this->node);
               $concats[] = $rep;
               $resnode = new block_formal_langs_lexical_concat_operator($concats);
           }
           $result = $resnode->build_table();
       } else {
           $alt = $this->build_alternative_concatenations($this->min, $this->max);
           $result = $alt->build_table();
       }
        return $result;
    }
}

/**
 * A simple lexical action for working with accepted data
 */
abstract class block_formal_langs_lexical_action  {
    /**
     * Returns new starting state for lexer
     * @return string
     */
    public function new_lexer_starting_state() {
        return 'YYINITIAL';
    }
    /**
     * Accepts lexer state
     * @param $lexer
     * @param $acceptstate
     * @return mixed some info data
     */
    abstract public function accept($lexer, $acceptstate);
}


/**
 * A simple lexical action for working with accepted data
 */
class block_formal_langs_lexical_simple_action extends block_formal_langs_lexical_action {
    /**
     * Defines a new starting state, which automata can enter
     */
    protected $startingstate;

    /**
     * Custom data, which can determine one action from another
     * @var mixed
     */
    protected $customdata;

    /**
     * Sets a custom data
     * @param $data
     */
    public function set_custom_data($data) {
        $this->customdata = $data;
    }
    /**
     * Creates a starting simple state
     * @param string $startingstate starting state which automata enters to
     */
    public function __construct($startingstate = 'YYINITIAL') {
        $this->startingstate = $startingstate;
        $this->customdata = null;
    }
    /**
     * Returns new starting state for lexer
     * @return string
     */
    public function new_lexer_starting_state() {
        return $this->startingstate;
    }
    /**
     * Accepts lexer state
     * @param block_formal_langs_lexical_automata $lexer lexical automata
     * @param  block_formal_langs_lexical_automata_state $acceptstate
     * @return mixed some info data
     */
    public function accept($lexer, $acceptstate) {
        $a = new stdClass();
        $a->text = $acceptstate->buffer();
        $a->data = $this->customdata;
        $lexer->set_result($a);
        $lexer->set_starting_state($this->new_lexer_starting_state());
    }
}