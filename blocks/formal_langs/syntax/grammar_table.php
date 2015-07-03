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
 * Describes an algorithm for building parser tables
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_action_builder.php');
/** A parser tables, which must be used when parsing some stuff
 */
class block_formal_langs_grammar_table {
    /** An LR(1)-items
     *  @var array of array of block_formal_langs_grammar_lr_one_item
     */
    protected $lr1items;
    /** A LR(1)-item goto. It is stored as number of LR(1)-item set mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, number of set I for goto into>, described as array
     *  LR(1)-item sets are stored in $lr1items
     * @var array of array
     */
    protected $lr1goto;
    /** An action table, which defined as a number of LR(1)-item state mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, stdClass action>, described as array
     */
    protected $action;
    /**
     * A grammar, which must be used to build some tables
     * @var block_formal_langs_grammar
     */
    protected $g;
    /** Constructs a new parser table from a grammar
     *  @param block_formal_langs_grammar $g grammar
     */
    public function __construct($g) {
        $this->g = $g;
        $this->lr1items = array();
        $this->lr1goto = array();
        $this->action = array();
        if ($this->g->valid()) {
            //echo 'Compute items';
            $this->compute_items();
            //echo 'Replace sets with same kernel';
            $this->replace_sets_with_same_kernel_by_union();
            //echo 'Build action';
            $builder = new block_formal_langs_grammar_action_builder($g, $this->lr1items, $this->lr1goto);
            $this->action = $builder->action();
        }
    }

    /**
     * Returns a goto table, as number of LR(1)-item set mapped into pair
     * <block_formal_langs_grammar_production_symbol symbol, number of set I for goto into>, described as array
     * @return array
     */
    public function gototable() {
        return $this->lr1goto;
    }

    /**
     * Returns an action table, as a number of LR(1)-item state mapped into pair
     *  <block_formal_langs_grammar_production_symbol symbol, stdClass action>, described as array
     * @return array
     */
    public function action() {
        return $this->action;
    }

    /**
     * Returns a table of items, which can be useful on error recovery
     * @return array of items
     */
    public function items() {
        return $this->lr1items;
    }
    /**
     * Converts all data into a string, useful for debug
     * @return string
     */
    public function tostring() {
        $result = array();
        if (count($this->lr1items)) {
            $tmp = $this->dump_items();
            $this->append_array($result, $tmp);
        }
        if (count($this->lr1goto)) {
            $tmp = $this->dump_goto();
            $this->append_array($result, $tmp);
        }
        if (count($this->action)) {
            $tmp = $this->dump_action();
            $this->append_array($result, $tmp);
        }
        if (count($result) != 0) {
            return implode(PHP_EOL, $result) . PHP_EOL;
        }
        return '';
    }

    /**
     * Appends one array to the and of another
     * @param array $a1
     * @param array $a2
     */
    private function append_array(&$a1, $a2) {
        for($i = 0; $i < count($a2); $i++) {
            $a1[] = $a2[$i];
        }
    }

    /**
     * Dumps an action table into array of strings
     * @return array of string
     */
    private function dump_action() {
        $result = array();
        $result[] = 'Action:';
        for($i = 0; $i < count($this->action); $i++) {
            $result[] = 'I'. $i . ':';
            for($j = 0; $j < count($this->action[$i]); $j++) {
                $a = $this->action[$i][$j];
                /** @var  block_formal_langs_grammar_production_symbol $symclass  */
                $symclass = $a['symbol'];
                $sym = $symclass->type();

                $type = $a['action']->type;
                if ($a['action']->type == 'shift') {
                    $type .= ' ' .  (($a['action']->goto)?$a['action']->goto:'0');
                }
                if ($a['action']->type == 'reduce') {
                    /** @var block_formal_langs_grammar_production_rule $rule  */
                    $rule = $a['action']->rule;
                    $type .= ' ' .  $rule->tostring();
                }

                $result[] = ' ' . $sym  . '->' . $type;
            }
        }
        $result[] = '';
        $result[] = '';
        return $result;
    }
    /**
     * Dumps an  LR(1) items table into array of strings
     * @return array of string
     */
    public function dump_items() {
        $result = array();
        $result[] = 'Items:';
        for($i = 0; $i < count($this->lr1items); $i++) {
            $result[] = 'I'. $i . ':';
            for($j = 0; $j < count($this->lr1items[$i]); $j++) {
                /** @var block_formal_langs_grammar_lr_one_item $item  */
                $item = $this->lr1items[$i][$j];
                $result[] = ' ' . $item->tostring();
            }
        }
        $result[] = '';
        $result[] = '';
        return $result;
    }

