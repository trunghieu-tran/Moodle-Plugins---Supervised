<?php
/* Driver template for the PHP_qtype_preg_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class qtype_preg_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof qtype_preg_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof qtype_preg_yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof qtype_preg_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof qtype_preg_yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class qtype_preg_yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                     ** is the value of the token  */
};

// code external to the class is included here
#line 2 "../preg_parser.y"

    global $CFG;
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
#line 104 "../preg_parser.php"

// declare_class is output here
#line 7 "../preg_parser.y"
class qtype_preg_parser#line 109 "../preg_parser.php"
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 8 "../preg_parser.y"

    // Root of the Abstract Syntax Tree (AST).
    private $root;

    // Objects of qtype_preg_node_error for errors found during parsing.
    private $errornodes;

    // Handling options.
    private $options;

    // Counter of nodes id. After parsing, equals the number of nodes in the tree.
    private $id_counter;

    // Counter of subpatterns.
    private $subpatt_counter;

    // Map subpattern number => subpattern node.
    private $subpatt_number_to_node_map;

    // Map subexpression number => nodes (possibly duplicates).
    private $subexpr_number_to_nodes_map;

    public function __construct($options = null) {
        $this->root = null;
        $this->errornodes = array();
        if ($options == null) {
            $options = new qtype_preg_handling_options();
        }
        $this->options = $options;
        $this->id_counter = 0;
        $this->subpatt_counter = 0;
        $this->subpatt_number_to_node_map = array();
        $this->subexpr_number_to_nodes_map = array();
    }

    public function get_root() {
        return $this->root;
    }

    public function errors_exist() {
        return (count($this->errornodes) > 0);
    }

    public function get_error_nodes() {
        return $this->errornodes;
    }

    public function get_max_id() {
        return $this->id_counter;
    }

    public function set_max_id($value) {
        $this->id_counter = $value;
    }

    public function get_max_subpatt() {
        return $this->subpatt_counter - 1;
    }

    public function get_subpatt_number_to_node_map() {
        return $this->subpatt_number_to_node_map;
    }

    public function get_subexpr_number_to_nodes_map() {
        return $this->subexpr_number_to_nodes_map;
    }

    /**
     * Creates and returns an error node, also adds it to the array of parser errors
     */
    protected function create_error_node($subtype, $addinfo, $position, $userinscription, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($position, $userinscription);
        $newnode->operands = $operands;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
      * Creates and return correct parenthesis node (subexpression, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param operator parenthesis token from lexer
      * @param operand the node for expression inside parenthesis
      */
    protected function create_parens_node($operator, $operand, $closeparen) {
        $position = $operator->position->compose($closeparen->position);
        $result = $operator;
        $result->operands[0] = $operand;
        $result->set_user_info($position, array(new qtype_preg_userinscription($operator->userinscription[0] . '...)')));
        return $result;
    }

    protected function add_cond_subexpr_negative_branch($node) {
        $shift = (int)$node->is_condition_assertion();

        // Add an eps leaf if there's only positive branch.
        if (count($node->operands) - $shift == 1) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($node->position->right()->add_chars_right(-1));
            $node->operands[] = $epsleaf;
        }
    }

    protected function create_cond_subexpr_assertion_node($node, $assertbody, $exprnode, $closeparen) {
        if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($node->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }

        if ($assertbody === null) {
            $assertbody = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $startpos = $node->position->indlast;
            $assertbody->set_user_info(new qtype_preg_position($startpos + 1, $startpos));
        }
        $condbranch = new qtype_preg_node_assert($subtype);
        $condbranch->operands = array($assertbody);
        $condbranch->set_user_info($node->position->compose($assertbody->position)->add_chars_left(2)->add_chars_right(1),
                                   array(new qtype_preg_userinscription(core_text::substr($node->userinscription[0], 2) . '...)')));

        $node->operands = array($condbranch);

        $position = $node->position->compose($closeparen->position);

        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($node->position->add_chars_left(1));
        }
        if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $node->operands = array_merge($node->operands, $exprnode->operands);
            if (count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, array(), array($exprnode));
            }
        } else {
            $node->operands[] = $exprnode;
        }

        $this->add_cond_subexpr_negative_branch($node);

        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . '...)...|...)')));
        return $node;
    }

    protected function create_cond_subexpr_other_node($node, $exprnode, $closeparen) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($node->position->add_chars_left(1));
        }

        $position = $node->position->compose($closeparen->position);

        if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT) {
            $node->operands = $exprnode->operands;
            if ($node->subtype == qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE || count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, array(), array($exprnode));
            }
        } else {
            $node->operands = array($exprnode);
        }

        $ui = implode('|', array_fill(0, count($node->operands), '...')) . ')';

        $this->add_cond_subexpr_negative_branch($node);

        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . $ui)));
        return $node;
    }

    protected function build_subexpr_number_to_nodes_map($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->build_subexpr_number_to_nodes_map($operand);
            }
        }
        if ($node->subtype == qtype_preg_node_subexpr::SUBTYPE_SUBEXPR) {
            if (!array_key_exists($node->number, $this->subexpr_number_to_nodes_map)) {
                $this->subexpr_number_to_nodes_map[$node->number] = array();
            }
            $this->subexpr_number_to_nodes_map[$node->number][] = $node;
        }
    }

    protected function detect_recursive_subexpr_calls($node, $currentsubexprs = array(0)) {
        if ($node->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL) {
            $node->isrecursive = in_array($node->number, $currentsubexprs);
        }

        $newsubexprs = $currentsubexprs;
        if ($node->type == qtype_preg_node::TYPE_NODE_SUBEXPR) {
            if ($node->number != -1 && !in_array($node->number, $newsubexprs)) {
                $newsubexprs[] = $node->number;
            }
        }

        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->detect_recursive_subexpr_calls($operand, $newsubexprs);
            }
        }
    }

    protected function replace_non_recursive_subexpr_calls($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->replace_non_recursive_subexpr_calls($operand);
            }
        }

        if ($node->type == qtype_preg_node::TYPE_LEAF_SUBEXPR_CALL && !$node->isrecursive && array_key_exists($node->number, $this->subexpr_number_to_nodes_map)) {

            // According to pcre.txt, we are able to replace the node.
            // Options affected the (?n) leaf do not affect the original node.
            // But we shoud treat the replacement node as an atomic group.
            $node = clone $this->subexpr_number_to_nodes_map[$node->number][0];
            $node->subtype = qtype_preg_node_subexpr::SUBTYPE_GROUPING;   // TODO: onceonly.
        }

        return $node;
    }

    protected function expand_quantifiers($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->expand_quantifiers($operand);
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            if ($node->leftborder == 0 && $node->rightborder == 0) {
                // Convert x{0} to emptiness.
                $node = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            } else if ($node->rightborder > 1) {
                // Expand finite quantifier to a sequence like xxxxx?x?x?x?
                $concat = new qtype_preg_node_concat();
                for ($i = 0; $i < $node->rightborder; $i++) {
                    $operand = clone $node->operands[0];
                    if ($i >= $node->leftborder) {
                        $qu = new qtype_preg_node_finite_quant(0, 1);
                        $qu->operands[] = $operand;
                        $operand = $qu;
                    }
                    $concat->operands[] = $operand;
                }
                $node = $concat;
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_INFINITE_QUANT && $node->leftborder > 1) {
            // Expand finite quantifier to a sequence like xxxx+
            $concat = new qtype_preg_node_concat();
            for ($i = 0; $i < $node->leftborder; $i++) {
                $operand = clone $node->operands[0];
                if ($i == $node->leftborder - 1) {
                    $plus = new qtype_preg_node_infinite_quant(1);
                    $plus->operands[] = $operand;
                    $operand = $plus;
                }
                $concat->operands[] = $operand;
            }
            $node = $concat;
        }
        return $node;
    }

    protected function assign_subpatts($node) {
        if ($node->is_subpattern() || $node === $this->root) {
            $node->subpattern = $this->subpatt_counter++;
            $this->subpatt_number_to_node_map[$node->subpattern] = $node;
        }
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->assign_subpatts($operand);
            }
        }
    }

    protected function assign_ids($node) {
        $node->id = ++$this->id_counter;
        if (is_a($node, 'qtype_preg_operator')) {
            foreach ($node->operands as $operand) {
                $this->assign_ids($operand);
            }
        }
    }

    protected static function is_alt_nullable($altnode) {
        foreach ($altnode->operands as $operand) {
            if ($operand->type == qtype_preg_node::TYPE_LEAF_META && $operand->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return true;
            }
        }
        return false;
    }
