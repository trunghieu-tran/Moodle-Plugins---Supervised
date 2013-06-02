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
 * Describes a production rules and LR(0) and LR(1) items, as such described in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/symbol_definitions.php');

/**
 * Describes an associativity for operator
 */
class block_formal_langs_grammar_associativity {
    /**
     *  Left associativity for operator
     *  @var int
     */
    public static $left = 1;
    /**
     * Right associativity for operator
     * @var int
     */
    public static $right = 2;
    /**
     * Defines, whether operator is non-associative
     * @var int
     */
    public static $nonassoc = 3;
}
/** A production rule (B->gamma) for reducing symbols into terminals.
 *  Also contains a reduction action, which will be used, when reducing some data
 */
class block_formal_langs_grammar_production_rule {
    /** Left side of rule, that describe a single nonterminal
     *  @var block_formal_langs_grammar_production_symbol
     */
    protected $left;
    /** Right size of rule, that describes a multiple non-terminal symbols
     * @var array of block_formal_langs_grammar_production_symbol
     */
    protected $right;
    /** An action, activated, when some reduction available
     *  @var block_formal_langs_grammar_action action rule
     */
    protected $action;
    /**
     * Defines a precedence for rule. Null if not set
     * @var int|null
     */
    protected $precedence;
    /** Constructs new production rule, with following parameters
     *  @param block_formal_langs_grammar_production_symbol $left left part of production rule
     *  @param array $right of block_formal_langs_grammar_production_symbol right part as sequence of symbols
     *  @param block_formal_langs_grammar_action $action toggled action
     */
    public function __construct($left, $right, $action) {
        $this->left = $left;
        $this->right = $right;
        $this->action = $action;
        $this->precedence = null;
    }

    /**
     * Returns precedence of rule
     * @return int|null
     */
    public function precedence() {
        return $this->precedence;
    }

    /**
     * Sets a precedence for data
     * @param int $prec
     */
    public function set_precedence($prec) {
        $this->precedence = $prec;
    }
    /** Returns a left part of rule, that describes non-terminal
     *  @return block_formal_langs_grammar_production_symbol
     */
    public function left() {
        return $this->left;
    }
    /** Returns a count of symbols in right part of rule
     *  @return int
     */
    public function rightcount() {
        return count($this->right);
    }
    /** Returns an element from sequence in a right part of rule
     *  @param int $i index
     *  @return block_formal_langs_grammar_production_symbol
     */
    public function right($i) {

        return $this->right[$i];
    }
    /** Returns an action, assigned to a rule
     * @return block_formal_langs_grammar_action
     */
    public function action() {
        return $this->action;
    }

    /**
     * Returns a string representation of rule, without
     * description of action
     * @return string
     */
    public function tostring() {
        $type = $this->left()->type();

        $rs = array();
        for($i = 0; $i < $this->rightcount(); $i++) {
            $rs[] = $this->right($i)->type();
        }
        return $type . '->' . implode(' ', $rs);
    }
    /** Tests, whether is two rules are equiualent, don't checking some action
     *  @param block_formal_langs_grammar_production_rule $o rule to check with
     *  @return bool true, whether two rules are same
     */
    public function is_same($o) {
        $val = $this->left()->is_same($o->left());
        $val = $val && $this->rightcount() == $o->rightcount();
        if ($val != false) {
            for($i = 0; $i < $this->rightcount(); $i++) {
                $val = $val && $this->right($i)->is_same($o->right($i));
            }
        }
        return $val;
    }
}

/**  And LR(0)-item is a production rule with point, marking current position of sequence
 *   cursor in reducing sequence
 */
class block_formal_langs_grammar_lr_zero_item {
    /** A production rule for LR(0) item
     *  @var block_formal_langs_grammar_production_rule
     */
    protected $rule;
    /** A position of cursor in sequence.
     *   0 is position, before first item,
     *  ::rightcount() is position, after last item
     *  @var int
     */
    protected $position;


