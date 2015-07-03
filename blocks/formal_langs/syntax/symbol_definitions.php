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
 * A definition and functions, need to work with some formal grammar symbols,
 * as defined in
 * A.V. Aho, R. Sethi, J. D. Ullman. Compilers: Principles, Techniques, and Tools.
 *
 * @package    blocks
 * @subpackage formal_langs
 * @copyright &copy; 2011 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev, Mamontov Dmitriy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
require_once($CFG->dirroot.'/blocks/formal_langs/syntax/debug_log.php');
/** A common symbol, which can be used, when  descripting a
 *  production rule in left or right part of it
 */
abstract class block_formal_langs_grammar_production_symbol {
    /** Type of symbol (return string type representation)
     *  @return string string of data
     */
    abstract public function type();
    /** Whether it is epsilon or empty symbol, that doesn't consume nothing
     *  @return bool whether it is epsilon
     */
    public function is_epsilon() {
        return false;
    }
    /** Whether it is a sequence-ending $ symbol
     *  @return bool whether it it sequence-ending symbol
     */
    public function is_dollar() {
        return false;
    }
    /** Compares two production symbols
     *  @param block_formal_langs_grammar_production_symbol $o other symbol
     *  @return bool true, whether two symbols, are the same
     */
    public function is_same($o) {
        return $this->type() == $o->type()
            && $this->is_epsilon() == $o->is_epsilon()
            && $this->is_dollar() == $o->is_dollar();
    }
}

/** A sequence-ending symbol, which can be used, when describing a grammar rule
 */
class block_formal_langs_grammar_dollar_symbol extends block_formal_langs_grammar_production_symbol {
    /**
     * Returns '$' symbol, because that's like it is described in the book
     * @return string type data
     */
    public function type() {
        return '$';
    }

    /**
     * Marks an sequence ending special symbol
     * @return bool true
     */
    public function is_dollar() {
        return true;
    }
}

/** An epsilon symbol, which marks special "empty" symbol, when nothing is found
 */
class block_formal_langs_grammar_epsilon_symbol extends block_formal_langs_grammar_production_symbol {
    /**
     * Returns small greek letter epsilon, like it's described in the book
     * @return string type data
     */
    public function type() {
        return "\u0395";
    }

    /**
     * Marks that symbol as special "empty" symbol
     * @return bool true
     */
    public function is_epsilon() {
        return true;
    }
}
/** A common symbol, that can be used in production rules
 */
class block_formal_langs_grammar_common_symbol extends block_formal_langs_grammar_production_symbol {
    /** A string, that defines a type of symbol
     *  @var string
     */
    protected $type;
    /** Constructs a new symbol with defined type
     *  @param string $type type of symbol
     */
    public function __construct($type) {
        $this->type = $type;
    }

    /**
     * Returns a type, defined when constructing a symbol
     * @return string
     */
    public function type() {
        return $this->type;
    }

}

/**
 * A boxed ast-node, which holds returned by reduction ast node or terminal, with type,
 * defined by it's construction
 */
class block_formal_langs_grammar_boxed_ast_node {
    /**
     * A real node, saved in depth of this class, to make parsing simple
     * @var mixed
     */
    protected  $realnode;
    /**
     * A supplied non-terminal type, as defined in grammar
     * @var string
     */
    protected  $type;

    /**
     * Constructs a new boxed ast node, with defined non-terminal type and inner node
     * @param string $type a type of boxed ast node, as defined in grammar
     * @param mixed $realnode a returned ast node
     */
    public function __construct($type, $realnode) {
        $this->realnode = $realnode;
        $this->type = $type;
    }

    /**
     * Returns a type of node, as defined in a grammar
     * @return string
     */
    public function type() {
        return $this->type;
    }

    /**
     * Unboxed hidden value hidden in ast_node
     * @return mixed
     */
    public function unbox() {
        return $this->realnode;
    }
}
/**
 *  An action, which must be activated, when rule reduction occurs
 *  For most time - it must return object, which represents an ast_node, with correct
 *  method type(), which returns non-terminal name. Also, lexer terminal nodes,
 *  must also have this method, which returns a string with terminal name
 */
abstract class block_formal_langs_grammar_action {
    /**
     * A function, used by parser for reducing some rule, returning a
     * boxed block_formal_langs_grammar_boxed_ast_node and calling
     * ::reduce to work with real values
     * @param block_formal_langs_grammar_production_rule $rule a rule, applied to children
     * @param array $children of block_formal_langs_grammar_boxed_ast_node
     * @return block_formal_langs_grammar_boxed_ast_node resulting node
     */
    public function apply($rule, $children) {
        $realchildren = array();
        for($i = 0; $i < count($children); $i++) {
            /** @var block_formal_langs_grammar_boxed_ast_node $child  */
            $child = $children[$i];
            $realchildren[] = $child->unbox();
        }
        $result = $this->reduce($rule, $realchildren);
        $type = $rule->left()->type();
        return new block_formal_langs_grammar_boxed_ast_node($type, $result);
    }
    /** Applies a reduction rule, returning an ast-node, that represents a non-terminal,
     *  Reimplement this function to return your ast-node.
     *  on a left-side of rule.
     *  @param block_formal_langs_grammar_production_rule $rule applied rule
     *  @param array $children of block_formal_langs_ast_node Right-side node data
     *  @return mixed something, that represents an ast-node with type method
     */
    abstract public function reduce($rule, $children);
}

/**
 * A fake action, which will be used for rules, which are generated by a generator.
 * Those are:
 * S' -> S - fake rule, to simulate accept event
 * Concatenative rules, to combine all of multiple starting symbols, if they are presented
 * in grammar.
 * This rule does nothing, but returns a first child
 */
class block_formal_langs_grammar_replace_action extends block_formal_langs_grammar_action {
    /** Returns a first child, thus replacing top node with bottom
     *  @param block_formal_langs_grammar_production_rule $rule applied rule
     *  @param array $children of block_formal_langs_ast_node Right-side node data
     *  @return mixed something, that represents an ast-node with type method
     */
    public function reduce($rule, $children) {
        return $children[0];
    }
}