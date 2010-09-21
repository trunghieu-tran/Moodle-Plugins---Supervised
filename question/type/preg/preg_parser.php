<?php
/* Driver template for the LEMON parser generator.
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class preg_parser_yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof preg_parser_yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof preg_parser_yyToken) {
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
                $x = ($value instanceof preg_parser_yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof preg_parser_yyToken) {
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

    require_once($CFG->dirroot . '/question/type/preg/node.php');
#line 80 "../preg_parser.php"

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
class preg_parser_yyStackEntry
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
class preg_parser_yyParser
{
/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 5 "../preg_parser.y"

    //Root of the Abstract Syntax Tree (AST)
    private $root;
    //Is a pattern fully anchored?
    private $anchor;
    //Are there any errors during the parsing
    private $error;
    //Error messages for errors during the parsing
    private $errormessages;
    //Count of reduces made
    private $reducecount;
    //Assert characters
    private $parens;

    function __construct() {
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
        $this->error = false;
        $this->errormessages = array();
        $this->reducecount = 0;
        $this->parens = array(NODE_SUBPATT => '(', NODE => '(?:', NODE_ONETIMESUBPATT => '(?>', 
                                NODE_ASSERTTF => '(?=', NODE_ASSERTTB => '(?<=',NODE_ASSERTFF => '(?!', NODE_ASSERTFB => '(?<!');
    }

    function get_root() {
        return $this->root;
    }

    function get_anchor() {
        return $this->anchor;
    }

    function get_error() {
        return $this->error;
    }

    function get_error_messages() {
        return $this->errormessages;
    }

    /**
    *create and return an error node
    @param errorstr translation string name for the error
    @param a object, string or number to be used in translation string
    @return node
    */
    protected function create_error_node($errorstr, $a = null) {
        $newnode = new node;
        $newnode->type = ERROR;
        $newnode->subtype = $errorstr;
        $this->errormessages[] = get_string($errorstr,'qtype_preg',$a);
        $this->error = true;
    }

    static function is_conc($prevtoken, $currtoken) {
        /*static $condsubpatt = false;
        static $close = 0;
        if ($currtoken == preg_parser_yyParser::CONDSUBPATT) {
            $condsubpatt = true;
            $close = -1;
        }
        if ($condsubpatt && $currtoken == preg_parser_yyParser::CLOSEBRACK) {
            $close++;
        }
        if ($condsubpatt && ($currtoken == preg_parser_yyParser::OPENBRACK || $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FF  ||
            $currtoken == preg_parser_yyParser::ASSERT_FB || $currtoken == preg_parser_yyParser::ASSERT_TB || $currtoken == preg_parser_yyParser::GROUPING ||
            $currtoken == preg_parser_yyParser::ONETIMESUBPATT)) {
            $close--;
        }
        if ($close == 0) {
            $condsubpatt = false;
        }
        $flag1 = ($prevtoken == preg_parser_yyParser::PARSLEAF || $prevtoken == preg_parser_yyParser::CLOSEBRACK ||
                  $prevtoken == preg_parser_yyParser::QUEST || $prevtoken == preg_parser_yyParser::LAZY_QUEST ||
                  $prevtoken == preg_parser_yyParser::ITER || $prevtoken == preg_parser_yyParser::LAZY_ITER ||
                  $prevtoken == preg_parser_yyParser::PLUS || $prevtoken == preg_parser_yyParser::LAZY_PLUS ||
                  $prevtoken == preg_parser_yyParser::QUANT || $prevtoken == preg_parser_yyParser::LAZY_QUANT ||
                  $prevtoken == preg_parser_yyParser::WORDBREAK || $prevtoken == preg_parser_yyParser::WORDNOTBREAK);
        $flag2 = ($currtoken == preg_parser_yyParser::PARSLEAF || $currtoken == preg_parser_yyParser::OPENBRACK ||
                  $currtoken == preg_parser_yyParser::GROUPING || $currtoken == preg_parser_yyParser::CONDSUBPATT ||
                  $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FF ||
                  $currtoken == preg_parser_yyParser::ASSERT_TF || $currtoken == preg_parser_yyParser::ASSERT_FB||
                  $currtoken == preg_parser_yyParser::WORDBREAK || $currtoken == preg_parser_yyParser::WORDNOTBREAK ||
                  $currtoken == preg_parser_yyParser::ONETIMESUBPATT);
        $flag = ($flag1 && $flag2 && isset($prevtoken) && !$condsubpatt);
        return $flag;*/
        return false;
    }