#line 413 "../preg_parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const ERROR_PREC_SHORTEST            =  1;
    const ERROR_PREC_SHORT               =  2;
    const ERROR_PREC                     =  3;
    const CLOSEBRACK                     =  4;
    const TEMPLATECLOSEBRACK             =  5;
    const TEMPLATESEP_SHORTEST           =  6;
    const TEMPLATESEP_SHORT              =  7;
    const TEMPLATESEP                    =  8;
    const ALT_SHORTEST                   =  9;
    const ALT_SHORT                      = 10;
    const ALT                            = 11;
    const CONC                           = 12;
    const PARSELEAF                      = 13;
    const TEMPLATEPARSELEAF              = 14;
    const QUANT                          = 15;
    const OPENBRACK                      = 16;
    const TEMPLATEOPENBRACK              = 17;
    const CONDSUBEXPR                    = 18;
    const YY_NO_ACTION = 72;
    const YY_ACCEPT_ACTION = 71;
    const YY_ERROR_ACTION = 70;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 231;
static public $yy_action = array(
 /*     0 */    27,   35,   71,    4,   12,   20,   17,   15,    1,   24,
 /*    10 */    23,   34,   10,    9,    8,   30,   31,   57,    3,   12,
 /*    20 */     5,   19,   15,    6,   24,   23,   34,   10,    9,    8,
 /*    30 */    26,   36,   57,   11,   13,   18,   14,   16,   57,   24,
 /*    40 */    23,   28,   10,    9,    8,   33,   35,   57,   57,   12,
 /*    50 */    57,   57,   15,   57,   24,   23,   34,   10,    9,    8,
 /*    60 */    25,   36,   57,   57,   13,   57,   57,   16,   57,   24,
 /*    70 */    23,   28,   10,    9,    8,    7,   36,   57,   57,   13,
 /*    80 */    57,   57,   16,   57,   24,   23,   28,   10,    9,    8,
 /*    90 */    29,   22,   57,   57,   13,   57,   57,   16,   57,   24,
 /*   100 */    23,   28,   10,    9,    8,   30,   35,   57,   57,   12,
 /*   110 */    57,   57,   15,   57,   24,   23,   34,   10,    9,    8,
 /*   120 */    29,   36,   57,   57,   13,   57,   57,   16,   57,   24,
 /*   130 */    23,   28,   10,    9,    8,   32,   35,   57,   57,   12,
 /*   140 */    57,   57,   15,   57,   24,   23,   34,   10,    9,    8,
 /*   150 */     2,   35,   57,   57,   12,   57,   57,   15,   57,   24,
 /*   160 */    23,   34,   10,    9,    8,   21,   36,   57,   57,   13,
 /*   170 */    57,   57,   16,   57,   24,   23,   28,   10,    9,    8,
 /*   180 */    57,   13,   57,   57,   16,   57,   24,   23,   28,   10,
 /*   190 */     9,    8,   57,   12,   57,   57,   15,   57,   24,   23,
 /*   200 */    34,   10,    9,    8,   57,   16,   57,   24,   23,   28,
 /*   210 */    10,    9,    8,   15,   57,   24,   23,   34,   10,    9,
 /*   220 */     8,   24,   23,   34,   10,    9,    8,   34,   10,    9,
 /*   230 */     8,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,   20,   21,    8,   21,   21,   11,   21,   13,
 /*    10 */    14,   15,   16,   17,   18,    4,    5,   22,   21,    8,
 /*    20 */    21,   21,   11,   21,   13,   14,   15,   16,   17,   18,
 /*    30 */     4,    5,   22,   21,    8,   21,   21,   11,   22,   13,
 /*    40 */    14,   15,   16,   17,   18,    4,    5,   22,   22,    8,
 /*    50 */    22,   22,   11,   22,   13,   14,   15,   16,   17,   18,
 /*    60 */     4,    5,   22,   22,    8,   22,   22,   11,   22,   13,
 /*    70 */    14,   15,   16,   17,   18,    4,    5,   22,   22,    8,
 /*    80 */    22,   22,   11,   22,   13,   14,   15,   16,   17,   18,
 /*    90 */     4,    5,   22,   22,    8,   22,   22,   11,   22,   13,
 /*   100 */    14,   15,   16,   17,   18,    4,    5,   22,   22,    8,
 /*   110 */    22,   22,   11,   22,   13,   14,   15,   16,   17,   18,
 /*   120 */     4,    5,   22,   22,    8,   22,   22,   11,   22,   13,
 /*   130 */    14,   15,   16,   17,   18,    4,    5,   22,   22,    8,
 /*   140 */    22,   22,   11,   22,   13,   14,   15,   16,   17,   18,
 /*   150 */     4,    5,   22,   22,    8,   22,   22,   11,   22,   13,
 /*   160 */    14,   15,   16,   17,   18,    4,    5,   22,   22,    8,
 /*   170 */    22,   22,   11,   22,   13,   14,   15,   16,   17,   18,
 /*   180 */    22,    8,   22,   22,   11,   22,   13,   14,   15,   16,
 /*   190 */    17,   18,   22,    8,   22,   22,   11,   22,   13,   14,
 /*   200 */    15,   16,   17,   18,   22,   11,   22,   13,   14,   15,
 /*   210 */    16,   17,   18,   11,   22,   13,   14,   15,   16,   17,
 /*   220 */    18,   13,   14,   15,   16,   17,   18,   15,   16,   17,
 /*   230 */    18,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 20;
    static public $yy_shift_ofst = array(
 /*     0 */   116,  131,  161,  146,  101,   11,   -4,   26,   71,   86,
 /*    10 */    56,   41,  173,  173,  185,  194,  194,  202,  202,  208,
 /*    20 */   212,
);
    const YY_REDUCE_USE_DFLT = -19;
    const YY_REDUCE_MAX = 20;
    static public $yy_reduce_ofst = array(
 /*     0 */   -18,  -16,  -13,  -16,  -16,  -16,  -16,    2,   -3,   -1,
 /*    10 */    12,  -16,   14,   15,  -16,    0,  -15,  -16,  -16,  -16,
 /*    20 */   -16,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 1 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 2 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 3 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 4 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 5 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 6 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 7 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 8 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 9 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 10 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 11 */ array(4, 5, 8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 12 */ array(8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 13 */ array(8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 14 */ array(8, 11, 13, 14, 15, 16, 17, 18, ),
        /* 15 */ array(11, 13, 14, 15, 16, 17, 18, ),
        /* 16 */ array(11, 13, 14, 15, 16, 17, 18, ),
        /* 17 */ array(11, 13, 14, 15, 16, 17, 18, ),
        /* 18 */ array(11, 13, 14, 15, 16, 17, 18, ),
        /* 19 */ array(13, 14, 15, 16, 17, 18, ),
        /* 20 */ array(15, 16, 17, 18, ),
        /* 21 */ array(),
        /* 22 */ array(),
        /* 23 */ array(),
        /* 24 */ array(),
        /* 25 */ array(),
        /* 26 */ array(),
        /* 27 */ array(),
        /* 28 */ array(),
        /* 29 */ array(),
        /* 30 */ array(),
        /* 31 */ array(),
        /* 32 */ array(),
        /* 33 */ array(),
        /* 34 */ array(),
        /* 35 */ array(),
        /* 36 */ array(),
);
    static public $yy_default = array(
 /*     0 */    70,   66,   58,   67,   37,   63,   70,   60,   68,   65,
 /*    10 */    64,   62,   53,   55,   54,   42,   44,   43,   52,   41,
 /*    20 */    40,   49,   56,   39,   38,   46,   51,   50,   69,   60,
 /*    30 */    58,   57,   48,   47,   45,   59,   61,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 23;
    const YYSTACKDEPTH = 100;
    const qtype_preg_ARG_DECL = '0';
    const YYNSTATE = 37;
    const YYNRULE = 33;
    const YYERRORSYMBOL = 19;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx = -1;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array( 
  '$',             'ERROR_PREC_SHORTEST',  'ERROR_PREC_SHORT',  'ERROR_PREC',  
  'CLOSEBRACK',    'TEMPLATECLOSEBRACK',  'TEMPLATESEP_SHORTEST',  'TEMPLATESEP_SHORT',
  'TEMPLATESEP',   'ALT_SHORTEST',  'ALT_SHORT',     'ALT',         
  'CONC',          'PARSELEAF',     'TEMPLATEPARSELEAF',  'QUANT',       
  'OPENBRACK',     'TEMPLATEOPENBRACK',  'CONDSUBEXPR',   'error',       
  'start',         'expr',        
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= expr",
 /*   1 */ "expr ::= PARSELEAF",
 /*   2 */ "expr ::= TEMPLATEPARSELEAF",
 /*   3 */ "expr ::= expr expr",
 /*   4 */ "expr ::= expr ALT expr",
 /*   5 */ "expr ::= expr ALT",
 /*   6 */ "expr ::= ALT expr",
 /*   7 */ "expr ::= ALT",
 /*   8 */ "expr ::= expr QUANT",
 /*   9 */ "expr ::= OPENBRACK CLOSEBRACK",
 /*  10 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*  11 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr CLOSEBRACK",
 /*  12 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK CLOSEBRACK",
 /*  13 */ "expr ::= CONDSUBEXPR CLOSEBRACK expr CLOSEBRACK",
 /*  14 */ "expr ::= CONDSUBEXPR CLOSEBRACK CLOSEBRACK",
 /*  15 */ "expr ::= expr TEMPLATESEP expr",
 /*  16 */ "expr ::= expr TEMPLATESEP",
 /*  17 */ "expr ::= TEMPLATESEP expr",
 /*  18 */ "expr ::= TEMPLATESEP",
 /*  19 */ "expr ::= TEMPLATEOPENBRACK TEMPLATECLOSEBRACK",
 /*  20 */ "expr ::= TEMPLATEOPENBRACK expr TEMPLATECLOSEBRACK",
 /*  21 */ "expr ::= expr CLOSEBRACK",
 /*  22 */ "expr ::= expr TEMPLATECLOSEBRACK",
 /*  23 */ "expr ::= CLOSEBRACK",
 /*  24 */ "expr ::= TEMPLATECLOSEBRACK",
 /*  25 */ "expr ::= OPENBRACK expr",
 /*  26 */ "expr ::= TEMPLATEOPENBRACK expr",
 /*  27 */ "expr ::= OPENBRACK",
 /*  28 */ "expr ::= TEMPLATEOPENBRACK",
 /*  29 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr",
 /*  30 */ "expr ::= CONDSUBEXPR expr",
 /*  31 */ "expr ::= CONDSUBEXPR",
 /*  32 */ "expr ::= QUANT",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param qtype_preg_yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::$yyTokenName[$yytos->major] .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new qtype_preg_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new qtype_preg_yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::$yyTokenName[$iLookAhead] . " => " .
                        self::$yyTokenName[$iFallback] . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new qtype_preg_yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::$yyTokenName[$this->yystack[$i]->major]);
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 20, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 3 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 3 ),
  array( 'lhs' => 21, 'rhs' => 5 ),
  array( 'lhs' => 21, 'rhs' => 4 ),
  array( 'lhs' => 21, 'rhs' => 4 ),
  array( 'lhs' => 21, 'rhs' => 3 ),
  array( 'lhs' => 21, 'rhs' => 3 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 3 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 4 ),
  array( 'lhs' => 21, 'rhs' => 2 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        17 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        18 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 23,
        24 => 24,
        25 => 25,
        30 => 25,
        26 => 26,
        27 => 27,
        31 => 27,
        28 => 28,
        29 => 29,
        32 => 32,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 324 "../preg_parser.y"
    function yy_r0(){
    // Set the root node.
    $this->root = $this->yystack[$this->yyidx + 0]->minor;

    // Build subexpr map.
    $this->build_subexpr_number_to_nodes_map($this->root);

    // Calculate recursive subexpression calls.
    $this->detect_recursive_subexpr_calls($this->root);

    // Replace non-recursive subexpression calls if needed.
    if ($this->options->replacesubexprcalls) {
        $this->root = $this->replace_non_recursive_subexpr_calls($this->root);
    }

    // Expand quantifiers if needed.
    if ($this->options->expandquantifiers) {
        $this->root = $this->expand_quantifiers($this->root);
    }

    // Assign subpattern numbers.
    $this->assign_subpatts($this->root);

     // Assign identifiers.
    $this->assign_ids($this->root);

    // Calculate nullable, firstpos, lastpos and followpos for all nodes.
    $followpos = array();   // TODO: make a field + getter if this is needed
    $this->root->calculate_nflf($followpos);
    }
#line 1235 "../preg_parser.php"
#line 355 "../preg_parser.y"
    function yy_r1(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1240 "../preg_parser.php"
#line 359 "../preg_parser.y"
    function yy_r2(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists($this->_retvalue->name, $available)) {
        $template = $available[$this->_retvalue->name];
        if ($template->placeholderscount > 0) {
            // Template node called as a leaf
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, $this->_retvalue->name, $this->_retvalue->position, $this->_retvalue->userinscription, array());
        }
    }
    }
#line 1253 "../preg_parser.php"
#line 371 "../preg_parser.y"
    function yy_r3(){
    if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_NODE_CONCAT && $this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_CONCAT) {
        $this->yystack[$this->yyidx + -1]->minor->operands = array_merge($this->yystack[$this->yyidx + -1]->minor->operands, $this->yystack[$this->yyidx + 0]->minor->operands);
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_NODE_CONCAT && $this->yystack[$this->yyidx + 0]->minor->type != qtype_preg_node::TYPE_NODE_CONCAT) {
        $this->yystack[$this->yyidx + -1]->minor->operands[] = $this->yystack[$this->yyidx + 0]->minor;
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else if ($this->yystack[$this->yyidx + -1]->minor->type != qtype_preg_node::TYPE_NODE_CONCAT && $this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_CONCAT) {
        $this->yystack[$this->yyidx + 0]->minor->operands = array_merge(array($this->yystack[$this->yyidx + -1]->minor), $this->yystack[$this->yyidx + 0]->minor->operands);
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else {
        $this->_retvalue = new qtype_preg_node_concat();
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + -1]->minor;
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + 0]->minor;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position));
    }
#line 1272 "../preg_parser.php"
#line 389 "../preg_parser.y"
    function yy_r4(){
    if ($this->yystack[$this->yyidx + -2]->minor->type == qtype_preg_node::TYPE_NODE_ALT && $this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        $this->yystack[$this->yyidx + -2]->minor->operands = array_merge($this->yystack[$this->yyidx + -2]->minor->operands, $this->yystack[$this->yyidx + 0]->minor->operands);
        $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    } else if ($this->yystack[$this->yyidx + -2]->minor->type == qtype_preg_node::TYPE_NODE_ALT && $this->yystack[$this->yyidx + 0]->minor->type != qtype_preg_node::TYPE_NODE_ALT) {
        $this->yystack[$this->yyidx + -2]->minor->operands[] = $this->yystack[$this->yyidx + 0]->minor;
        $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    } else if ($this->yystack[$this->yyidx + -2]->minor->type != qtype_preg_node::TYPE_NODE_ALT && $this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        $this->yystack[$this->yyidx + 0]->minor->operands = array_merge(array($this->yystack[$this->yyidx + -2]->minor), $this->yystack[$this->yyidx + 0]->minor->operands);
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else {
        $this->_retvalue = new qtype_preg_node_alt();
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + -2]->minor;
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + 0]->minor;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -2]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + -1]->minor->userinscription);
    }
