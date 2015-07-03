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
 * Describes a closure(I) and goto(I,X) functions, as defined in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools
 * p. 236.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/grammar_first.php');
/**
 * Describes a CLOSURE(I) function, as defined in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools
 * p. 236.
 */
class block_formal_langs_grammar_closure {
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

    /**  A closure(I) for LR(1) items as defined in p. 236
     *   @param array $I of block_formal_langs_grammar_lr_one_item LR(1) items
     *   @return array of block_formal_langs_grammar_lr_one_item closures
     */
    public function closure($I) {
        //echo 'Entered closure';
        $QI = $I;
        $RI = $I;
        while(count($QI) != 0) {
            /** @var  block_formal_langs_grammar_lr_one_item $item  */
            $item = array_shift($QI);
            $alpha = $item->item()->partbeforedot();
            $B = $item->item()->dotpart();
            $beta = $item->item()->partafterdot();

            $a = $item->symbol();

            //echo 'Entering first';
            $firstclass= new block_formal_langs_grammar_first($this->g);
            $first = $firstclass->first(array_merge($beta, array($a)));
            //echo 'Left first';

            $definitions = $this->g->get_definitions_for($B->type());
            for($i = 0; $i < count($definitions); $i++) {
                $production = $definitions[$i];
                for($j = 0; $j < count($first); $j++) {
                    $lr0item = new block_formal_langs_grammar_lr_zero_item($production, 0);
                    $lr1item = new block_formal_langs_grammar_lr_one_item( $lr0item , $first[$j]);

                    $contains = false;
                    for($k = 0; $k < count($RI); $k++) {
                        /** @var block_formal_langs_grammar_lr_one_item $RIk  */
                        $RIk = $RI[$k];
                        if ($RIk->is_same($lr1item)) {
                            $contains = true;
                        }
                    }

                    if ($contains == false) {
                        $QI[] = $lr1item;
                        $RI[] = $lr1item;
                    }
                }
            }
        }
        //echo 'Left closure';
        return $RI;
    }
}

/**
 * A class for goto(I,X) function implementation
 */
class block_formal_langs_grammar_goto {

    /** A goto(I,X) function, as defined in Aho, Hopcroft, p. 236. for LR(1)-items
     * @param block_formal_langs_grammar $g  grammar data
     * @param array $II of block_formal_langs_grammar_lr_one_item set of LR(1)-items
     * @param block_formal_langs_grammar_production_symbol $X a symbol a look-ahead symbol
     * @return array of block_formal_langs_grammar_lr_one_item
     */
    public static function run($g, $II, $X) {
        $J = array();
        for($i = 0; $i < count($II); $i++) {
            /** @var block_formal_langs_grammar_lr_one_item $IIi  */
            $IIi = $II[$i];
            if ($IIi->is_dot_part_equals($X) && $IIi->item()->rightcount() > $IIi->item()->position()) {
                $J[] = $IIi->clone_move_dot_forward();
            }
        }
        $result = array();
        if (count($J) != 0) {
            $c = new block_formal_langs_grammar_closure($g);
            $result = $c->closure($J);
        }
        return $result;
    }
}