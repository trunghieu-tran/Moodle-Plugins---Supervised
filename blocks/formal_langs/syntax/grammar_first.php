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
 * Describes a FIRST(X) function, as defined in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools
 * p. 195-196.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar.php');
/**
 * Describes a FIRST(X) function, as defined in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools
 * p. 195-196.
 */
class block_formal_langs_grammar_first {
    /**
     * A grammar for computing a FIRST function
     * @var block_formal_langs_grammar a grammar
     */
    protected $g;

    /**
     * Constructs a class for computing first function from grammar
     * @param block_formal_langs_grammar $g a grammar
     */
    public function __construct($g) {
        $this->g = $g;
    }

    /** Computes a FIRST(X) function
     *  @param array|block_formal_langs_grammar_production_symbol $x an array or symbol for which prefix must be determined
     *  @return array of block_formal_langs_grammar_production_symbol array of active prefixes
     */
    public function first($x) {
        if (is_array($x)) {
            if (count($x) == 0) {
                die('Invalid array supplied for FIRST(X)');
            }
            if (count($x) == 1) {
                return $this->first_for_element($x[0]);
            }
            return $this->first_for_array($x);
        }
        return $this->first_for_element($x);
    }

    /** Computes a FIRST(X) function for symbol
     *  @param block_formal_langs_grammar_production_symbol $x a symbol
     *  @return array of block_formal_langs_grammar_production_symbol array of active prefixes
     */
    private function first_for_element($x) {
        if ($this->g->is_terminal($x->type())) {
            return array( clone $x );
        }
        // Get definitions for symbol from grammar
        $defs = $this->g->get_definitions_for($x->type());
        $result = array();
        // Add epsilon if epsilon production is available
        $has_epsilon = false;
        for($i = 0; $i < count($defs); $i++) {
            /** @var block_formal_langs_grammar_production_rule $def  */
            $def = $defs[$i];
            if ($def->rightcount() == 1 && $def->right(0)->is_epsilon())
                $has_epsilon = true;
        }
        if ($has_epsilon) {
            $result[] = new block_formal_langs_grammar_epsilon_symbol();
        }
        // Merge all starting definitions
        for($i = 0; $i < count($defs); $i++) {
            $this->merge_if($result, $this->first_for_definition($x, $result, $defs[$i]), true, true);
        }

        return $result;
    }

    /** Computes FIRST(X) function for definition of element
     *  @param block_formal_langs_grammar_production_symbol $x X argument for FIRST(X) function
     *  @param array $parentresult a partially computed FIRST(X) for element X
     *  @param block_formal_langs_grammar_production_rule $definition definition of element
     *  @return array of block_formal_langs_grammar_production_symbol - array of active prefixes
     */
    private function first_for_definition($x, $parentresult, $definition) {
        $result = array();
        $epsilon_is_in_all =  true;
        for($i = 0 ; ($i < $definition->rightcount()) && $epsilon_is_in_all; $i++) {
            if ($definition->right($i)->type() == $x->type()) {
                $temp = $parentresult;
            } else {
                $temp = $this->first_for_element($definition->right($i));
            }
            $this->merge_if($result, $temp, $epsilon_is_in_all);
            $epsilon_is_in_all = $epsilon_is_in_all && $this->has_epsilon($temp);
        }


        if ($epsilon_is_in_all) {
            $result[] = new block_formal_langs_grammar_epsilon_symbol();
        }

        return $result;
    }

    /** Returns function FIRST(X) for sequence of symbols
     *  @param array $x of block_formal_langs_grammar_production_symbol sequence, FIRST(X) are defined for
     *  @return array of block_formal_langs_grammar_production_symbol - array of active prefixes
     */
    private function first_for_array($x) {
        $result = array();
        $epsilon_is_in_all =  true;
        for($i = 0; ($i < count($x)) && $epsilon_is_in_all ; $i++) {
            $temp = $this->first_for_element($x[$i]);
            $this->merge_if($result, $temp, $epsilon_is_in_all);
            $epsilon_is_in_all = $epsilon_is_in_all && $this->has_epsilon($temp);
        }

        if ($epsilon_is_in_all) {
            $result[] = new block_formal_langs_grammar_epsilon_symbol();
        }
        return $result;
    }


    /** Merges sets of symbols, if condition is true are met.
     *  Also skips epsilon symbol if $addepsilon is not supplied
     *  @param array $result of block_formal_langs_grammar_production_symbol resulting set, where all elements are stored
     *  @param array $set of block_formal_langs_grammar_production_symbol  setm whose elements will be merged with $result
     *  @param  bool $condition a condition flag, which must be supplied
     *  @param  bool $addepsilon whether epsilon symbols must be merged
     */
    protected function merge_if(&$result, $set, $condition, $addepsilon = false) {
        if ($condition == false)
            return;

        for($i = 0; $i < count($set); $i++ ) {
            /** @var block_formal_langs_grammar_production_symbol $el  */
            $el = $set[$i];
            $contains = false;
            for($j = 0 ; $j < count($result); $j++) {
                /** @var block_formal_langs_grammar_production_symbol $rel  */
                $rel = $result[$j];
                if ($el->is_same($rel))
                    $contains = true;
            }
            if ($contains == false  && ($addepsilon == true || $el->is_epsilon() == false))
                $result[] = clone $el;
        }
    }

    /**
     * Whether epsilon symbol is in set
     * @param array $array of array of  block_formal_langs_grammar_production_symbol multiple sets to check with
     * @return bool has it epsilon or not
     */
    private function has_epsilon($array) {
        for($i = 0; $i < count($array); $i++) {

            if ($array[$i]->is_epsilon())
                return true;
        }
        return false;
    }

    /** Whether epsilon symbol is in all sets in a range
     *   @param array $array of array of  block_formal_langs_grammar_production_symbol multiple sets to check with
     *   @param int|null   $from element, from which we must check sets. If null - all sets must be checked
     *   @param int|null   $to   the number of last set, we must check
     *   @return bool true, if epsilon is all sets in range
     */
    private function epsilon_is_in_all_sets($array, $from = null, $to = null) {
        if ($from === null || $to === null) {
            $from = 0;
            $to = count($array);
        }
        $ok = true;
        for($i = $from; $i < $to; $i++) {
            $contains = $this->has_epsilon($array[$i]);
            $ok = $ok && $contains;
        }
        return $ok;
    }
}