#line 1291 "../preg_parser.php"
#line 407 "../preg_parser.y"
    function yy_r5(){
    if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + -1]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if ($this->options->preserveallnodes || !self::is_alt_nullable($this->yystack[$this->yyidx + -1]->minor)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($this->yystack[$this->yyidx + 0]->minor->position->add_chars_left(1));
            $this->yystack[$this->yyidx + -1]->minor->operands[] = $epsleaf;
        }
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info($this->yystack[$this->yyidx + 0]->minor->position->add_chars_left(1));
        $this->_retvalue = new qtype_preg_node_alt();
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + -1]->minor;
        $this->_retvalue->operands[] = $epsleaf;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1312 "../preg_parser.php"
#line 427 "../preg_parser.y"
    function yy_r6(){
    if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + 0]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if ($this->options->preserveallnodes || !self::is_alt_nullable($this->yystack[$this->yyidx + 0]->minor)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->add_chars_right(-1));
            $this->yystack[$this->yyidx + 0]->minor->operands = array_merge(array($epsleaf), $this->yystack[$this->yyidx + 0]->minor->operands);
        }
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->add_chars_right(-1));
        $this->_retvalue = new qtype_preg_node_alt();
        $this->_retvalue->operands[] = $epsleaf;
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + 0]->minor;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + -1]->minor->userinscription);
    }