#line 203 "../preg_parser.php"

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
    const ERROR_PREC_VERY_SHORT          =  1;
    const ERROR_PREC_SHORT               =  2;
    const ERROR_PREC                     =  3;
    const CLOSEBRACK                     =  4;
    const ALT                            =  5;
    const CONC                           =  6;
    const PARSLEAF                       =  7;
    const WORDBREAK                      =  8;
    const WORDNOTBREAK                   =  9;
    const STARTANCHOR                    = 10;
    const QUEST                          = 11;
    const PLUS                           = 12;
    const ITER                           = 13;
    const QUANT                          = 14;
    const LAZY_ITER                      = 15;
    const LAZY_QUEST                     = 16;
    const LAZY_PLUS                      = 17;
    const LAZY_QUANT                     = 18;
    const OPENBRACK                      = 19;
    const CONDSUBPATT                    = 20;
    const ENDANCHOR                      = 21;
    const LEXERROR                       = 22;
    const YY_NO_ACTION = 80;
    const YY_ACCEPT_ACTION = 79;
    const YY_ERROR_ACTION = 78;

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
    const YY_SZ_ACTTAB = 135;
static public $yy_action = array(
 /*     0 */    28,   11,    9,   40,   17,   16,   10,   31,   30,   29,
 /*    10 */    38,   35,   34,   39,   37,    7,    6,   27,   11,    9,
 /*    20 */    40,   17,   16,   10,   31,   30,   29,   38,   35,   34,
 /*    30 */    39,   37,    7,    6,    5,   11,    9,   40,   17,   16,
 /*    40 */    10,   31,   30,   29,   38,   35,   34,   39,   37,    7,
 /*    50 */     6,   36,   11,    9,   40,   17,   16,   10,   31,   30,
 /*    60 */    29,   38,   35,   34,   39,   37,    7,    6,   24,   33,
 /*    70 */    32,   40,   17,   16,   10,   25,   18,   26,   23,   22,
 /*    80 */    19,   20,   21,    7,    6,    9,   40,   17,   16,   10,
 /*    90 */    31,   30,   29,   38,   35,   34,   39,   37,    7,    6,
 /*   100 */    40,   17,   16,   10,   25,   18,   26,   23,   22,   19,
 /*   110 */    20,   21,    7,    6,   31,   30,   29,   38,   35,   34,
 /*   120 */    39,   37,    7,    6,   79,   15,    3,   13,   12,   66,
 /*   130 */     8,   14,    4,    2,    1,
    );
    static public $yy_lookahead = array(
 /*     0 */     4,    5,    6,    7,    8,    9,   10,   11,   12,   13,
 /*    10 */    14,   15,   16,   17,   18,   19,   20,    4,    5,    6,
 /*    20 */     7,    8,    9,   10,   11,   12,   13,   14,   15,   16,
 /*    30 */    17,   18,   19,   20,    4,    5,    6,    7,    8,    9,
 /*    40 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*    50 */    20,    4,    5,    6,    7,    8,    9,   10,   11,   12,
 /*    60 */    13,   14,   15,   16,   17,   18,   19,   20,    4,   21,
 /*    70 */    22,    7,    8,    9,   10,   11,   12,   13,   14,   15,
 /*    80 */    16,   17,   18,   19,   20,    6,    7,    8,    9,   10,
 /*    90 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*   100 */     7,    8,    9,   10,   11,   12,   13,   14,   15,   16,
 /*   110 */    17,   18,   19,   20,   11,   12,   13,   14,   15,   16,
 /*   120 */    17,   18,   19,   20,   24,   25,   26,   26,   26,   27,
 /*   130 */    26,   26,   26,   26,   26,
);
    const YY_SHIFT_USE_DFLT = -5;
    const YY_SHIFT_MAX = 15;
    static public $yy_shift_ofst = array(
 /*     0 */    64,   47,   30,   13,   -4,   64,   64,   64,   79,   64,
 /*    10 */    64,   93,  103,  103,  103,   48,
);
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 14;
    static public $yy_reduce_ofst = array(
 /*     0 */   100,  101,  101,  101,  101,  108,  107,  106,  101,  105,
 /*    10 */   102,  104,  101,  101,  101,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 1 */ array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 2 */ array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 3 */ array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 4 */ array(4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 5 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 6 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 7 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 8 */ array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 9 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 10 */ array(4, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 11 */ array(7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 12 */ array(11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 13 */ array(11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 14 */ array(11, 12, 13, 14, 15, 16, 17, 18, 19, 20, ),
        /* 15 */ array(21, 22, ),
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
        /* 37 */ array(),
        /* 38 */ array(),
        /* 39 */ array(),
        /* 40 */ array(),
);
    static public $yy_default = array(
 /*     0 */    78,   66,   67,   59,   64,   62,   68,   65,   44,   78,
 /*    10 */    78,   45,   57,   43,   42,   41,   61,   60,   70,   74,
 /*    20 */    75,   76,   73,   72,   63,   69,   71,   62,   54,   47,
 /*    30 */    48,   46,   77,   58,   49,   50,   55,   53,   52,   51,
 /*    40 */    56,
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
**    preg_parser_TOKENTYPE     is the data type used for minor tokens given 
**                       directly to the parser from the tokenizer.
**    YYMINORTYPE        is the data type used for all minor tokens.
**                       This is typically a union of many types, one of
**                       which is preg_parser_TOKENTYPE.  The entry in the union
**                       for base tokens is called "yy0".
**    YYSTACKDEPTH       is the maximum depth of the parser's stack.
**    preg_parser_ARG_DECL      A global declaration for the %extra_argument
**    YYNSTATE           the combined number of states.
**    YYNRULE            the number of rules in the grammar
**    YYERRORSYMBOL      is the code number of the error symbol.  If not
**                       defined, then do no error processing.
*/
    const YYNOCODE = 28;
    const YYSTACKDEPTH = 100;
    const preg_parser_ARG_DECL = '0';
    const YYNSTATE = 41;
    const YYNRULE = 37;
    const YYERRORSYMBOL = 23;
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
  '$',             'ERROR_PREC_VERY_SHORT',  'ERROR_PREC_SHORT',  'ERROR_PREC',  
  'CLOSEBRACK',    'ALT',           'CONC',          'PARSLEAF',    
  'WORDBREAK',     'WORDNOTBREAK',  'STARTANCHOR',   'QUEST',       
  'PLUS',          'ITER',          'QUANT',         'LAZY_ITER',   
  'LAZY_QUEST',    'LAZY_PLUS',     'LAZY_QUANT',    'OPENBRACK',   
  'CONDSUBPATT',   'ENDANCHOR',     'LEXERROR',      'error',       
  'start',         'lastexpr',      'expr',        
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::= lastexpr",
 /*   1 */ "expr ::= expr CONC expr",
 /*   2 */ "expr ::= expr expr",
 /*   3 */ "expr ::= expr ALT expr",
 /*   4 */ "expr ::= expr ALT",
 /*   5 */ "expr ::= expr QUEST",
 /*   6 */ "expr ::= expr ITER",
 /*   7 */ "expr ::= expr PLUS",
 /*   8 */ "expr ::= expr LAZY_QUEST",
 /*   9 */ "expr ::= expr LAZY_ITER",
 /*  10 */ "expr ::= expr LAZY_PLUS",
 /*  11 */ "expr ::= expr QUANT",
 /*  12 */ "expr ::= expr LAZY_QUANT",
 /*  13 */ "expr ::= OPENBRACK expr CLOSEBRACK",
 /*  14 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr CLOSEBRACK",
 /*  15 */ "expr ::= PARSLEAF",
 /*  16 */ "expr ::= STARTANCHOR expr",
 /*  17 */ "lastexpr ::= lastexpr ENDANCHOR",
 /*  18 */ "lastexpr ::= expr",
 /*  19 */ "expr ::= WORDBREAK",
 /*  20 */ "expr ::= WORDNOTBREAK",
 /*  21 */ "expr ::= expr CLOSEBRACK",
 /*  22 */ "expr ::= CLOSEBRACK",
 /*  23 */ "expr ::= OPENBRACK expr",
 /*  24 */ "expr ::= OPENBRACK",
 /*  25 */ "expr ::= CONDSUBPATT expr CLOSEBRACK expr",
 /*  26 */ "expr ::= CONDSUBPATT expr",
 /*  27 */ "expr ::= CONDSUBPATT",
 /*  28 */ "expr ::= QUEST",
 /*  29 */ "expr ::= PLUS",
 /*  30 */ "expr ::= ITER",
 /*  31 */ "expr ::= QUANT",
 /*  32 */ "expr ::= LAZY_ITER",
 /*  33 */ "expr ::= LAZY_QUEST",
 /*  34 */ "expr ::= LAZY_PLUS",
 /*  35 */ "expr ::= LAZY_QUANT",
 /*  36 */ "lastexpr ::= lastexpr LEXERROR",
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
     * @param preg_parser_yyParser
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
                        $x = new preg_parser_yyStackEntry;
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
                        $x = new preg_parser_yyStackEntry;
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
        $yytos = new preg_parser_yyStackEntry;
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
  array( 'lhs' => 24, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 5 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 25, 'rhs' => 2 ),
  array( 'lhs' => 25, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 4 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 25, 'rhs' => 2 ),
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
        23 => 23,
        24 => 24,
        25 => 25,
        26 => 26,
        27 => 27,
        28 => 28,
        29 => 29,
        30 => 30,
        31 => 31,
        32 => 32,
        33 => 33,
        34 => 34,
        35 => 35,
        36 => 36,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 108 "../preg_parser.y"
    function yy_r0(){
    $this->root = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 974 "../preg_parser.php"
#line 111 "../preg_parser.y"
    function yy_r1(){
    ECHO 'CONC <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONC;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 985 "../preg_parser.php"
#line 120 "../preg_parser.y"
    function yy_r2(){
    ECHO 'CONC1 <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONC;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 996 "../preg_parser.php"
#line 129 "../preg_parser.y"
    function yy_r3(){
    ECHO 'ALT <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ALT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 1007 "../preg_parser.php"
#line 138 "../preg_parser.y"
    function yy_r4(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ALT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->secop = new node;
    $this->_retvalue->secop->type = LEAF;
    $this->_retvalue->secop->subtype = LEAF_EMPTY;
    $this->reducecount++;
    }
#line 1019 "../preg_parser.php"
#line 148 "../preg_parser.y"
    function yy_r5(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUESTQUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1029 "../preg_parser.php"
#line 156 "../preg_parser.y"
    function yy_r6(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ITER;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1039 "../preg_parser.php"
#line 164 "../preg_parser.y"
    function yy_r7(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_PLUSQUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1049 "../preg_parser.php"
#line 172 "../preg_parser.y"
    function yy_r8(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUESTQUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1059 "../preg_parser.php"
#line 180 "../preg_parser.y"
    function yy_r9(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ITER;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1069 "../preg_parser.php"
#line 188 "../preg_parser.y"
    function yy_r10(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_PLUSQUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1079 "../preg_parser.php"
#line 196 "../preg_parser.y"
    function yy_r11(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->leftborder = $this->yystack[$this->yyidx + 0]->minor->leftborder;
    $this->_retvalue->rightborder = $this->yystack[$this->yyidx + 0]->minor->rightborder;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1091 "../preg_parser.php"
#line 206 "../preg_parser.y"
    function yy_r12(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->leftborder = $this->yystack[$this->yyidx + 0]->minor->leftborder;
    $this->_retvalue->rightborder = $this->yystack[$this->yyidx + 0]->minor->rightborder;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1103 "../preg_parser.php"
#line 216 "../preg_parser.y"
    function yy_r13(){
    ECHO 'SUBPATT '.$this->parens[$this->yystack[$this->yyidx + -2]->minor].'<br/>';
    if ($this->yystack[$this->yyidx + -2]->minor != NODE) {
        $this->_retvalue = new node;
        $this->_retvalue->type = NODE;
        $this->_retvalue->subtype = $this->yystack[$this->yyidx + -2]->minor;
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    } else {//grouping node
        $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
    $this->reducecount++;
    }
#line 1117 "../preg_parser.php"
#line 228 "../preg_parser.y"
    function yy_r14(){
    ECHO  'CONDSUB TF <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in $this->yystack[$this->yyidx + -1]->minor (or $this->yystack[$this->yyidx + -1]->minor->firop/$this->yystack[$this->yyidx + -1]->minor->secop)*/
    if ($this->yystack[$this->yyidx + -1]->minor->subtype != NODE_ALT) {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    } else {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor->firop;
        $this->_retvalue->secop = $this->yystack[$this->yyidx + -1]->minor->secop;
    }
    $this->_retvalue->thirdop->type = NODE;
    $this->_retvalue->thirdop->subtype = $this->yystack[$this->yyidx + -4]->minor;
    $this->_retvalue->thirdop->firop = $this->yystack[$this->yyidx + -3]->minor;
    $this->reducecount++;
    }
#line 1136 "../preg_parser.php"
#line 244 "../preg_parser.y"
    function yy_r15(){
    ECHO 'LEAF <br/>';
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 1144 "../preg_parser.php"
#line 250 "../preg_parser.y"
    function yy_r16(){
    $this->anchor->start = true;
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 1152 "../preg_parser.php"
#line 256 "../preg_parser.y"
    function yy_r17(){
    $this->anchor->end = true;
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    $this->reducecount++;
    }
#line 1160 "../preg_parser.php"
#line 262 "../preg_parser.y"
    function yy_r18(){
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    $this->reducecount++;
    }
#line 1167 "../preg_parser.php"
#line 267 "../preg_parser.y"
    function yy_r19(){
    $this->_retvalue = new node;
    $this->_retvalue->type = LEAF;
    $this->_retvalue->subtype = LEAF_WORDBREAK;
    $this->reducecount++;
    }
#line 1175 "../preg_parser.php"
#line 273 "../preg_parser.y"
    function yy_r20(){
    $this->_retvalue = new node;
    $this->_retvalue->type = LEAF;
    $this->_retvalue->subtype = LEAF_WORDNOTBREAK;
    $this->reducecount++;
    }
#line 1183 "../preg_parser.php"
#line 280 "../preg_parser.y"
    function yy_r21(){
    //ECHO 'UNOPENPARENS <br/>';
    $this->_retvalue = $this->create_error_node('unopenedparen');
    $this->reducecount++;
    }
#line 1190 "../preg_parser.php"
#line 286 "../preg_parser.y"
    function yy_r22(){
    //ECHO 'CLOSEPARENATSTART <br/>';
    if($this->reducecount == 0) {//close bracket at the very start of expression
        $this->_retvalue = $this->create_error_node('closeparenatverystart');
    } else {
        $this->_retvalue = $this->create_error_node('closeparenatstart');
    }
    $this->reducecount++;
    }
#line 1201 "../preg_parser.php"
#line 296 "../preg_parser.y"
    function yy_r23(){
    ECHO 'UNCLOSEDPARENS <br/>';
    end($this->errormessages);
    $unopenstr = get_string('unopenedparen','qtype_preg');
    $closeatstartstr = get_string('closeparenatstart','qtype_preg');
    $i = count($this->errormessages) - 1;
    while ($i>=0 && current($this->errormessages) != $unopenstr && current($this->errormessages) != $closeatstartstr) {
        prev($this->errormessages);//Iterate over all previous error messages except unopened brackets (to not catch 'b)c(f' as empty brackets)
        $i--;
    }
    if ($i>=0 && current($this->errormessages) == $closeatstartstr) {
        //empty brackets, avoiding two error messages
        array_splice($this->errormessages, $i, 1);
        $this->_retvalue = $this->create_error_node('emptyparens',$this->parens[$this->yystack[$this->yyidx + -1]->minor]);
    } else {
        $this->_retvalue = $this->create_error_node('unclosedparen',$this->parens[$this->yystack[$this->yyidx + -1]->minor]);
    }
    $this->reducecount++;
    }
#line 1222 "../preg_parser.php"
#line 316 "../preg_parser.y"
    function yy_r24(){
    $this->_retvalue = $this->create_error_node('openparenatend',$this->parens[$this->yystack[$this->yyidx + 0]->minor]);
    $this->reducecount++;
    }
#line 1228 "../preg_parser.php"
#line 321 "../preg_parser.y"
    function yy_r25(){
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        $this->_retvalue = $this->create_error_node('emptyparens','(?'.$this->parens[$this->yystack[$this->yyidx + -3]->minor]);
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        $this->_retvalue = $this->create_error_node('unclosedparen','(?'.$this->parens[$this->yystack[$this->yyidx + -3]->minor]);
    }
    $this->reducecount++;
    }
#line 1243 "../preg_parser.php"
#line 335 "../preg_parser.y"
    function yy_r26(){
    //ECHO 'UNCLOSEDPARENS <br/>';
    $lasterrormsg = array_pop($this->errormessages);
    if ($lasterrormsg == get_string('closeparenatstart','qtype_preg')) {//empty brackets, avoiding two error messages
        $this->_retvalue = $this->create_error_node('emptyparens','(?'.$this->parens[$this->yystack[$this->yyidx + -1]->minor]);
    } else {//normal unclosed bracket
        if ($lasterrormsg != null) {
            $this->errormessages[] = $lasterrormsg;
        }
        $this->_retvalue = $this->create_error_node('unclosedparen','(?'.$this->parens[$this->yystack[$this->yyidx + -1]->minor]);
    }
    $this->reducecount++;
    }
#line 1258 "../preg_parser.php"
#line 349 "../preg_parser.y"
    function yy_r27(){
    $this->_retvalue = $this->create_error_node('openparenatend','(?'.$this->parens[$this->yystack[$this->yyidx + 0]->minor]);
    $this->reducecount++;
    }
#line 1264 "../preg_parser.php"
#line 354 "../preg_parser.y"
    function yy_r28(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','?');
    $this->reducecount++;
    }
#line 1270 "../preg_parser.php"
#line 359 "../preg_parser.y"
    function yy_r29(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','+');
    $this->reducecount++;
    }
#line 1276 "../preg_parser.php"
#line 364 "../preg_parser.y"
    function yy_r30(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','*');
    $this->reducecount++;
    }
#line 1282 "../preg_parser.php"
#line 369 "../preg_parser.y"
    function yy_r31(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','{...}');
    $this->reducecount++;
    }
#line 1288 "../preg_parser.php"
#line 374 "../preg_parser.y"
    function yy_r32(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','*?');
    $this->reducecount++;
    }
#line 1294 "../preg_parser.php"
#line 379 "../preg_parser.y"
    function yy_r33(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','??');
    $this->reducecount++;
    }
#line 1300 "../preg_parser.php"
#line 384 "../preg_parser.y"
    function yy_r34(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','+?');
    $this->reducecount++;
    }
#line 1306 "../preg_parser.php"
#line 389 "../preg_parser.y"
    function yy_r35(){
    $this->_retvalue = $this->create_error_node('quantifieratstart','{...}?');
    $this->reducecount++;
    }
#line 1312 "../preg_parser.php"
#line 394 "../preg_parser.y"
    function yy_r36(){
    $this->_retvalue = $this->create_error_node($this->yystack[$this->yyidx + 0]->minor);
    $this->reducecount++;
    }
#line 1318 "../preg_parser.php"

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
        //preg_parser_yyStackEntry $yymsp;            /* The top of the parser's stack */
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
                $x = new preg_parser_yyStackEntry;
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
#line 93 "../preg_parser.y"

    if (!$this->error) {
        $this->errormessages[] = get_string('incorrectregex', 'qtype_preg');
        $this->error = true;
    }
#line 1424 "../preg_parser.php"
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
     * "preg_parser_Alloc" which describes the current state of the parser.
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
        if (self::preg_parser_ARG_DECL && $extraargument !== null) {
            $this->{self::preg_parser_ARG_DECL} = $extraargument;
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
            $x = new preg_parser_yyStackEntry;
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