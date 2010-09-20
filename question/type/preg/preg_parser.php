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

    private $root;
    private $anchor;
    private $error;
    private $errormessages;

    function __construct() {
        $this->anchor = new stdClass;
        $this->anchor->start = false;
        $this->anchor->end = false;
        $this->error = false;
        $this->errormessages = array();
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

    static function is_conc($prevtoken, $currtoken) {
        static $condsubpatt = false;
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
        return $flag;
    }
#line 177 "../preg_parser.php"

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
    const ERROR_PREC_SHORT               =  1;
    const ERROR_PREC                     =  2;
    const CLOSEBRACK                     =  3;
    const ALT                            =  4;
    const CONC                           =  5;
    const PARSLEAF                       =  6;
    const WORDBREAK                      =  7;
    const WORDNOTBREAK                   =  8;
    const STARTANCHOR                    =  9;
    const QUEST                          = 10;
    const PLUS                           = 11;
    const ITER                           = 12;
    const QUANT                          = 13;
    const LAZY_ITER                      = 14;
    const LAZY_QUEST                     = 15;
    const LAZY_PLUS                      = 16;
    const LAZY_QUANT                     = 17;
    const OPENBRACK                      = 18;
    const GROUPING                       = 19;
    const ASSERT_TF                      = 20;
    const ASSERT_TB                      = 21;
    const ASSERT_FF                      = 22;
    const ASSERT_FB                      = 23;
    const CONDSUBPATT                    = 24;
    const ONETIMESUBPATT                 = 25;
    const ENDANCHOR                      = 26;
    const YY_NO_ACTION = 124;
    const YY_ACCEPT_ACTION = 123;
    const YY_ERROR_ACTION = 122;

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
    const YY_SZ_ACTTAB = 477;
static public $yy_action = array(
 /*     0 */    78,   39,   35,   77,   74,   73,   25,   63,   65,   64,
 /*    10 */    55,   60,   61,   56,   57,   30,   33,   37,   28,   19,
 /*    20 */    17,   45,   21,   49,   38,   35,   77,   74,   73,   25,
 /*    30 */    63,   65,   64,   55,   60,   61,   56,   57,   30,   33,
 /*    40 */    37,   28,   19,   17,   45,   21,   29,   39,   35,   77,
 /*    50 */    74,   73,   25,   63,   65,   64,   55,   60,   61,   56,
 /*    60 */    57,   30,   33,   37,   28,   19,   17,   45,   21,   23,
 /*    70 */    39,   35,   77,   74,   73,   25,   63,   65,   64,   55,
 /*    80 */    60,   61,   56,   57,   30,   33,   37,   28,   19,   17,
 /*    90 */    45,   21,   54,   39,   35,   77,   74,   73,   25,   63,
 /*   100 */    65,   64,   55,   60,   61,   56,   57,   30,   33,   37,
 /*   110 */    28,   19,   17,   45,   21,   58,   39,   35,   77,   74,
 /*   120 */    73,   25,   63,   65,   64,   55,   60,   61,   56,   57,
 /*   130 */    30,   33,   37,   28,   19,   17,   45,   21,   49,   41,
 /*   140 */    35,   77,   74,   73,   25,   63,   65,   64,   55,   60,
 /*   150 */    61,   56,   57,   30,   33,   37,   28,   19,   17,   45,
 /*   160 */    21,   49,   39,   35,   77,   74,   73,   25,   63,   65,
 /*   170 */    64,   55,   60,   61,   56,   57,   30,   33,   37,   28,
 /*   180 */    19,   17,   45,   21,   67,   39,   35,   77,   74,   73,
 /*   190 */    25,   63,   65,   64,   55,   60,   61,   56,   57,   30,
 /*   200 */    33,   37,   28,   19,   17,   45,   21,   76,   39,   35,
 /*   210 */    77,   74,   73,   25,   63,   65,   64,   55,   60,   61,
 /*   220 */    56,   57,   30,   33,   37,   28,   19,   17,   45,   21,
 /*   230 */    34,   39,   35,   77,   74,   73,   25,   63,   65,   64,
 /*   240 */    55,   60,   61,   56,   57,   30,   33,   37,   28,   19,
 /*   250 */    17,   45,   21,   75,   39,   35,   77,   74,   73,   25,
 /*   260 */    63,   65,   64,   55,   60,   61,   56,   57,   30,   33,
 /*   270 */    37,   28,   19,   17,   45,   21,   36,   39,   35,   77,
 /*   280 */    74,   73,   25,   63,   65,   64,   55,   60,   61,   56,
 /*   290 */    57,   30,   33,   37,   28,   19,   17,   45,   21,   49,
 /*   300 */    40,   35,   77,   74,   73,   25,   63,   65,   64,   55,
 /*   310 */    60,   61,   56,   57,   30,   33,   37,   28,   19,   17,
 /*   320 */    45,   21,   66,   39,   35,   77,   74,   73,   25,   63,
 /*   330 */    65,   64,   55,   60,   61,   56,   57,   30,   33,   37,
 /*   340 */    28,   19,   17,   45,   21,   59,   39,   35,   77,   74,
 /*   350 */    73,   25,   63,   65,   64,   55,   60,   61,   56,   57,
 /*   360 */    30,   33,   37,   28,   19,   17,   45,   21,   69,    8,
 /*   370 */    42,   77,   74,   73,   25,   68,   70,   71,   72,   79,
 /*   380 */    50,   48,   47,   30,   33,   37,   28,   19,   17,   45,
 /*   390 */    21,   35,   77,   74,   73,   25,   63,   65,   64,   55,
 /*   400 */    60,   61,   56,   57,   30,   33,   37,   28,   19,   17,
 /*   410 */    45,   21,   77,   74,   73,   25,   68,   70,   71,   72,
 /*   420 */    79,   50,   48,   47,   30,   33,   37,   28,   19,   17,
 /*   430 */    45,   21,   63,   65,   64,   55,   60,   61,   56,   57,
 /*   440 */    30,   33,   37,   28,   19,   17,   45,   21,   24,   32,
 /*   450 */    18,   27,  123,   46,   14,    5,    3,   31,   62,   26,
 /*   460 */    22,   44,   75,    4,   16,   20,   12,   43,    7,   15,
 /*   470 */     9,   11,   13,    2,    1,   10,    6,
    );
    static public $yy_lookahead = array(
 /*     0 */     3,    4,    5,    6,    7,    8,    9,   10,   11,   12,
 /*    10 */    13,   14,   15,   16,   17,   18,   19,   20,   21,   22,
 /*    20 */    23,   24,   25,    3,    4,    5,    6,    7,    8,    9,
 /*    30 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*    40 */    20,   21,   22,   23,   24,   25,    3,    4,    5,    6,
 /*    50 */     7,    8,    9,   10,   11,   12,   13,   14,   15,   16,
 /*    60 */    17,   18,   19,   20,   21,   22,   23,   24,   25,    3,
 /*    70 */     4,    5,    6,    7,    8,    9,   10,   11,   12,   13,
 /*    80 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   23,
 /*    90 */    24,   25,    3,    4,    5,    6,    7,    8,    9,   10,
 /*   100 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*   110 */    21,   22,   23,   24,   25,    3,    4,    5,    6,    7,
 /*   120 */     8,    9,   10,   11,   12,   13,   14,   15,   16,   17,
 /*   130 */    18,   19,   20,   21,   22,   23,   24,   25,    3,    4,
 /*   140 */     5,    6,    7,    8,    9,   10,   11,   12,   13,   14,
 /*   150 */    15,   16,   17,   18,   19,   20,   21,   22,   23,   24,
 /*   160 */    25,    3,    4,    5,    6,    7,    8,    9,   10,   11,
 /*   170 */    12,   13,   14,   15,   16,   17,   18,   19,   20,   21,
 /*   180 */    22,   23,   24,   25,    3,    4,    5,    6,    7,    8,
 /*   190 */     9,   10,   11,   12,   13,   14,   15,   16,   17,   18,
 /*   200 */    19,   20,   21,   22,   23,   24,   25,    3,    4,    5,
 /*   210 */     6,    7,    8,    9,   10,   11,   12,   13,   14,   15,
 /*   220 */    16,   17,   18,   19,   20,   21,   22,   23,   24,   25,
 /*   230 */     3,    4,    5,    6,    7,    8,    9,   10,   11,   12,
 /*   240 */    13,   14,   15,   16,   17,   18,   19,   20,   21,   22,
 /*   250 */    23,   24,   25,    3,    4,    5,    6,    7,    8,    9,
 /*   260 */    10,   11,   12,   13,   14,   15,   16,   17,   18,   19,
 /*   270 */    20,   21,   22,   23,   24,   25,    3,    4,    5,    6,
 /*   280 */     7,    8,    9,   10,   11,   12,   13,   14,   15,   16,
 /*   290 */    17,   18,   19,   20,   21,   22,   23,   24,   25,    3,
 /*   300 */     4,    5,    6,    7,    8,    9,   10,   11,   12,   13,
 /*   310 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   23,
 /*   320 */    24,   25,    3,    4,    5,    6,    7,    8,    9,   10,
 /*   330 */    11,   12,   13,   14,   15,   16,   17,   18,   19,   20,
 /*   340 */    21,   22,   23,   24,   25,    3,    4,    5,    6,    7,
 /*   350 */     8,    9,   10,   11,   12,   13,   14,   15,   16,   17,
 /*   360 */    18,   19,   20,   21,   22,   23,   24,   25,    3,   30,
 /*   370 */    30,    6,    7,    8,    9,   10,   11,   12,   13,   14,
 /*   380 */    15,   16,   17,   18,   19,   20,   21,   22,   23,   24,
 /*   390 */    25,    5,    6,    7,    8,    9,   10,   11,   12,   13,
 /*   400 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   23,
 /*   410 */    24,   25,    6,    7,    8,    9,   10,   11,   12,   13,
 /*   420 */    14,   15,   16,   17,   18,   19,   20,   21,   22,   23,
 /*   430 */    24,   25,   10,   11,   12,   13,   14,   15,   16,   17,
 /*   440 */    18,   19,   20,   21,   22,   23,   24,   25,   20,   21,
 /*   450 */    22,   23,   28,   29,   30,   30,   30,   30,   26,   30,
 /*   460 */    30,   30,   31,   30,   30,   30,   30,   30,   30,   30,
 /*   470 */    30,   30,   30,   30,   30,   30,   30,
);
    const YY_SHIFT_USE_DFLT = -4;
    const YY_SHIFT_MAX = 46;
    static public $yy_shift_ofst = array(
 /*     0 */   365,  273,  250,  319,  296,  342,  204,   66,   43,   20,
 /*    10 */    -3,   89,  112,  181,  158,  227,  135,  365,  365,  365,
 /*    20 */   386,  365,  386,  365,  365,  365,  386,  365,  365,  365,
 /*    30 */   365,  386,  365,  365,  365,  365,  365,  365,  406,  406,
 /*    40 */   406,  406,  422,  422,  422,  428,  432,
);
    const YY_REDUCE_USE_DFLT = -1;
    const YY_REDUCE_MAX = 44;
    static public $yy_reduce_ofst = array(
 /*     0 */   424,  340,  340,  340,  340,  340,  340,  340,  340,  340,
 /*    10 */   340,  340,  340,  340,  340,  340,  340,  446,  444,  443,
 /*    20 */   340,  445,  340,  441,  438,  437,  340,  439,  442,  440,
 /*    30 */   436,  340,  339,  425,  434,  431,  433,  426,  430,  435,
 /*    40 */   427,  429,  340,  340,  340,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 1 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 2 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 3 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 4 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 5 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 6 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 7 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 8 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 9 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 10 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 11 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 12 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 13 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 14 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 15 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 16 */ array(3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 17 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 18 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 19 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 20 */ array(5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 21 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 22 */ array(5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 23 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 24 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 25 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 26 */ array(5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 27 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 28 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 29 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 30 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 31 */ array(5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 32 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 33 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 34 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 35 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 36 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 37 */ array(3, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 38 */ array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 39 */ array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 40 */ array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 41 */ array(6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 42 */ array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 43 */ array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 44 */ array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, ),
        /* 45 */ array(20, 21, 22, 23, ),
        /* 46 */ array(26, ),
        /* 47 */ array(),
        /* 48 */ array(),
        /* 49 */ array(),
        /* 50 */ array(),
        /* 51 */ array(),
        /* 52 */ array(),
        /* 53 */ array(),
        /* 54 */ array(),
        /* 55 */ array(),
        /* 56 */ array(),
        /* 57 */ array(),
        /* 58 */ array(),
        /* 59 */ array(),
        /* 60 */ array(),
        /* 61 */ array(),
        /* 62 */ array(),
        /* 63 */ array(),
        /* 64 */ array(),
        /* 65 */ array(),
        /* 66 */ array(),
        /* 67 */ array(),
        /* 68 */ array(),
        /* 69 */ array(),
        /* 70 */ array(),
        /* 71 */ array(),
        /* 72 */ array(),
        /* 73 */ array(),
        /* 74 */ array(),
        /* 75 */ array(),
        /* 76 */ array(),
        /* 77 */ array(),
        /* 78 */ array(),
        /* 79 */ array(),
);
    static public $yy_default = array(
 /*     0 */   122,  122,  122,  122,  122,  122,  122,  122,  122,  122,
 /*    10 */   122,  122,  112,  122,  106,  122,  122,  122,  122,  122,
 /*    20 */    83,  122,   83,  110,  122,  122,   83,  122,  122,  110,
 /*    30 */   113,   83,  122,  122,  110,  122,  110,  122,   84,   84,
 /*    40 */    84,   84,   82,  104,   81,  122,   80,  121,  120,  110,
 /*    50 */   119,  100,  102,  101,   99,   91,   90,   92,   93,   94,
 /*    60 */    89,   88,  105,   85,   86,   87,   95,   96,  114,  111,
 /*    70 */   115,  116,  117,  109,  108,   97,   98,  103,  107,  118,
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
    const YYNOCODE = 32;
    const YYSTACKDEPTH = 100;
    const preg_parser_ARG_DECL = '0';
    const YYNSTATE = 80;
    const YYNRULE = 42;
    const YYERRORSYMBOL = 27;
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
  '$',             'ERROR_PREC_SHORT',  'ERROR_PREC',    'CLOSEBRACK',  
  'ALT',           'CONC',          'PARSLEAF',      'WORDBREAK',   
  'WORDNOTBREAK',  'STARTANCHOR',   'QUEST',         'PLUS',        
  'ITER',          'QUANT',         'LAZY_ITER',     'LAZY_QUEST',  
  'LAZY_PLUS',     'LAZY_QUANT',    'OPENBRACK',     'GROUPING',    
  'ASSERT_TF',     'ASSERT_TB',     'ASSERT_FF',     'ASSERT_FB',   
  'CONDSUBPATT',   'ONETIMESUBPATT',  'ENDANCHOR',     'error',       
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
 /*  14 */ "expr ::= GROUPING expr CLOSEBRACK",
 /*  15 */ "expr ::= ASSERT_TF expr CLOSEBRACK",
 /*  16 */ "expr ::= ASSERT_TB expr CLOSEBRACK",
 /*  17 */ "expr ::= ASSERT_FF expr CLOSEBRACK",
 /*  18 */ "expr ::= ASSERT_FB expr CLOSEBRACK",
 /*  19 */ "expr ::= CONDSUBPATT ASSERT_TF expr CLOSEBRACK expr CLOSEBRACK",
 /*  20 */ "expr ::= CONDSUBPATT ASSERT_TB expr CLOSEBRACK expr ALT expr CLOSEBRACK",
 /*  21 */ "expr ::= CONDSUBPATT ASSERT_FF expr CLOSEBRACK expr ALT expr CLOSEBRACK",
 /*  22 */ "expr ::= CONDSUBPATT ASSERT_FB expr CLOSEBRACK expr ALT expr CLOSEBRACK",
 /*  23 */ "expr ::= PARSLEAF",
 /*  24 */ "expr ::= STARTANCHOR expr",
 /*  25 */ "lastexpr ::= lastexpr ENDANCHOR",
 /*  26 */ "lastexpr ::= expr",
 /*  27 */ "expr ::= ONETIMESUBPATT expr CLOSEBRACK",
 /*  28 */ "expr ::= WORDBREAK",
 /*  29 */ "expr ::= WORDNOTBREAK",
 /*  30 */ "expr ::= expr CLOSEBRACK",
 /*  31 */ "expr ::= CLOSEBRACK",
 /*  32 */ "expr ::= OPENBRACK expr",
 /*  33 */ "expr ::= OPENBRACK",
 /*  34 */ "expr ::= QUEST",
 /*  35 */ "expr ::= PLUS",
 /*  36 */ "expr ::= ITER",
 /*  37 */ "expr ::= QUANT",
 /*  38 */ "expr ::= LAZY_ITER",
 /*  39 */ "expr ::= LAZY_QUEST",
 /*  40 */ "expr ::= LAZY_PLUS",
 /*  41 */ "expr ::= LAZY_QUANT",
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
  array( 'lhs' => 28, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 6 ),
  array( 'lhs' => 30, 'rhs' => 8 ),
  array( 'lhs' => 30, 'rhs' => 8 ),
  array( 'lhs' => 30, 'rhs' => 8 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 29, 'rhs' => 2 ),
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 2 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
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
        37 => 37,
        38 => 38,
        39 => 39,
        40 => 40,
        41 => 41,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 83 "../preg_parser.y"
    function yy_r0(){
    $this->root = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1084 "../preg_parser.php"
#line 86 "../preg_parser.y"
    function yy_r1(){
    ECHO 'CONC <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONC;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1094 "../preg_parser.php"
#line 94 "../preg_parser.y"
    function yy_r2(){
    ECHO 'CONC1 <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONC;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1104 "../preg_parser.php"
#line 102 "../preg_parser.y"
    function yy_r3(){
    ECHO 'ALT <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ALT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -2]->minor;
    $this->_retvalue->secop = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1114 "../preg_parser.php"
#line 110 "../preg_parser.y"
    function yy_r4(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ALT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    $this->_retvalue->secop = new node;
    $this->_retvalue->secop->type = LEAF;
    $this->_retvalue->secop->subtype = LEAF_EMPTY;
    }
#line 1125 "../preg_parser.php"
#line 119 "../preg_parser.y"
    function yy_r5(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUESTQUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1134 "../preg_parser.php"
#line 126 "../preg_parser.y"
    function yy_r6(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ITER;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1143 "../preg_parser.php"
#line 133 "../preg_parser.y"
    function yy_r7(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_PLUSQUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1152 "../preg_parser.php"
#line 140 "../preg_parser.y"
    function yy_r8(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUESTQUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1161 "../preg_parser.php"
#line 147 "../preg_parser.y"
    function yy_r9(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ITER;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1170 "../preg_parser.php"
#line 154 "../preg_parser.y"
    function yy_r10(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_PLUSQUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1179 "../preg_parser.php"
#line 161 "../preg_parser.y"
    function yy_r11(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUANT;
    $this->_retvalue->greed = true;
    $this->_retvalue->leftborder = $this->yystack[$this->yyidx + 0]->minor->leftborder;
    $this->_retvalue->rightborder = $this->yystack[$this->yyidx + 0]->minor->rightborder;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1190 "../preg_parser.php"
#line 170 "../preg_parser.y"
    function yy_r12(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_QUANT;
    $this->_retvalue->greed = false;
    $this->_retvalue->leftborder = $this->yystack[$this->yyidx + 0]->minor->leftborder;
    $this->_retvalue->rightborder = $this->yystack[$this->yyidx + 0]->minor->rightborder;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1201 "../preg_parser.php"
#line 179 "../preg_parser.y"
    function yy_r13(){
    ECHO 'SUBPATT <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_SUBPATT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1210 "../preg_parser.php"
#line 186 "../preg_parser.y"
    function yy_r14(){
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1215 "../preg_parser.php"
#line 189 "../preg_parser.y"
    function yy_r15(){
    ECHO  'ASSERT TF <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ASSERTTF;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1224 "../preg_parser.php"
#line 196 "../preg_parser.y"
    function yy_r16(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ASSERTTB;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1232 "../preg_parser.php"
#line 202 "../preg_parser.y"
    function yy_r17(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ASSERTFF;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1240 "../preg_parser.php"
#line 208 "../preg_parser.y"
    function yy_r18(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ASSERTFB;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1248 "../preg_parser.php"
#line 214 "../preg_parser.y"
    function yy_r19(){
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
    $this->_retvalue->thirdop->subtype = NODE_ASSERTTF;
    $this->_retvalue->thirdop->firop = $this->yystack[$this->yyidx + -3]->minor;
    }
#line 1266 "../preg_parser.php"
#line 229 "../preg_parser.y"
    function yy_r20(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in $this->yystack[$this->yyidx + -3]->minor (or $this->yystack[$this->yyidx + -3]->minor->firop/$this->yystack[$this->yyidx + -3]->minor->secop)*/
    if ($this->yystack[$this->yyidx + -3]->minor->subtype != NODE_ALT) {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor;
    } else {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor->firop;
        $this->_retvalue->secop = $this->yystack[$this->yyidx + -3]->minor->secop;
    }
    $this->_retvalue->thirdop->type = NODE;
    $this->_retvalue->thirdop->subtype = NODE_ASSERTTB;
    $this->_retvalue->thirdop->firop = $this->yystack[$this->yyidx + -5]->minor;
    }
#line 1283 "../preg_parser.php"
#line 243 "../preg_parser.y"
    function yy_r21(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in $this->yystack[$this->yyidx + -3]->minor (or $this->yystack[$this->yyidx + -3]->minor->firop/$this->yystack[$this->yyidx + -3]->minor->secop)*/
    if ($this->yystack[$this->yyidx + -3]->minor->subtype != NODE_ALT) {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor;
    } else {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor->firop;
        $this->_retvalue->secop = $this->yystack[$this->yyidx + -3]->minor->secop;
    }
    $this->_retvalue->thirdop->type = NODE;
    $this->_retvalue->thirdop->subtype = NODE_ASSERTFF;
    $this->_retvalue->thirdop->firop = $this->yystack[$this->yyidx + -5]->minor;
    }
#line 1300 "../preg_parser.php"
#line 257 "../preg_parser.y"
    function yy_r22(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_CONDSUBPATT;
    /*TODO - add check that there is no ALT operators in $this->yystack[$this->yyidx + -3]->minor (or $this->yystack[$this->yyidx + -3]->minor->firop/$this->yystack[$this->yyidx + -3]->minor->secop)*/
    if ($this->yystack[$this->yyidx + -3]->minor->subtype != NODE_ALT) {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor;
    } else {
        $this->_retvalue->firop = $this->yystack[$this->yyidx + -3]->minor->firop;
        $this->_retvalue->secop = $this->yystack[$this->yyidx + -3]->minor->secop;
    }
    $this->_retvalue->thirdop->type = NODE;
    $this->_retvalue->thirdop->subtype = NODE_ASSERTFB;
    $this->_retvalue->thirdop->firop = $this->yystack[$this->yyidx + -5]->minor;
    }
#line 1317 "../preg_parser.php"
#line 271 "../preg_parser.y"
    function yy_r23(){
    ECHO 'LEAF <br/>';
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1324 "../preg_parser.php"
#line 276 "../preg_parser.y"
    function yy_r24(){
    $this->anchor->start = true;
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1331 "../preg_parser.php"
#line 281 "../preg_parser.y"
    function yy_r25(){
    $this->anchor->end = true;
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1338 "../preg_parser.php"
#line 286 "../preg_parser.y"
    function yy_r26(){
    $this->_retvalue = new node;
    $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;
    }
#line 1344 "../preg_parser.php"
#line 290 "../preg_parser.y"
    function yy_r27(){
    $this->_retvalue = new node;
    $this->_retvalue->type = NODE;
    $this->_retvalue->subtype = NODE_ONETIMESUBPATT;
    $this->_retvalue->firop = $this->yystack[$this->yyidx + -1]->minor;
    }
#line 1352 "../preg_parser.php"
#line 296 "../preg_parser.y"
    function yy_r28(){
    $this->_retvalue = new node;
    $this->_retvalue->type = LEAF;
    $this->_retvalue->subtype = LEAF_WORDBREAK;
    }
#line 1359 "../preg_parser.php"
#line 301 "../preg_parser.y"
    function yy_r29(){
    $this->_retvalue = new node;
    $this->_retvalue->type = LEAF;
    $this->_retvalue->subtype = LEAF_WORDNOTBREAK;
    }
#line 1366 "../preg_parser.php"
#line 307 "../preg_parser.y"
    function yy_r30(){
    ECHO 'UNOPENPARENS <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('unopenedparen','qtype_preg');
    $this->error = true;
    }
#line 1375 "../preg_parser.php"
#line 315 "../preg_parser.y"
    function yy_r31(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('closeparenatstart','qtype_preg');
    $this->error = true;
    }
#line 1383 "../preg_parser.php"
#line 322 "../preg_parser.y"
    function yy_r32(){
    ECHO 'UNCLOSEDPARENS <br/>';
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('unclosedparen','qtype_preg');
    $this->error = true; 
    }
#line 1392 "../preg_parser.php"
#line 330 "../preg_parser.y"
    function yy_r33(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('openparenatend','qtype_preg');
    $this->error = true; 
    }
#line 1400 "../preg_parser.php"
#line 337 "../preg_parser.y"
    function yy_r34(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','?');
    $this->error = true; 
    }
#line 1408 "../preg_parser.php"
#line 344 "../preg_parser.y"
    function yy_r35(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','+');
    $this->error = true; 
    }
#line 1416 "../preg_parser.php"
#line 351 "../preg_parser.y"
    function yy_r36(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','*');
    $this->error = true; 
    }
#line 1424 "../preg_parser.php"
#line 358 "../preg_parser.y"
    function yy_r37(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','{..}');
    $this->error = true; 
    }
#line 1432 "../preg_parser.php"
#line 365 "../preg_parser.y"
    function yy_r38(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','*?');
    $this->error = true; 
    }
#line 1440 "../preg_parser.php"
#line 372 "../preg_parser.y"
    function yy_r39(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','??');
    $this->error = true; 
    }
#line 1448 "../preg_parser.php"
#line 379 "../preg_parser.y"
    function yy_r40(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','+?');
    $this->error = true; 
    }
#line 1456 "../preg_parser.php"
#line 386 "../preg_parser.y"
    function yy_r41(){
    $this->_retvalue = new node;
    $this->_retvalue->type = ERROR;
    $this->errormessages[] = get_string('quantifieratstart','qtype_preg','{..}?');
    $this->error = true; 
    }
#line 1464 "../preg_parser.php"

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
#line 69 "../preg_parser.y"

    if (!$this->error) {
        $this->errormessages[] = get_string('incorrectregex', 'qtype_preg');
        $this->error = true;
    }
#line 1570 "../preg_parser.php"
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