#line 1333 "../preg_parser.php"
#line 447 "../preg_parser.y"
    function yy_r7(){
    $this->_retvalue = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1339 "../preg_parser.php"
#line 452 "../preg_parser.y"
    function yy_r8(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1346 "../preg_parser.php"
#line 458 "../preg_parser.y"
    function yy_r9(){
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info($this->yystack[$this->yyidx + 0]->minor->position->add_chars_right(-1), array_merge($this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription));
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -1]->minor, $emptynode, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1353 "../preg_parser.php"
#line 464 "../preg_parser.y"
    function yy_r10(){
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1358 "../preg_parser.php"
#line 468 "../preg_parser.y"
    function yy_r11(){
    if ($this->yystack[$this->yyidx + -4]->minor->is_condition_assertion()) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1367 "../preg_parser.php"
#line 476 "../preg_parser.y"
    function yy_r12(){
    if ($this->yystack[$this->yyidx + -3]->minor->is_condition_assertion()) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -2]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1376 "../preg_parser.php"
#line 484 "../preg_parser.y"
    function yy_r13(){
    if ($this->yystack[$this->yyidx + -3]->minor->is_condition_assertion()) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1385 "../preg_parser.php"
#line 492 "../preg_parser.y"
    function yy_r14(){
    if ($this->yystack[$this->yyidx + -2]->minor->is_condition_assertion()) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -2]->minor, null, null, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -2]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1394 "../preg_parser.php"
#line 504 "../preg_parser.y"
    function yy_r15(){
    $b = is_array($this->yystack[$this->yyidx + -2]->minor) ? $this->yystack[$this->yyidx + -2]->minor : array($this->yystack[$this->yyidx + -2]->minor);
    $d = is_array($this->yystack[$this->yyidx + 0]->minor) ? $this->yystack[$this->yyidx + 0]->minor : array($this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue = array_merge($b, $d);
    }
#line 1401 "../preg_parser.php"
#line 510 "../preg_parser.y"
    function yy_r16(){
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1406 "../preg_parser.php"
#line 523 "../preg_parser.y"
    function yy_r19(){
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info($this->yystack[$this->yyidx + 0]->minor->position->add_chars_right(-1), array_merge($this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription));
    $position = $this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position);
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->operands = array($emptynode);
    $this->_retvalue->position = $position;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists($this->_retvalue->name, $available)) {
        $template = $available[$this->_retvalue->name];
        if ($template->placeholderscount === 0) {
            // Template leaf called as a node
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, $this->_retvalue->name, $position, $this->_retvalue->userinscription, array());
        }
    }
    }
#line 1424 "../preg_parser.php"
#line 540 "../preg_parser.y"
    function yy_r20(){
    $position = $this->yystack[$this->yyidx + -2]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position);
    $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->operands = is_array($this->yystack[$this->yyidx + -1]->minor) ? $this->yystack[$this->yyidx + -1]->minor : array($this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue->position = $position;
    $available = qtype_preg\template::available_templates();
    if (array_key_exists($this->_retvalue->name, $available)) {
        $template = $available[$this->_retvalue->name];
        if ($template->placeholderscount !== count($this->_retvalue->operands)) {
            // Wrong number of parameters
            $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_TEMPLATE_PARAMS_COUNT, $this->_retvalue->name, $position, $this->_retvalue->userinscription, array());
        }
    }
    }
#line 1440 "../preg_parser.php"
#line 560 "../preg_parser.y"
    function yy_r21(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription, array($this->yystack[$this->yyidx + -1]->minor));
    }
#line 1445 "../preg_parser.php"
#line 564 "../preg_parser.y"
    function yy_r22(){
    $b = is_array($this->yystack[$this->yyidx + -1]->minor) ? $this->yystack[$this->yyidx + -1]->minor : array($this->yystack[$this->yyidx + -1]->minor);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription, $b);
    }