    /** Constructs an LR(0)-item
     *  @param block_formal_langs_grammar_production_rule $rule rule, defining item
     *  @param int $pos position of cursor in sequence in right side of rule
     */
    public function __construct($rule, $pos) {
        $this->rule = $rule;
        $this->position = $pos;
        assert($this->position <= $rule->rightcount());
    }
    /** Returns a rule, which LR(0)-item corresponds to
     *  @return block_formal_langs_grammar_production_rule
     */
    public function rule() {
        return $this->rule;
    }
    /** Returns a left part of rule, describing a non-terminal
     *  @return block_formal_langs_grammar_production_symbol
     */
    public function left() {
        return $this->rule->left();
    }
    /** Returns a count of sequence in right part of production rule
     *  @return int
     */
    public function rightcount() {
        return $this->rule->rightcount();
    }
    /** Returns an element from sequence in a right part of rule
     *  @param int $i index
     *  @return block_formal_langs_grammar_production_symbol
     */
    public function right($i) {
        return $this->rule->right($i);
    }
    /** Returns an action, assigned to a rule
     *  @return block_formal_langs_grammar_action
     */
    public function action() {
        return $this->rule->action();
    }
    /** Returns a position of text cursor position in right part of rule
     *  @return int
     */
    public function position() {
        return $this->position;
    }
    /** Returns a subsequence from right part of rule
     *  @param int      $from starting position of subsequence
     *  @param int|null $to   ending position of subsequence. Element with such index will not be included.
     *  @return array of block_formal_langs_grammar_production_symbol
     */
    protected function subsequence($from, $to) {
        if ($to == null) {
            $to = $this->rule->rightcount();
        }
        $result = array();
        for ($i = $from; $i < $to; $i++) {
            $result[] = $this->rule->right($i);
        }
        return $result;
    }
    /** Returns a part of sequence, which goes before text cursor or epsilon, if text-cursor
     *  is before all symbols
     *  @return array of block_formal_langs_grammar_production_symbol
     */
    public function partbeforedot() {
        $result = array();

        if ($this->position == 0) {
            $result[] = new block_formal_langs_grammar_epsilon_symbol();
        } else {
            $result = $this->subsequence(0, $this->position);
        }
        return $result;
    }
    /** Returns a symbol, which is after text-cursor. Returns epsilon, when at end of
     *  sequence
     *  @return block_formal_langs_grammar_production_symbol
     */
    public function dotpart() {
        $result = new block_formal_langs_grammar_epsilon_symbol();
        if ($this->position != $this->rightcount()) {
            $result = $this->rule->right($this->position);
        }
        return $result;
    }
    /** Returns a part of sequence, which starts after one position, after a text-cursor,
     *  or epsilon, if at end
     *  @return array of block_formal_langs_grammar_production_symbol
     */
    public function partafterdot() {
        $result = array();
        if ($this->position + 1 >= $this->rightcount()) {
            $result[] = new block_formal_langs_grammar_epsilon_symbol();
        } else {
            $result = $this->subsequence($this->position + 1, null);
        }
        return $result;
    }

    /**
     * Returns a string representation of LR(0)-item
     * @return string
     */
    public function tostring() {
        $type = $this->left()->type();

        $wtype = '';
        for($i = 0; $i < $this->rightcount(); $i++) {
            if ($i != 0) {
                $wtype .= ' ';
            }
            if ($i == $this->position) {
                $wtype .= '.';
            }
            $rtype = $this->right($i)->type();
            $wtype .= $rtype;
        }
        if ($this->position == $this->rightcount()) {
            $wtype .= '.';
        }
        return $type . '->' . $wtype;
    }
    /** Clones self with specified position of text-cursor
     *  @param int $pos new position of text cursor
     *  @return block_formal_langs_grammar_lr_zero_item
     */
    public function clone_with_position($pos) {
        return new block_formal_langs_grammar_lr_zero_item(clone $this->rule, $pos);
    }
    /** Tests, whether two LR(0) items are the same
     *  @param  block_formal_langs_grammar_lr_zero_item $o other LR(0)-item to check with
     *  @return bool true, if they are the same
     */
    public function is_same($o) {
        $val = $this->rule->is_same($o->rule);
        $val = $val && $this->position == $o->position;
        return $val;
    }
}

/** LR(1) grammar item is a LR(0)-item with look-ahead symbol
 */
class block_formal_langs_grammar_lr_one_item {
    /** LR(0) item
     *  @var block_formal_langs_grammar_lr_zero_item
     */
    protected $item;
    /** A look-ahead symbol
     *  @var block_formal_langs_grammar_production_symbol
     */
    protected $symbol;

    /** Constructs new LR(1) item
     * @param block_formal_langs_grammar_lr_zero_item $item LR(0)-item
     * @param block_formal_langs_grammar_production_symbol $symbol look-ahead symbol
     */
    public function __construct($item, $symbol) {
        $this->item = $item;
        $this->symbol = $symbol;
    }
    /** Returns LR(0)-item
     *  @return block_formal_langs_grammar_lr_zero_item
     */
    public function item() {
        return $this->item;
    }
    /** Returns look-ahead symbol
     *  @return block_formal_langs_grammar_production_symbol look-ahead symbol
     */
    public function symbol() {
        return $this->symbol;
    }

    /**
     * Returns a string representation of LR(1) item
     * @return string
     */
    public function tostring() {
        return '[' . $this->item->tostring() . ', ' . $this->symbol->type() . ']';
    }

    /** Tests, whether is two rules are the same
    @param block_formal_langs_grammar_lr_one_item $o same
    @return bool whether two rules are same
     */
    public function is_same($o) {
        $val = $this->item->is_same($o->item);
        $val = $val && $this->symbol->is_same($o->symbol);
        return $val;
    }

    /** Clones self, returning a copy, where text-cursor is moved one step forward
     * @return block_formal_langs_grammar_lr_one_item
     */
    public function clone_move_dot_forward() {
        $item = $this->item->clone_with_position($this->item->position()+1);
        return new block_formal_langs_grammar_lr_one_item($item, clone $this->symbol);
    }
    /** Tests, whether marked symbol with text-cursor, equals supplied
     *  @param block_formal_langs_grammar_production_symbol $symbol a symbol, to test with
     *  @return boolean true if text cursor marked symbol is same as supplied
     */
    public function is_dot_part_equals($symbol) {
        return $this->item->dotpart()->is_same($symbol);
    }
}