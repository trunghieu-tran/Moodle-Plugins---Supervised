<?php
/* Driver template for the LEMON parser generator.
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
        return $this->_string;
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
            $this->string .= $value->string;
            $this->metadata[$offset] = $value->metadata;
        } else {
            $this->metadata[$offset] = $value;
        }
    }

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

// code external to the class is included here
#line 2 "../preg_parser.y"

    global $CFG;
    require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
    require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
#line 83 "../preg_parser.php"

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

// any extra class_declaration (extends/implements) are defined here
/**
 * The state of the parser is completely contained in an instance of
 * the following structure
 */
class qtype_preg_yyParser
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 8 "../preg_parser.y"

    // Root of the Abstract Syntax Tree (AST).
    private $root;
    // Objects of qtype_preg_node_error for errors found during the parsing.
    private $errornodes;
    // Handling options.
    private $handlingoptions;
    // Counter of nodes id. After parsing, equals the number of nodes in the tree.
    private $id_counter;
    // Counter of subpatterns.
    private $subpatt_counter;
    // Followpos map.
    private $followpos;
    // Max difference (right - left).
    private $max_finite_quant_borders_difference;   // В принципе это костыль; когда ДКА будет получаться из НКА - удалить это.

    public function __construct($handlingoptions = null) {
        $this->root = null;
        $this->errornodes = array();
        if ($handlingoptions == null) {
            $handlingoptions = new qtype_preg_handling_options();
        }
        $this->handlingoptions = $handlingoptions;
        $this->id_counter = 0;
        $this->subpatt_counter = 0;
        $this->followpos = array();
        $this->max_finite_quant_borders_difference = 0;
    }

    public function get_root() {
        return $this->root;
    }

    public function get_error() {
        return (count($this->errornodes) > 0);
    }

    public function get_error_nodes() {
        return $this->errornodes;
    }

    public function get_max_subpatt() {
        return $this->subpatt_counter;
    }

    public function get_followpos() {
        return $this->followpos;
    }

    public function get_max_finite_quant_borders_difference() {
        return $this->max_finite_quant_borders_difference;
    }

    /**
     * Creates and returns an error node, also adds it to the array of parser errors
     * @param subtype type of error
     * @param indfirst the starting index of the highlited area
     * @param indlast the ending index of the highlited area
     * @param addinfo additional info, supplied for this error
     * @return qtype_preg_node_error object
     */
    protected function create_error_node($subtype, $addinfo, $indfirst, $indlast, $userinscription, $operands = array()) {
        $newnode = new qtype_preg_node_error($subtype, $addinfo);
        $newnode->set_user_info($indfirst, $indlast, $userinscription);
        $newnode->operands = $operands;
        $this->errornodes[] = $newnode;
        return $newnode;
    }

    /**
     * Creates error node(s) if there is an error in the given node.
     * @param node the node to be checked.
     */
    protected function create_error_node_from_lexer($node) {
        if (isset($node->type) && $node->type === qtype_preg_node::TYPE_NODE_ERROR) {
            $this->create_error_node($node->subtype, $node->addinfo, $node->indfirst, $node->indlast, $node->userinscription);
        }
        if (!isset($node->error)) {
            return;
        }
        if (is_array($node->error)) {
            foreach ($node->error as $error) {
                $this->create_error_node($error->subtype, $error->addinfo, $error->indfirst, $error->indlast, $error->userinscription);
            }
        } else if ($node->error !== null) {
            $this->create_error_node($node->error->subtype, $node->error->addinfo, $node->error->indfirst, $node->error->indlast, $node->error->userinscription);
        }
    }

    /**
      * Creates and return correct parenthesis node (subexpression, groping or assertion).
      *
      * Used to avoid code duplication between empty and non-empty parenthesis.
      * @param parens parenthesis token from lexer
      * @param exprnode the node for expression inside parenthesis
      */
    protected function create_parens_node($parens, $exprnode) {
        $result = null;
        if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING && !$this->handlingoptions->preserveallnodes) {
            $result = $exprnode;
        } else {
            if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_GROUPING) {
                $result = new qtype_preg_node_subexpr(-1);
            } else if ($parens->subtype === qtype_preg_node_subexpr::SUBTYPE_SUBEXPR || $parens->subtype === qtype_preg_node_subexpr::SUBTYPE_ONCEONLY) {
                $result = new qtype_preg_node_subexpr($parens->number);
            } else {
                $result = new qtype_preg_node_assert();
            }
            $result->subtype = $parens->subtype;
            $result->operands[0] = $exprnode;
            $result->userinscription = new qtype_preg_userinscription($parens->userinscription->data . '...)');
        }
        $result->set_user_info($parens->indfirst, $exprnode->indlast + 1, $result->userinscription);
        return $result;
    }

    protected function create_cond_subexpr_assertion_node($paren, $assertnode, $exprnode) {
        if ($assertnode === null) {
            $assertnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $assertnode->set_user_info($paren->indlast, $paren->indlast, new qtype_preg_userinscription());
        }
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($assertnode->indlast + 1, $assertnode->indlast + 1, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subexpr($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
            // Error: only one or two top-level alternative allowed in a conditional subexpression.
            if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT && count($exprnode->operands) > 2) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription(), array($exprnode, $assertnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subexpr($paren->subtype);
                $result->operands = $exprnode->operands;
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLA;
        } else if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB) {
            $subtype = qtype_preg_node_assert::SUBTYPE_PLB;
        } else if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA) {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLA;
        } else {
            $subtype = qtype_preg_node_assert::SUBTYPE_NLB;
        }
        $result->condbranch = new qtype_preg_node_assert($subtype);
        $result->condbranch->operands[0] = $assertnode;
        $result->condbranch->userinscription = new qtype_preg_userinscription(qtype_poasquestion_string::substr($paren->userinscription->data, 2) . '...)');
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...)...|...)'));
        return $result;
    }

    protected function create_cond_subexpr_other_node($paren, $exprnode) {
        if ($exprnode === null) {
            $exprnode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $exprnode->set_user_info($paren->indlast + 2, $paren->indlast + 2, new qtype_preg_userinscription());
        }
        if ($exprnode->type != qtype_preg_node::TYPE_NODE_ALT) {
            $result = new qtype_preg_node_cond_subexpr($paren->subtype);
            $result->operands[0] = $exprnode;
        } else {
             // Error: only one or two top-level alternative allowed in a conditional subexpression.
            if ($exprnode->type == qtype_preg_node::TYPE_NODE_ALT && count($exprnode->operands) > 2) {
                $result = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription(), array($exprnode));
                return $result;
            } else {
                $result = new qtype_preg_node_cond_subexpr($paren->subtype);
                $result->operands[0] = $exprnode->operands[0];
                $result->operands[1] = $exprnode->operands[1];
            }
        }
        if ($paren->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR) {
            $result->number = $paren->number;
        }
        $result->set_user_info($paren->indfirst, $exprnode->indlast + 1, new qtype_preg_userinscription($paren->userinscription->data . '...|...)'));
        return $result;
    }

    protected function assign_subpatts($node) {
        if ($node->is_subpattern() || $node === $this->root) {
            $node->subpattern = $this->subpatt_counter++;
        }
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $this->assign_subpatts($node->condbranch);
            }
            foreach ($node->operands as $operand) {
                $this->assign_subpatts($operand);
            }
        }
    }

    protected function assign_ids($node) {
        $node->id = ++$this->id_counter;
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $this->assign_ids($node->condbranch);
            }
            foreach ($node->operands as $operand) {
                $this->assign_ids($operand);
            }
        }
    }

    protected function expand_quantifiers($node) {
        if (is_a($node, 'qtype_preg_operator')) {
            if ($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->condbranch !== null) {
                $node->condbranch = $this->expand_quantifiers($node->condbranch);
            }
            foreach ($node->operands as $key => $operand) {
                $node->operands[$key] = $this->expand_quantifiers($operand);
            }
        }
        if ($node->type == qtype_preg_node::TYPE_NODE_FINITE_QUANT) {
            $this->max_finite_quant_borders_difference = max($this->max_finite_quant_borders_difference, $node->rightborder - $node->leftborder);
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

    protected static function is_alt_nullable($altnode) {
        foreach ($altnode->operands as $operand) {
            if ($operand->type == qtype_preg_node::TYPE_LEAF_META && $operand->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
                return true;
            }
        }
        return false;
    }
#line 377 "../preg_parser.php"

/* Next is all token values, in a form suitable for use by makeheaders.
** This section will be null unless lemon is run with the -m switch.
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
    const ALT_SHORTEST                   =  5;
    const ALT_SHORT                      =  6;
    const ALT                            =  7;
    const CONC                           =  8;
    const PARSLEAF                       =  9;
    const QUANT                          = 10;
    const OPENBRACK                      = 11;
    const CONDSUBEXPR                    = 12;
    const YY_NO_ACTION = 52;
    const YY_ACCEPT_ACTION = 51;
    const YY_ERROR_ACTION = 50;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < YYNSTATE                  Shift N.  That is, push the lookahead
**                                      token onto the stack and goto state N.
**
**   YYNSTATE <= N < YYNSTATE+YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == YYNSTATE+YYNRULE              A syntax error has occurred.
**
**   N == YYNSTATE+YYNRULE+1            The parser accepts its input.
**
**   N == YYNSTATE+YYNRULE+2            No such action.  Denotes unused
**                                      slots in the yy_action[] table.
**
** The action table is constructed as a single large table named yy_action[].
** Given state S and lookahead X, the action is computed as
**
**      yy_action[ yy_shift_ofst[S] + X ]
**
** If the index value yy_shift_ofst[S]+X is out of range or if the value
** yy_lookahead[yy_shift_ofst[S]+X] is not equal to X or if yy_shift_ofst[S]
** is equal to YY_SHIFT_USE_DFLT, it means that the action is not in the table
** and that yy_default[S] should be used instead.
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the yy_reduce_ofst[] array is used in place of
** the yy_shift_ofst[] array and YY_REDUCE_USE_DFLT is used in place of
** YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  yy_action[]        A single table containing all actions.
**  yy_lookahead[]     A table containing the lookahead for each entry in
**                     yy_action.  Used to detect hash collisions.
**  yy_shift_ofst[]    For each state, the offset into yy_action for
**                     shifting terminals.
**  yy_reduce_ofst[]   For each state, the offset into yy_action for
**                     shifting non-terminals after a reduce.
**  yy_default[]       Default action for each state.
*/
    const YY_SZ_ACTTAB = 125;
static public $yy_action = array(
 /*     0 */    24,   14,   13,   11,    1,   21,   23,    8,    2,   12,
 /*    10 */    18,    6,   42,   10,    5,   21,   26,    8,    2,    7,
 /*    20 */    16,   42,   42,   11,   42,   21,   23,    8,    2,   42,
 /*    30 */    19,   42,   42,   10,   42,   21,   26,    8,    2,   42,
 /*    40 */    15,   42,   42,   11,   42,   21,   23,    8,    2,   42,
 /*    50 */    22,   42,   42,   11,   42,   21,   23,    8,    2,   42,
 /*    60 */    17,   42,   42,   10,   42,   21,   26,    8,    2,   42,
 /*    70 */    20,   42,   42,   10,   42,   21,   26,    8,    2,   42,
 /*    80 */     3,   42,   42,   11,   42,   21,   23,    8,    2,   42,
 /*    90 */     4,   42,   42,   10,   42,   21,   26,    8,    2,   42,
 /*   100 */    42,   10,   42,   21,   26,    8,    2,   42,   42,   11,
 /*   110 */    42,   21,   23,    8,    2,   21,   23,    8,    2,   51,
 /*   120 */    25,    9,   23,    8,    2,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    10 */     4,   16,   17,    7,   16,    9,   10,   11,   12,   16,
 /*    20 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    30 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    40 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    50 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    60 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    70 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    80 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*    90 */     4,   17,   17,    7,   17,    9,   10,   11,   12,   17,
 /*   100 */    17,    7,   17,    9,   10,   11,   12,   17,   17,    7,
 /*   110 */    17,    9,   10,   11,   12,    9,   10,   11,   12,   14,
 /*   120 */    15,   16,   10,   11,   12,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 14;
    static public $yy_shift_ofst = array(
 /*     0 */    66,   76,   86,   56,    6,   16,   36,   46,   26,   -4,
 /*    10 */    94,   94,  102,  106,  112,
);
    const YY_REDUCE_USE_DFLT = -16;
    const YY_REDUCE_MAX = 14;
    static public $yy_reduce_ofst = array(
 /*     0 */   105,  -15,  -12,    3,   -2,  -15,  -15,  -15,   -5,  -15,
 /*    10 */    -7,  -14,  -15,  -15,  -15,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(4, 7, 9, 10, 11, 12, ),
        /* 1 */ array(4, 7, 9, 10, 11, 12, ),
        /* 2 */ array(4, 7, 9, 10, 11, 12, ),
        /* 3 */ array(4, 7, 9, 10, 11, 12, ),
        /* 4 */ array(4, 7, 9, 10, 11, 12, ),
        /* 5 */ array(4, 7, 9, 10, 11, 12, ),
        /* 6 */ array(4, 7, 9, 10, 11, 12, ),
        /* 7 */ array(4, 7, 9, 10, 11, 12, ),
        /* 8 */ array(4, 7, 9, 10, 11, 12, ),
        /* 9 */ array(4, 7, 9, 10, 11, 12, ),
        /* 10 */ array(7, 9, 10, 11, 12, ),
        /* 11 */ array(7, 9, 10, 11, 12, ),
        /* 12 */ array(7, 9, 10, 11, 12, ),
        /* 13 */ array(9, 10, 11, 12, ),
        /* 14 */ array(10, 11, 12, ),
        /* 15 */ array(),
        /* 16 */ array(),
        /* 17 */ array(),
        /* 18 */ array(),
        /* 19 */ array(),
        /* 20 */ array(),
        /* 21 */ array(),
        /* 22 */ array(),
        /* 23 */ array(),
        /* 24 */ array(),
        /* 25 */ array(),
        /* 26 */ array(),
);
    static public $yy_default = array(
 /*     0 */    50,   47,   48,   42,   43,   50,   44,   46,   45,   41,
 /*    10 */    32,   30,   31,   29,   28,   35,   38,   37,   39,   34,
 /*    20 */    43,   40,   36,   33,   42,   27,   49,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    YYCODETYPE         is the data type used for storing terminal
**                       and nonterminal numbers.  "unsigned char" is
**                       used if there are fewer than 250 terminals
**                       and nonterminals.  "int" is used otherwise.
**    YYNOCODE           is a number of type YYCODETYPE which corresponds
**                       to no legal terminal or nonterminal number.  This
**                       number is used to fill in empty slots of the hash
**                       table.
**    YYFALLBACK         If defined, this indicates that one or more tokens
**                       have fall-back values which should be used if the
**                       original value of the token will not parse.
**    YYACTIONTYPE       is the data type used for storing terminal
**                       and nonterminal numbers.  "unsigned char" is
**                       used if there are fewer than 250 rules and
**                       states combined.  "int" is used otherwise.
**    qtype_preg_TOKENTYPE     is the data type used for minor tokens given
**                       directly to the parser from the tokenizer.
**    YYMINORTYPE        is the data type used for all minor tokens.
**                       This is typically a union of many types, one of
**                       which is qtype_preg_TOKENTYPE.  The entry in the union
**                       for base tokens is called "yy0".
**    YYSTACKDEPTH       is the maximum depth of the parser's stack.
**    qtype_preg_ARG_DECL      A global declaration for the %extra_argument
**    YYNSTATE           the combined number of states.
**    YYNRULE            the number of rules in the grammar
**    YYERRORSYMBOL      is the code number of the error symbol.  If not
**                       defined, then do no error processing.
*/
    const YYNOCODE = 18;
    const YYSTACKDEPTH = 100;
    const qtype_preg_ARG_DECL = '0';
    const YYNSTATE = 27;
    const YYNRULE = 23;
    const YYERRORSYMBOL = 13;
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

    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    static public $yyTraceFILE;
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    //public $???????;      /* A place to hold %extra_argument - dynamically added */
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
  'CLOSEBRACK',    'ALT_SHORTEST',  'ALT_SHORT',     'ALT',         
  'CONC',          'PARSLEAF',      'QUANT',         'OPENBRACK',   
  'CONDSUBEXPR',   'error',         'start',         'lastexpr',    
  'expr',        
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= lastexpr",
 /*   1 */ "expr ::= expr expr",
 /*   2 */ "expr ::= expr ALT expr",
 /*   3 */ "expr ::= expr ALT",
 /*   4 */ "expr ::= ALT expr",
 /*   5 */ "expr ::= ALT",
 /*   6 */ "expr ::= expr QUANT",
 /*   7 */ "expr ::= OPENBRACK CLOSEBRACK",
 /*   8 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*   9 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr CLOSEBRACK",
 /*  10 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK CLOSEBRACK",
 /*  11 */ "expr ::= CONDSUBEXPR CLOSEBRACK expr CLOSEBRACK",
 /*  12 */ "expr ::= CONDSUBEXPR CLOSEBRACK CLOSEBRACK",
 /*  13 */ "expr ::= PARSLEAF",
 /*  14 */ "lastexpr ::= expr",
 /*  15 */ "expr ::= expr CLOSEBRACK",
 /*  16 */ "expr ::= CLOSEBRACK",
 /*  17 */ "expr ::= OPENBRACK expr",
 /*  18 */ "expr ::= OPENBRACK",
 /*  19 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr",
 /*  20 */ "expr ::= CONDSUBEXPR expr",
 /*  21 */ "expr ::= CONDSUBEXPR",
 /*  22 */ "expr ::= QUANT",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    function tokenName($tokenType)
    {
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /* The following function deletes the value associated with a
    ** symbol.  The symbol can be either a terminal or nonterminal.
    ** "yymajor" is the symbol code, and "yypminor" is a pointer to
    ** the value.
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

    function yy_is_expected_token($token)
    {
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
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
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
            for($i = 1; $i <= $this->yyidx; $i++) {
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
     * static const struct {
     *  YYCODETYPE lhs;         Symbol on the left-hand side of the rule
     *  unsigned char nrhs;     Number of right-hand side symbols in the rule
     * }
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 14, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 5 ),
  array( 'lhs' => 16, 'rhs' => 4 ),
  array( 'lhs' => 16, 'rhs' => 4 ),
  array( 'lhs' => 16, 'rhs' => 3 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 4 ),
  array( 'lhs' => 16, 'rhs' => 2 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
  array( 'lhs' => 16, 'rhs' => 1 ),
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
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5,
        6 => 6,
        7 => 7,
        8 => 8,
        9 => 9,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 16,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 20,
        21 => 21,
        22 => 22,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 282 "../preg_parser.y"
    function yy_r0(){
    // Set the root node.
    $this->root = $this->yystack[$this->yyidx + 0]->minor;

    // Assign subpattern numbers.
    $this->assign_subpatts($this->root);

    // Expand quantifiers if needed.
    if ($this->handlingoptions->expandquantifiers) {
        $this->root = $this->expand_quantifiers($this->root);
    }

    // Assign identifiers.
    $this->assign_ids($this->root);

    // Calculate nullable, firstpos, lastpos and followpos for all nodes.
    $this->root->calculate_nflf($this->followpos);
    }
#line 1091 "../preg_parser.php"
#line 301 "../preg_parser.y"
    function yy_r1(){
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
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription());
    }
#line 1110 "../preg_parser.php"
#line 319 "../preg_parser.y"
    function yy_r2(){
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
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -2]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription('|'));
    }
#line 1129 "../preg_parser.php"
#line 337 "../preg_parser.y"
    function yy_r3(){
    if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + -1]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable($this->yystack[$this->yyidx + -1]->minor)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, new qtype_preg_userinscription('|'));
            $this->yystack[$this->yyidx + -1]->minor->operands[] = $epsleaf;
        }
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info($this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, new qtype_preg_userinscription('|'));
        $this->_retvalue = new qtype_preg_node_alt();
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + -1]->minor;
        $this->_retvalue->operands[] = $epsleaf;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, new qtype_preg_userinscription('|'));
    }
#line 1150 "../preg_parser.php"
#line 357 "../preg_parser.y"
    function yy_r4(){
    if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + 0]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if (!self::is_alt_nullable($this->yystack[$this->yyidx + 0]->minor)) {
            $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
            $epsleaf->set_user_info($this->yystack[$this->yyidx + 0]->minor->indfirst - 1, $this->yystack[$this->yyidx + 0]->minor->indfirst - 1, new qtype_preg_userinscription('|'));
            $this->yystack[$this->yyidx + 0]->minor->operands = array_merge(array($epsleaf), $this->yystack[$this->yyidx + 0]->minor->operands);
        }
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else {
        $epsleaf = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
        $epsleaf->set_user_info($this->yystack[$this->yyidx + 0]->minor->indfirst - 1, $this->yystack[$this->yyidx + 0]->minor->indfirst - 1, new qtype_preg_userinscription('|'));
        $this->_retvalue = new qtype_preg_node_alt();
        $this->_retvalue->operands[] = $epsleaf;
        $this->_retvalue->operands[] = $this->yystack[$this->yyidx + 0]->minor;
    }
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + 0]->minor->indfirst - 1, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription('|'));
    }
#line 1171 "../preg_parser.php"
#line 377 "../preg_parser.y"
    function yy_r5(){
    $this->_retvalue = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, new qtype_preg_userinscription('|'));
    }
#line 1177 "../preg_parser.php"
#line 382 "../preg_parser.y"
    function yy_r6(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1185 "../preg_parser.php"
#line 389 "../preg_parser.y"
    function yy_r7(){
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info($this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, new qtype_preg_userinscription($this->yystack[$this->yyidx + -1]->minor->userinscription->data . ')'));
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -1]->minor, $emptynode);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1193 "../preg_parser.php"
#line 396 "../preg_parser.y"
    function yy_r8(){
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -2]->minor);
    }
#line 1199 "../preg_parser.php"
#line 401 "../preg_parser.y"
    function yy_r9(){
    if ($this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
    }
#line 1209 "../preg_parser.php"
#line 410 "../preg_parser.y"
    function yy_r10(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -2]->minor, null);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, null);
    }
    }
#line 1219 "../preg_parser.php"
#line 419 "../preg_parser.y"
    function yy_r11(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + -1]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor);
    }
    }
#line 1229 "../preg_parser.php"
#line 428 "../preg_parser.y"
    function yy_r12(){
    if ($this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -2]->minor, null, null);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -2]->minor, null);
    }
    }
#line 1239 "../preg_parser.php"
#line 437 "../preg_parser.y"
    function yy_r13(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1245 "../preg_parser.php"
#line 442 "../preg_parser.y"
    function yy_r14(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1250 "../preg_parser.php"
#line 452 "../preg_parser.y"
    function yy_r15(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + -1]->minor->indlast + 1, $this->yystack[$this->yyidx + 0]->minor->userinscription, array($this->yystack[$this->yyidx + -1]->minor));
    }
#line 1255 "../preg_parser.php"
#line 456 "../preg_parser.y"
    function yy_r16(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1260 "../preg_parser.php"
#line 460 "../preg_parser.y"
    function yy_r17(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->userinscription->data, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + -1]->minor);
    }
#line 1266 "../preg_parser.php"
#line 465 "../preg_parser.y"
    function yy_r18(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1272 "../preg_parser.php"
#line 470 "../preg_parser.y"
    function yy_r19(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -3]->minor->userinscription->data, $this->yystack[$this->yyidx + -3]->minor->indfirst, $this->yystack[$this->yyidx + -3]->minor->indlast, $this->yystack[$this->yyidx + -3]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -2]->minor));
    }
#line 1277 "../preg_parser.php"
#line 474 "../preg_parser.y"
    function yy_r20(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + -1]->minor->userinscription->data, $this->yystack[$this->yyidx + -1]->minor->indfirst, $this->yystack[$this->yyidx + -1]->minor->indlast, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1282 "../preg_parser.php"
#line 478 "../preg_parser.y"
    function yy_r21(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_WRONG_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->indfirst, $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1287 "../preg_parser.php"
#line 482 "../preg_parser.y"
    function yy_r22(){
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, $this->yystack[$this->yyidx + 0]->minor->userinscription->data, $this->yystack[$this->yyidx + 0]->minor->indfirst,  $this->yystack[$this->yyidx + 0]->minor->indlast, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->create_error_node_from_lexer($this->yystack[$this->yyidx + 0]->minor);
    }
#line 1293 "../preg_parser.php"

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
        for($i = $yysize; $i; $i--) {
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
#line 266 "../preg_parser.y"

    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, -1, -1, new qtype_preg_userinscription());
    }
#line 1398 "../preg_parser.php"
    }

    /**
     * The following code executes when a syntax error first occurs.
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
    }

    /*
    ** The following is executed when the parser accepts
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
     *  The main parser program.
     * The first argument is a pointer to a structure obtained from
     * "qtype_preg_Alloc" which describes the current state of the parser.
     * The second argument is the major token number.  The third is
     * the minor token.  The fourth optional argument is whatever the
     * user wants (and specified in the grammar) and is available for
     * use by the action routines.
     *
     * Inputs:
     *
     * - A pointer to the parser (an opaque structure.)
     * - The major token number.
     * - The minor token number (token value).
     * - An option argument of a grammar-specified type.
     *
     * Outputs:
     * None.
     * @param int the token number
     * @param mixed the token value
     * @param mixed any extra arguments that should be passed to handlers
     */
    function doParse($yymajor, $yytokenvalue, $extraargument = null)
    {
        if (self::qtype_preg_ARG_DECL && $extraargument !== null) {
            $this->{self::qtype_preg_ARG_DECL} = $extraargument;
        }
//        YYMINORTYPE yyminorunion;
//        int yyact;            /* The parser action. */
//        int yyendofinput;     /* True if we are at the end of input */
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
            fprintf(self::$yyTraceFILE, "%sInput %s\n",
                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
        }

        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL &&
                  !$this->yy_is_expected_token($yymajor)) {
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
                    fprintf(self::$yyTraceFILE, "%sSyntax Error!\n",
                        self::$yyTracePrompt);
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
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ){
                        if (self::$yyTraceFILE) {
                            fprintf(self::$yyTraceFILE, "%sDiscard input token %s\n",
                                self::$yyTracePrompt, self::$yyTokenName[$yymajor]);
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0 &&
                                 $yymx != self::YYERRORSYMBOL &&
        ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                              ){
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