#line 1451 "../preg_parser.php"
#line 569 "../preg_parser.y"
    function yy_r23(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1456 "../preg_parser.php"
#line 573 "../preg_parser.y"
    function yy_r24(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1461 "../preg_parser.php"
#line 577 "../preg_parser.y"
    function yy_r25(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + -1]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + -1]->minor->position, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1466 "../preg_parser.php"
#line 581 "../preg_parser.y"
    function yy_r26(){
    $c = is_array($this->yystack[$this->yyidx + 0]->minor) ? $this->yystack[$this->yyidx + 0]->minor : array($this->yystack[$this->yyidx + 0]->minor);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN, $this->yystack[$this->yyidx + -1]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + -1]->minor->position, $this->yystack[$this->yyidx + -1]->minor->userinscription, $c);
    }
#line 1472 "../preg_parser.php"
#line 586 "../preg_parser.y"
    function yy_r27(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1477 "../preg_parser.php"
#line 590 "../preg_parser.y"
    function yy_r28(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_TEMPLATE_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1482 "../preg_parser.php"
#line 594 "../preg_parser.y"
    function yy_r29(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + -3]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + -3]->minor->position, $this->yystack[$this->yyidx + -3]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -2]->minor));
    }
#line 1487 "../preg_parser.php"
#line 606 "../preg_parser.y"
    function yy_r32(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1492 "../preg_parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //qtype_preg_yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new qtype_preg_yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
#line 305 "../preg_parser.y"

    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, new qtype_preg_position(), array());
    }
#line 1599 "../preg_parser.php"
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     * @param mixed ...           any extra arguments that should be passed to handlers
     *
     * @return void
     */
    function doParse($yymajor, $yytokenvalue)
    {
//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new qtype_preg_yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(
                self::$yyTraceFILE,
                "%sInput %s\n",
                self::$yyTracePrompt,
                self::$yyTokenName[$yymajor]
            );
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL
                && !$this->yy_is_expected_token($yymajor)
            ) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(
                        self::$yyTraceFILE,
                        "%sSyntax Error!\n",
                        self::$yyTracePrompt
                    );
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ) {
                        if (self::$yyTraceFILE) {
                            fprintf(
                                self::$yyTraceFILE,
                                "%sDiscard input token %s\n",
                                self::$yyTracePrompt,
                                self::$yyTokenName[$yymajor]
                            );
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0
                            && $yymx != self::YYERRORSYMBOL
                            && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);
    }
}