    /**
     * Dumps a goto table into array of strings
     * @return array of string
     */
    public function dump_goto() {
        $result = array();
        $result[] =  'Goto:';
        foreach($this->lr1goto as $findex => $values) {
            $result[] = 'I'. $findex . ':';
            for($i = 0; $i < count($values); $i++) {
                /** @var block_formal_langs_grammar_production_symbol $symbol  */
                $symbol = $values[$i]['symbol'];
                $result[] =  ' ' . $symbol->type() . ' -> ' . $values[$i]['goto'] . "\n";
            }
        }
        $result[] = '';
        $result[] = '';
        return $result;
    }



    /** Computes a canonical set of LR(1) items
     */
    public function compute_items() {
        $startlr = $this->g->starting_lr1_item();
        $closure = new block_formal_langs_grammar_closure($this->g);
        $goto = new block_formal_langs_grammar_goto();
        $this->lr1items = array( $closure->closure(array( $startlr ) ) );
        $this->lr1goto = array();
        $symbols = $this->g->symbols();
        for($setindex = 0; $setindex < count($this->lr1items); $setindex++) {
            $currentset =  $this->lr1items[$setindex];
            for($symindex = 0; $symindex < count($symbols); $symindex++ ) {
                /** @var block_formal_langs_grammar_production_symbol $currentsym  */
                $currentsym = $symbols[$symindex];
                $mygoto = $goto->run($this->g, $currentset , $currentsym);
                //print_r($mygoto);
                if (count($mygoto)!=0) {
                    $supersetindex = $this->get_lr1_subset_index($mygoto, $this->lr1items);
                    if ($supersetindex == -1) {
                        $gotoindex = count($this->lr1items);
                        $this->lr1items[] = $mygoto;
                        if (array_key_exists($setindex, $this->lr1goto) == false) {
                            $this->lr1goto[$setindex] = array();
                        }
                        $this->lr1goto[$setindex][] = array('symbol' => $currentsym, 'goto' => $gotoindex);
                    } else {
                        if (array_key_exists($setindex, $this->lr1goto) == false) {
                            $this->lr1goto[$setindex] = array();
                        }
                        $this->lr1goto[$setindex][] = array('symbol' => $currentsym, 'goto' => $supersetindex);
                    }
                }
            }
        }
    }
    /** Returns index of LR(1) itemset in LR(1) set of itemset
     *  @param array $subset a small set of LR(1) items
     *  @param array $superset a set of set of LR(1) items
     *  @return int index of set in superset, -1 if not found
     */
    private function get_lr1_subset_index($subset, $superset) {
        for($i = 0; $i < count($superset); $i++) {
            if ($this->are_itemsets_equal($subset, $superset[$i])) {
                return $i;
            }
        }
        return -1;
    }
    /** Compares two sets of LR(0-1) item sets and returns true, if they are equal
     *  @param array $set1 first set
     *  @param array $set2 second set
     *  @return bool true if they are equal
     */
    private function are_itemsets_equal($set1, $set2) {
        if (count($set1) != count($set2)) {
            return false;
        }
        for($i = 0; $i < count($set1);$i++) {
            /** @var  block_formal_langs_grammar_lr_one_item $s1  */
            /** @var  block_formal_langs_grammar_lr_one_item $s2  */
            $s1 = $set1[$i];
            $s2 = $set2[$i];
            $eq= $s1->is_same($s2);
            if ($eq == false)
                return false;
        }
        return true;
    }
    /** Determines, whether LR(1)-item is kernel
     *  @param block_formal_langs_grammar_lr_one_item $item LR(1) item
     *  @return bool true if kernel
     */
    private function is_kernel_item($item) {
        $lr0 = $item->item();
        $result = false;
        if ($lr0->position() == 0 ) {
            $fst = $this->g->starting_lr1_item()->item();;
            if ($fst->is_same($lr0)) {
                $result = true;
            }
        } else {
            $result = true;
        }
        return $result;
    }

