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
                                   array(new qtype_preg_userinscription(textlib::substr($node->userinscription[0], 2) . '...)')));

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
            if (count($exprnode->operands) > 2) {
                // Error: only one or two top-level alternations allowed in a conditional subexpression.
                $node->errors[] = $this->create_error_node(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_TOO_MUCH_ALTER, null, $position, array(), array($exprnode));
            }
        } else {
            $node->operands[0] = $exprnode;
        }

        $node->set_user_info($position, array(new qtype_preg_userinscription($node->userinscription[0] . '...|...)')));
        return $node;
    }

    protected function assign_subpatts($node) {
        if ($node->is_subpattern() || $node === $this->root) {
            $node->subpattern = $this->subpatt_counter++;
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

    protected function expand_quantifiers($node) {
        if (is_a($node, 'qtype_preg_operator')) {
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
#line 340 "../preg_parser.php"

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
    const PARSELEAF                      =  9;
    const QUANT                          = 10;
    const OPENBRACK                      = 11;
    const CONDSUBEXPR                    = 12;
    const YY_NO_ACTION = 50;
    const YY_ACCEPT_ACTION = 49;
    const YY_ERROR_ACTION = 48;

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
    const YY_SZ_ACTTAB = 123;
static public $yy_action = array(
 /*     0 */    20,   49,    4,   12,   11,   16,   21,    9,    2,   14,
 /*    10 */    15,   40,   13,   12,    1,   16,   21,    9,    2,    7,
 /*    20 */    24,   40,    8,   10,    5,   16,   25,    9,    2,   40,
 /*    30 */    17,   40,   40,   10,   40,   16,   25,    9,    2,   40,
 /*    40 */    23,   40,   40,   12,   40,   16,   21,    9,    2,   40,
 /*    50 */    19,   40,   40,   10,   40,   16,   25,    9,    2,   40,
 /*    60 */    18,   40,   40,   10,   40,   16,   25,    9,    2,   40,
 /*    70 */     6,   40,   40,   10,   40,   16,   25,    9,    2,   40,
 /*    80 */    22,   40,   40,   12,   40,   16,   21,    9,    2,   40,
 /*    90 */     3,   40,   40,   12,   40,   16,   21,    9,    2,   40,
 /*   100 */    40,   10,   40,   16,   25,    9,    2,   40,   40,   12,
 /*   110 */    40,   16,   21,    9,    2,   16,   21,    9,    2,   40,
 /*   120 */    21,    9,    2,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,   14,   15,    7,   15,    9,   10,   11,   12,   15,
 /*    10 */     4,   16,   15,    7,   15,    9,   10,   11,   12,   15,
 /*    20 */     4,   16,   15,    7,   15,    9,   10,   11,   12,   16,
 /*    30 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    40 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    50 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    60 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    70 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    80 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*    90 */     4,   16,   16,    7,   16,    9,   10,   11,   12,   16,
 /*   100 */    16,    7,   16,    9,   10,   11,   12,   16,   16,    7,
 /*   110 */    16,    9,   10,   11,   12,    9,   10,   11,   12,   16,
 /*   120 */    10,   11,   12,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 14;
    static public $yy_shift_ofst = array(
 /*     0 */    16,   86,   66,   46,   -4,    6,   26,   36,   76,   56,
 /*    10 */    94,  102,   94,  106,  110,
);
    const YY_REDUCE_USE_DFLT = -14;
    const YY_REDUCE_MAX = 14;
    static public $yy_reduce_ofst = array(
 /*     0 */   -13,   -6,   -1,    7,   -6,   -6,    9,   -6,   -6,    4,
 /*    10 */   -11,   -6,   -3,   -6,   -6,
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
);
    static public $yy_default = array(
 /*     0 */    48,   45,   46,   40,   26,   48,   41,   42,   44,   43,
 /*    10 */    32,   31,   30,   29,   28,   38,   27,   39,   34,   37,
 /*    20 */    40,   33,   36,   35,   41,   47,
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
    const YYNOCODE = 17;
    const YYSTACKDEPTH = 100;
    const qtype_preg_ARG_DECL = '0';
    const YYNSTATE = 26;
    const YYNRULE = 22;
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
  'CONC',          'PARSELEAF',     'QUANT',         'OPENBRACK',   
  'CONDSUBEXPR',   'error',         'start',         'expr',        
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= expr",
 /*   1 */ "expr ::= PARSELEAF",
 /*   2 */ "expr ::= expr expr",
 /*   3 */ "expr ::= expr ALT expr",
 /*   4 */ "expr ::= expr ALT",
 /*   5 */ "expr ::= ALT expr",
 /*   6 */ "expr ::= ALT",
 /*   7 */ "expr ::= expr QUANT",
 /*   8 */ "expr ::= OPENBRACK CLOSEBRACK",
 /*   9 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*  10 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr CLOSEBRACK",
 /*  11 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK CLOSEBRACK",
 /*  12 */ "expr ::= CONDSUBEXPR CLOSEBRACK expr CLOSEBRACK",
 /*  13 */ "expr ::= CONDSUBEXPR CLOSEBRACK CLOSEBRACK",
 /*  14 */ "expr ::= expr CLOSEBRACK",
 /*  15 */ "expr ::= CLOSEBRACK",
 /*  16 */ "expr ::= OPENBRACK expr",
 /*  17 */ "expr ::= OPENBRACK",
 /*  18 */ "expr ::= CONDSUBEXPR expr CLOSEBRACK expr",
 /*  19 */ "expr ::= CONDSUBEXPR expr",
 /*  20 */ "expr ::= CONDSUBEXPR",
 /*  21 */ "expr ::= QUANT",
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
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 15, 'rhs' => 5 ),
  array( 'lhs' => 15, 'rhs' => 4 ),
  array( 'lhs' => 15, 'rhs' => 4 ),
  array( 'lhs' => 15, 'rhs' => 3 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 4 ),
  array( 'lhs' => 15, 'rhs' => 2 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
  array( 'lhs' => 15, 'rhs' => 1 ),
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
        19 => 16,
        17 => 17,
        20 => 17,
        18 => 18,
        21 => 21,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 246 "../preg_parser.y"
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
#line 1049 "../preg_parser.php"
#line 265 "../preg_parser.y"
    function yy_r1(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1054 "../preg_parser.php"
#line 269 "../preg_parser.y"
    function yy_r2(){
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
#line 1073 "../preg_parser.php"
#line 287 "../preg_parser.y"
    function yy_r3(){
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
#line 1092 "../preg_parser.php"
#line 305 "../preg_parser.y"
    function yy_r4(){
    if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + -1]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    } else if ($this->yystack[$this->yyidx + -1]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if ($this->handlingoptions->preserveallnodes || !self::is_alt_nullable($this->yystack[$this->yyidx + -1]->minor)) {
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
#line 1113 "../preg_parser.php"
#line 325 "../preg_parser.y"
    function yy_r5(){
    if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_LEAF_META && $this->yystack[$this->yyidx + 0]->minor->subtype == qtype_preg_leaf_meta::SUBTYPE_EMPTY) {
        $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    } else if ($this->yystack[$this->yyidx + 0]->minor->type == qtype_preg_node::TYPE_NODE_ALT) {
        if ($this->handlingoptions->preserveallnodes || !self::is_alt_nullable($this->yystack[$this->yyidx + 0]->minor)) {
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
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1134 "../preg_parser.php"
#line 345 "../preg_parser.y"
    function yy_r6(){
    $this->_retvalue = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + 0]->minor->position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1140 "../preg_parser.php"
#line 350 "../preg_parser.y"
    function yy_r7(){
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->_retvalue->set_user_info($this->yystack[$this->yyidx + -1]->minor->position->compose($this->yystack[$this->yyidx + 0]->minor->position), $this->yystack[$this->yyidx + 0]->minor->userinscription);
    $this->_retvalue->operands[0] = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1147 "../preg_parser.php"
#line 356 "../preg_parser.y"
    function yy_r8(){
    $emptynode = new qtype_preg_leaf_meta(qtype_preg_leaf_meta::SUBTYPE_EMPTY);
    $emptynode->set_user_info($this->yystack[$this->yyidx + 0]->minor->position->add_chars_right(-1), array_merge($this->yystack[$this->yyidx + -1]->minor->userinscription, $this->yystack[$this->yyidx + 0]->minor->userinscription));
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -1]->minor, $emptynode, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1154 "../preg_parser.php"
#line 362 "../preg_parser.y"
    function yy_r9(){
    $this->_retvalue = $this->create_parens_node($this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
#line 1159 "../preg_parser.php"
#line 366 "../preg_parser.y"
    function yy_r10(){
    if ($this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -4]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -4]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1169 "../preg_parser.php"
#line 375 "../preg_parser.y"
    function yy_r11(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -2]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1179 "../preg_parser.php"
#line 384 "../preg_parser.y"
    function yy_r12(){
    if ($this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -3]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -3]->minor, null, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -3]->minor, $this->yystack[$this->yyidx + -1]->minor, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1189 "../preg_parser.php"
#line 393 "../preg_parser.y"
    function yy_r13(){
    if ($this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLA || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLA ||
        $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_PLB || $this->yystack[$this->yyidx + -2]->minor->subtype === qtype_preg_node_cond_subexpr::SUBTYPE_NLB) {
        $this->_retvalue = $this->create_cond_subexpr_assertion_node($this->yystack[$this->yyidx + -2]->minor, null, null, $this->yystack[$this->yyidx + 0]->minor);
    } else {
        $this->_retvalue = $this->create_cond_subexpr_other_node($this->yystack[$this->yyidx + -2]->minor, null, $this->yystack[$this->yyidx + 0]->minor);
    }
    }
#line 1199 "../preg_parser.php"
#line 407 "../preg_parser.y"
    function yy_r14(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + 0]->minor->position->indfirst, $this->yystack[$this->yyidx + 0]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->linefirst, $this->yystack[$this->yyidx + 0]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->colfirst, $this->yystack[$this->yyidx + 0]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + 0]->minor->userinscription, array($this->yystack[$this->yyidx + -1]->minor));
    }
#line 1207 "../preg_parser.php"
#line 414 "../preg_parser.y"
    function yy_r15(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + 0]->minor->position->indfirst, $this->yystack[$this->yyidx + 0]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->linefirst, $this->yystack[$this->yyidx + 0]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->colfirst, $this->yystack[$this->yyidx + 0]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_OPEN_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1215 "../preg_parser.php"
#line 421 "../preg_parser.y"
    function yy_r16(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + -1]->minor->position->indfirst, $this->yystack[$this->yyidx + -1]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + -1]->minor->position->linefirst, $this->yystack[$this->yyidx + -1]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + -1]->minor->position->colfirst, $this->yystack[$this->yyidx + -1]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + -1]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + -1]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor));
    }
#line 1223 "../preg_parser.php"
#line 428 "../preg_parser.y"
    function yy_r17(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + 0]->minor->position->indfirst, $this->yystack[$this->yyidx + 0]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->linefirst, $this->yystack[$this->yyidx + 0]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->colfirst, $this->yystack[$this->yyidx + 0]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1231 "../preg_parser.php"
#line 435 "../preg_parser.y"
    function yy_r18(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + -3]->minor->position->indfirst, $this->yystack[$this->yyidx + -3]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + -3]->minor->position->linefirst, $this->yystack[$this->yyidx + -3]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + -3]->minor->position->colfirst, $this->yystack[$this->yyidx + -3]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_MISSING_CLOSE_PAREN, $this->yystack[$this->yyidx + -3]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + -3]->minor->userinscription, array($this->yystack[$this->yyidx + 0]->minor, $this->yystack[$this->yyidx + -2]->minor));
    }
#line 1239 "../preg_parser.php"
#line 456 "../preg_parser.y"
    function yy_r21(){
    $position = new qtype_preg_position($this->yystack[$this->yyidx + 0]->minor->position->indfirst, $this->yystack[$this->yyidx + 0]->minor->position->indlast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->linefirst, $this->yystack[$this->yyidx + 0]->minor->position->linelast,
                                        $this->yystack[$this->yyidx + 0]->minor->position->colfirst, $this->yystack[$this->yyidx + 0]->minor->position->collast);
    $this->_retvalue = $this->create_error_node(qtype_preg_node_error::SUBTYPE_QUANTIFIER_WITHOUT_PARAMETER, $this->yystack[$this->yyidx + 0]->minor->userinscription[0]->data, $position, $this->yystack[$this->yyidx + 0]->minor->userinscription);
    }
#line 1247 "../preg_parser.php"

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
#line 230 "../preg_parser.y"

    if (count($this->errornodes) === 0) {
        $this->create_error_node(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, null, new qtype_preg_position(), array());
    }
#line 1352 "../preg_parser.php"
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