    /** Extracts kernel items from the set
     *  @param array $set of block_formal_langs_grammar_lr_one_item a set of LR(1) items
     *  @return array of of block_formal_langs_grammar_lr_one_item kernel items of LR(1)
     */
    private function extract_kernel_items($set) {
        $result = array();
        for($i = 0; $i < count($set); $i++) {
            if ($this->is_kernel_item($set[$i])) {
                /** @var block_formal_langs_grammar_lr_one_item $seti  */
                $seti = $set[$i];
                // Test whether set is unique
                $contains = false;
                for($j = 0; $j < count($result); $j++) {
                    /** @var block_formal_langs_grammar_lr_one_item $resultj  */
                    $resultj = $result[$j];
                    if ($resultj->is_same($seti->item())) {
                        $contains = true;
                    }
                }

                if ($contains == false) {
                    $result[] = $seti->item();
                }
            }
        }
        return $result;
    }

    /** Replaces sets in LR(1) with same kernel items by their union.
     *  Also unites goto tables to fit replaced data.
     */
    private function replace_sets_with_same_kernel_by_union() {
        $kernels = array();
        // Array, whether merge is used
        $used = array();
        $merge = array();
        // Map set - bucket for goto table merge
        $bucket = array();
        for($i = 0; $i < count($this->lr1items); $i++) {
            $kernels[] = $this->extract_kernel_items($this->lr1items[$i]);
            $used[] = false;
        }

        // Form buckets for unions
        for($i = 0; $i < count($kernels); $i++) {
            if ($used[$i] == false) {
                $used[$i] = true;
                // Merge bucket index
                $bucketindex = count($merge);
                $merge[$bucketindex] = array($i);
                $bucket[$i] = $bucketindex;
                for($j = $i + 1; $j < count($kernels); $j++) {
                    if ($this->are_itemsets_equal($kernels[$i], $kernels[$j])) {
                        $merge[$bucketindex][] = $j;
                        $bucket[$j] = $bucketindex;
                        $used[$j] = true;
                    }
                }
            }
        }
        $new_items = array();
        $new_goto  = array();
        for($i = 0; $i < count($merge); $i++) {
            $new_items[] = $this->union_lr1_set($merge[$i]);
            $new_goto[] = $this->union_lr1_goto($merge[$i],$bucket);
        }

        $this->lr1items = $new_items;
        $this->lr1goto = $new_goto;
    }

    /**
     * Unions LR(1) itemsets in tables, whose indexes, described in a bucket
     * @param array $bucket of int indexes of merged sets
     * @return array LR(1) itemset
     */
    private function union_lr1_set($bucket) {
        $result = array();
        for($bucketindex = 0; $bucketindex < count($bucket); $bucketindex++) {
            $set = $this->lr1items[$bucket[$bucketindex]];
            for($i = 0; $i < count($set); $i++) {
                $contains = false;
                for($j = 0; $j < count($result); $j++) {
                    /** @var  block_formal_langs_grammar_production_symbol $seti  */
                    $seti = $set[$i];
                    if ($seti->is_same($result[$j])) {
                        $contains = true;
                    }
                }
                if ($contains == false) {
                    $result[] = $set[$i];
                }
            }
        }
        return $result;
    }

    /**
     * Unions goto tables in all data
     * @param array $bucket of int indexes of merging sets
     * @param array $mapping of int mapping from source set indexes to new set indexes
     * @return array of new goto rules for data
     */
    private function union_lr1_goto($bucket, $mapping) {
        $result = array();
        for($bucketindex = 0; $bucketindex < count($bucket); $bucketindex++) {
            $setindex = $bucket[$bucketindex];
            if (array_key_exists($setindex, $this->lr1goto) == true) {
                $row = $this->lr1goto[$setindex];
                for($i = 0; $i < count($row); $i++) {
                    $rule = $row[$i];
                    $rule['goto'] = $mapping[$rule['goto']];
                    /** @var block_formal_langs_grammar_production_symbol $sym  */
                    $sym = $rule['symbol'];
                    $contains = false;
                    for($j = 0; $j < count($result); $j++) {
                        if ($sym->is_same($result[$j]['symbol'])) {
                            $contains = true;
                        }
                    }
                    if ($contains == false) {
                        $result[] = $rule;
                    }
                }
            }
        }
        return $result;
    }
}