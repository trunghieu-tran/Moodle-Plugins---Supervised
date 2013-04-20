<?php
require_once 'PEAR.php';
/* the tokenizer states */
define('YYINITIAL', 0);
define('INMOVES', 1);
define('INTAG', 2);
define('INSTRING', 3);
define('INESCAPE', 4);
define('INCOMMENT', 5);
$a = 0;
define('FILE_CHESSPGN_TAGOPEN', ++$a); // [
define('FILE_CHESSPGN_TAGNAME', ++$a); // ]
define('FILE_CHESSPGN_TAGCLOSE', ++$a); // ]
define('FILE_CHESSPGN_STRING', ++$a); // ]
define('FILE_CHESSPGN_NAG', ++$a); // $1 $2, etc.
define('FILE_CHESSPGN_GAMEEND', ++$a); // * 1-0 0-1
define('FILE_CHESSPGN_PAWNMOVE', ++$a); // e4, e8=Q, exd8=R
define('FILE_CHESSPGN_PIECEMOVE', ++$a); // Nf2, Nab2, N3d5, Qxe4, Qexe4, Q3xe4
define('FILE_CHESSPGN_PLACEMENTMOVE', ++$a); // N@f2, P@d5
define('FILE_CHESSPGN_CHECK', ++$a); // +
define('FILE_CHESSPGN_MATE', ++$a); // #
define('FILE_CHESSPGN_DIGIT', ++$a); // 0-9
define('FILE_CHESSPGN_MOVEANNOT', ++$a); // ! ? !! ?? !? ?!
define('FILE_CHESSPGN_RAVOPEN', ++$a); // (
define('FILE_CHESSPGN_RAVCLOSE', ++$a); // )
define('FILE_CHESSPGN_PERIOD', ++$a); // .
define('FILE_CHESSPGN_COMMENTOPEN', ++$a); // {
define('FILE_CHESSPGN_COMMENTCLOSE', ++$a); // }
define('FILE_CHESSPGN_COMMENT', ++$a); // anything
define('FILE_CHESSPGN_CASTLE', ++$a); // O-O O-O-O
define ('YY_E_INTERNAL', 0);
define ('YY_E_MATCH',  1);
define ('YY_BUFFER_SIZE', 4096);
define ('YY_F' , -1);
define ('YY_NO_STATE', -1);
define ('YY_NOT_ACCEPT' ,  0);
define ('YY_START' , 1);
define ('YY_END' , 2);
define ('YY_NO_ANCHOR' , 4);
define ('YY_BOL' , 257);
define ('YY_EOF' , 258);


class File_ChessPGN_Lexer
{

    /**
     * @var array
     * @access private
     */
    var $_options = array(
                    );
    /**
     * return something useful, when a parse error occurs.
     *
     * used to build error messages if the parser fails, and needs to know the line number..
     *
     * @return   string 
     * @access   public 
     */
    function parseError() 
    {
        return "Error at line {$this->yyline}";
    }
    function setOptions($options = array())
    {
        $this->_options = array_merge($this->_options, $options);
    }
    function advance($parser)
    {
        $lex = $this->yylex();
        if ($lex) {
            $this->token = $parser->transTable[$lex[0]];
            $this->value = $lex[1];
        }
        return (boolean) $lex;
    }
    function raiseError($code, $msg, $fatal)
    {
        return PEAR::raiseError($code, $msg, $fatal);
    }
    function setup($data)
    {
        $this->File_ChessPGN_Lexer($data);
    }


    var $yy_reader;
    var $yy_buffer_index;
    var $yy_buffer_read;
    var $yy_buffer_start;
    var $yy_buffer_end;
    var $yy_buffer;
    var $yychar;
    var $yyline;
    var $yyEndOfLine;
    var $yy_at_bol;
    var $yy_lexical_state;

    function File_ChessPGN_Lexer($data) 
    {
        $this->yy_buffer = $data;
        $this->yy_buffer_read = strlen($data);
        $this->yy_buffer_index = 0;
        $this->yy_buffer_start = 0;
        $this->yy_buffer_end = 0;
        $this->yychar = 0;
        $this->yyline = 0;
        $this->yy_at_bol = true;
        $this->yy_lexical_state = YYINITIAL;

        $this->yy_buffer = str_replace("\r", '', $this->yy_buffer);
        $this->yy_buffer_read = strlen($this->yy_buffer);
        $this->_original = YYINITIAL;
        $this->_oldOriginal = YYINITIAL;
        $this->_listOriginal = YYINITIAL;
        $this->_listLevel = array();
        $this->_atBullet = false;
        $this->_atNewLine = false;
        $this->_listTypeStack = array();
        $this->_bulletStack = array();
        $this->_bulletLenStack = array();
        $this->_break = false;
        $this->debug = false;
        $this->yyline = 1;
    }

    var $yy_state_dtrans = array  ( 
        0,
        58,
        19,
        60,
        61,
        28
    );


    function yybegin ($state)
    {
        $this->yy_lexical_state = $state;
    }



    function yy_advance ()
    {
        if ($this->yy_buffer_index < $this->yy_buffer_read) {
            return ord($this->yy_buffer{$this->yy_buffer_index++});
        }
        return YY_EOF;
    }


    function yy_move_end ()
    {
        if ($this->yy_buffer_end > $this->yy_buffer_start && 
            '\n' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
        if ($this->yy_buffer_end > $this->yy_buffer_start &&
            '\r' == $this->yy_buffer{$this->yy_buffer_end-1})
        {
            $this->yy_buffer_end--;
        }
    }


    var $yy_last_was_cr=false;


    function yy_mark_start ()
    {
        for ($i = $this->yy_buffer_start; $i < $this->yy_buffer_index; $i++) {
            if ($this->yy_buffer{$i} == "\n" && !$this->yy_last_was_cr) {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
            }
            if ($this->yy_buffer{$i} == "\r") {
                $this->yyline++; $this->yyEndOfLine = $this->yychar;
                $this->yy_last_was_cr=true;
            } else {
                $this->yy_last_was_cr=false;
            }
        }
        $this->yychar = $this->yychar + $this->yy_buffer_index - $this->yy_buffer_start;
        $this->yy_buffer_start = $this->yy_buffer_index;
    }


    function yy_mark_end ()
    {
        $this->yy_buffer_end = $this->yy_buffer_index;
    }


    function  yy_to_mark ()
    {
        $this->yy_buffer_index = $this->yy_buffer_end;
        $this->yy_at_bol = ($this->yy_buffer_end > $this->yy_buffer_start) &&
            ($this->yy_buffer{$this->yy_buffer_end-1} == '\r' ||
            $this->yy_buffer{$this->yy_buffer_end-1} == '\n');
    }


    function yytext()
    {
        return substr($this->yy_buffer,$this->yy_buffer_start,$this->yy_buffer_end - $this->yy_buffer_start);
    }


    function yylength ()
    {
        return $this->yy_buffer_end - $this->yy_buffer_start;
    }


    var $yy_error_string = array(
        "Error: Internal error.\n",
        "Error: Unmatched input.\n"
        );


    function yy_error ($code,$fatal)
    {
        if (method_exists($this,'raiseError')) { 
         return $this->raiseError($code, $this->yy_error_string[$code], $fatal); 
     }
        echo $this->yy_error_string[$code];
        if ($fatal) {
            exit;
        }
    }


    var  $yy_acpt = array (
        /* 0 */   YY_NOT_ACCEPT,
        /* 1 */   YY_NO_ANCHOR,
        /* 2 */   YY_NO_ANCHOR,
        /* 3 */   YY_NO_ANCHOR,
        /* 4 */   YY_NO_ANCHOR,
        /* 5 */   YY_NO_ANCHOR,
        /* 6 */   YY_NO_ANCHOR,
        /* 7 */   YY_NO_ANCHOR,
        /* 8 */   YY_NO_ANCHOR,
        /* 9 */   YY_NO_ANCHOR,
        /* 10 */   YY_NO_ANCHOR,
        /* 11 */   YY_NO_ANCHOR,
        /* 12 */   YY_NO_ANCHOR,
        /* 13 */   YY_NO_ANCHOR,
        /* 14 */   YY_NO_ANCHOR,
        /* 15 */   YY_NO_ANCHOR,
        /* 16 */   YY_NO_ANCHOR,
        /* 17 */   YY_NO_ANCHOR,
        /* 18 */   YY_NO_ANCHOR,
        /* 19 */   YY_NO_ANCHOR,
        /* 20 */   YY_NO_ANCHOR,
        /* 21 */   YY_NO_ANCHOR,
        /* 22 */   YY_NO_ANCHOR,
        /* 23 */   YY_NO_ANCHOR,
        /* 24 */   YY_NO_ANCHOR,
        /* 25 */   YY_NO_ANCHOR,
        /* 26 */   YY_NO_ANCHOR,
        /* 27 */   YY_NO_ANCHOR,
        /* 28 */   YY_NO_ANCHOR,
        /* 29 */   YY_NO_ANCHOR,
        /* 30 */   YY_NOT_ACCEPT,
        /* 31 */   YY_NO_ANCHOR,
        /* 32 */   YY_NO_ANCHOR,
        /* 33 */   YY_NO_ANCHOR,
        /* 34 */   YY_NO_ANCHOR,
        /* 35 */   YY_NO_ANCHOR,
        /* 36 */   YY_NOT_ACCEPT,
        /* 37 */   YY_NO_ANCHOR,
        /* 38 */   YY_NOT_ACCEPT,
        /* 39 */   YY_NOT_ACCEPT,
        /* 40 */   YY_NOT_ACCEPT,
        /* 41 */   YY_NOT_ACCEPT,
        /* 42 */   YY_NOT_ACCEPT,
        /* 43 */   YY_NOT_ACCEPT,
        /* 44 */   YY_NOT_ACCEPT,
        /* 45 */   YY_NOT_ACCEPT,
        /* 46 */   YY_NOT_ACCEPT,
        /* 47 */   YY_NOT_ACCEPT,
        /* 48 */   YY_NOT_ACCEPT,
        /* 49 */   YY_NOT_ACCEPT,
        /* 50 */   YY_NOT_ACCEPT,
        /* 51 */   YY_NOT_ACCEPT,
        /* 52 */   YY_NOT_ACCEPT,
        /* 53 */   YY_NOT_ACCEPT,
        /* 54 */   YY_NOT_ACCEPT,
        /* 55 */   YY_NOT_ACCEPT,
        /* 56 */   YY_NOT_ACCEPT,
        /* 57 */   YY_NOT_ACCEPT,
        /* 58 */   YY_NOT_ACCEPT,
        /* 59 */   YY_NOT_ACCEPT,
        /* 60 */   YY_NOT_ACCEPT,
        /* 61 */   YY_NOT_ACCEPT,
        /* 62 */   YY_NOT_ACCEPT,
        /* 63 */   YY_NOT_ACCEPT
        );


    var  $yy_cmap = array(
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 31, 31, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        20, 28, 32, 27, 23, 30, 30, 30,
        2, 29, 24, 26, 30, 15, 25, 17,
        16, 14, 18, 6, 6, 6, 6, 6,
        7, 3, 30, 30, 30, 8, 30, 28,
        12, 30, 9, 30, 30, 30, 30, 30,
        30, 30, 30, 11, 30, 30, 9, 19,
        4, 9, 9, 30, 30, 30, 30, 30,
        30, 30, 30, 1, 33, 21, 30, 30,
        30, 5, 5, 5, 5, 5, 5, 5,
        5, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        10, 30, 30, 13, 30, 22, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 30, 30, 30, 30, 30, 30, 30,
        30, 0, 0 
         );


    var $yy_rmap = array(
        0, 1, 1, 1, 1, 2, 1, 1,
        1, 3, 1, 1, 1, 1, 1, 1,
        4, 1, 5, 6, 1, 1, 1, 7,
        1, 1, 1, 1, 8, 1, 9, 10,
        1, 1, 11, 12, 13, 14, 15, 16,
        17, 18, 19, 20, 21, 22, 23, 24,
        25, 26, 27, 28, 29, 18, 30, 31,
        32, 33, 34, 5, 35, 36, 37, 38
        );


    var $yy_nxt = array(
        array( 1, 2, 3, 3, 30, 36, 3, 3,
            -1, 38, -1, 38, -1, 4, 31, -1,
            37, -1, 3, 39, 5, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 5,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 5, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 5,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 55,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, 33, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, 18, -1, -1, 18, 18,
            -1, -1, -1, -1, -1, -1, 18, -1,
            18, -1, 18, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( 1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 20, 21, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            22, 34 ),
        array( -1, -1, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            -1, -1 ),
        array( 1, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 29, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35 ),
        array( -1, -1, -1, -1, -1, 36, -1, -1,
            -1, -1, -1, -1, 40, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 44,
            -1, 45, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            34, 34, 34, 34, -1, -1, 34, 34,
            34, 34, 34, 34, 34, 34, 34, 34,
            -1, 34 ),
        array( -1, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35, 35, 35, 35, 35, -1, 35,
            35, 35, 35, 35, 35, 35, 35, 35,
            35, 35 ),
        array( -1, -1, -1, -1, -1, -1, 6, 41,
            -1, -1, 62, -1, -1, -1, 41, -1,
            -1, -1, 6, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 46,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, 42, 43, 43,
            -1, -1, 63, -1, 40, -1, 43, -1,
            -1, -1, 43, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 47,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, 48, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            49, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, 51, 7, 7,
            -1, -1, 63, -1, -1, -1, 7, -1,
            -1, -1, 7, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, 51, -1, -1,
            -1, -1, 63, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            8, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 52, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 8, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 9, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 10, 10,
            -1, -1, -1, -1, -1, -1, 10, -1,
            -1, -1, 10, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 6, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 6, 53,
            -1, -1, -1, -1, -1, -1, 53, -1,
            -1, -1, 6, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, 7, 7,
            -1, -1, -1, -1, -1, -1, 7, -1,
            -1, -1, 7, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, 54,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, 56, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, 32, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, 57, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, 11, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( 1, -1, 3, 3, 30, 36, 3, 3,
            -1, 38, -1, 38, -1, 4, 31, -1,
            37, -1, 3, 39, 5, -1, -1, 59,
            12, 13, 14, 15, 16, 17, -1, 5,
            -1, -1 ),
        array( 1, -1, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            23, 23, 23, 23, 23, 23, 23, 23,
            24, 25 ),
        array( 1, 26, 26, 26, 26, 26, 26, 26,
            26, 26, 26, 26, 26, 26, 26, 26,
            26, 26, 26, 26, 26, 26, 26, 26,
            26, 26, 26, 26, 26, 26, 26, 26,
            27, 27 ),
        array( -1, -1, -1, -1, -1, 50, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 ),
        array( -1, -1, -1, -1, -1, 51, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1 )
        );


    function  yylex()
    {
        $yy_lookahead = '';
        $yy_anchor = YY_NO_ANCHOR;
        $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
        $yy_next_state = YY_NO_STATE;
         $yy_last_accept_state = YY_NO_STATE;
        $yy_initial = true;
        $yy_this_accept = 0;
        
        $this->yy_mark_start();
        $yy_this_accept = $this->yy_acpt[$yy_state];
        if (YY_NOT_ACCEPT != $yy_this_accept) {
            $yy_last_accept_state = $yy_state;
            $this->yy_buffer_end = $this->yy_buffer_index;
        }
        while (true) {
            if ($yy_initial && $this->yy_at_bol) {
                $yy_lookahead =  YY_BOL;
            } else {
                $yy_lookahead = $this->yy_advance();
            }
            $yy_next_state = $this->yy_nxt[$this->yy_rmap[$yy_state]][$this->yy_cmap[$yy_lookahead]];
            if (YY_EOF == $yy_lookahead && $yy_initial) {
                return false;            }
            if (YY_F != $yy_next_state) {
                $yy_state = $yy_next_state;
                $yy_initial = false;
                $yy_this_accept = $this->yy_acpt[$yy_state];
                if (YY_NOT_ACCEPT != $yy_this_accept) {
                    $yy_last_accept_state = $yy_state;
                    $this->yy_buffer_end = $this->yy_buffer_index;
                }
            } else {
                if (YY_NO_STATE == $yy_last_accept_state) {
                    $this->yy_error(1,1);
                } else {
                    $yy_anchor = $this->yy_acpt[$yy_last_accept_state];
                    if (0 != (YY_END & $yy_anchor)) {
                        $this->yy_move_end();
                    }
                    $this->yy_to_mark();
                    if ($yy_last_accept_state < 0) {
                       if ($yy_last_accept_state < 64) {
                           $this->yy_error(YY_E_INTERNAL, false);
                       }
                    } else {

                        switch ($yy_last_accept_state) {
case 2:
{
    if ($this->debug) echo 'new tag ['.$this->yytext()."]\n";
    $this->yybegin(INTAG);
    return array(FILE_CHESSPGN_TAGOPEN, $this->yytext());
}
case 3:
{
    $this->yybegin(INMOVES);
    if ($this->yytext() == '(') {
        if ($this->debug) echo '->found rav ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_RAVOPEN, $this->yytext());
    } elseif (is_numeric($this->yytext())) {
        if ($this->debug) echo '->found digit ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_DIGIT, $this->yytext());
    }
}
case 4:
{
    if ($this->debug) echo 'new comment ['.$this->yytext()."]\n";
    $this->_lastState = $this->yy_lexical_state;
    $this->yybegin(INCOMMENT);
    return array(FILE_CHESSPGN_COMMENTOPEN, $this->yytext());
}
case 5:
{
    break;
}
case 6:
{
    $this->yybegin(INMOVES);
    if ($this->debug) echo '->found pawn move ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_PAWNMOVE, $this->yytext());
}
case 7:
{
    $this->yybegin(INMOVES);
    if ($this->debug) echo '->found piece move ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_PIECEMOVE, $this->yytext());
}
case 8:
{
    // end of game
    $this->yybegin(YYINITIAL);
    if ($this->debug) echo 'found game end ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_GAMEEND, $this->yytext());
}
case 9:
{
    $this->yybegin(INMOVES);
    if ($this->debug) echo 'found castle move ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_CASTLE, $this->yytext());
}
case 10:
{
    $this->yybegin(INMOVES);
    if ($this->debug) echo '->found placement move ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_PLACEMENTMOVE, $this->yytext());
}
case 11:
{
    // end of game
    $this->yybegin(YYINITIAL);
    if ($this->debug) echo 'found game end ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_GAMEEND, $this->yytext());
}
case 12:
{
    // end of game
    $this->yybegin(YYINITIAL);
    if ($this->debug) echo 'found unfinished game indicator ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_GAMEEND, $this->yytext());
}
case 13:
{
    if ($this->debug) echo 'found period ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_PERIOD, $this->yytext());
}
case 14:
{
    if ($this->debug) echo 'found check ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_CHECK, $this->yytext());
}
case 15:
{
    if ($this->debug) echo 'found mate ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_MATE, $this->yytext());
}
case 16:
{
    if ($this->debug) echo 'found move annotation ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_MOVEANNOT, $this->yytext());
}
case 17:
{
    if ($this->debug) echo 'found recursive annotation variation close ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_RAVCLOSE, $this->yytext());
}
case 18:
{
    if ($this->debug) echo 'found numeric annotation glyph ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_NAG, $this->yytext());
}
case 19:
{
    if ($this->debug) echo 'tag contents ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_TAGNAME, $this->yytext());
}
case 20:
{
    break;
}
case 21:
{
    if ($this->debug) echo "ending tag [".$this->yytext()."]\n";
    $this->yybegin(YYINITIAL);
    return array(FILE_CHESSPGN_TAGCLOSE, $this->yytext());
}
case 22:
{
    if ($this->debug) echo "starting string\n";
    $this->yybegin(INSTRING);
    $this->_string = '';
    break;
}
case 23:
{
    if ($this->debug) echo "added to string [".$this->yytext()."]\n";
    $this->_string .= $this->yytext();
    break;
}
case 24:
{
    if ($this->debug) echo "returning string [$this->_string]\n";
    $this->yybegin(INTAG);
    $res = array(FILE_CHESSPGN_STRING, $this->_string);
    $this->_string = '';
    return $res;
}
case 25:
{
    if ($this->debug) echo "string escape [\\]\n";
    $this->yybegin(INESCAPE);
    break;
}
case 26:
{
    if ($this->debug) echo "non-escape [".$this->yytext()."]\n";
    $this->yybegin(INSTRING);
    $this->_string .= $this->yytext();
    break;
}
case 27:
{
    if ($this->debug) echo "escape [".$this->yytext()."]\n";
    $this->yybegin(INSTRING);
    $this->_string .= $this->yytext();
    break;
}
case 28:
{
    if ($this->debug) echo 'comment contents ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_COMMENT, $this->yytext());
}
case 29:
{
    if ($this->debug) echo 'close comment ['.$this->yytext()."]\n";
    $this->yybegin($this->_lastState);
    return array(FILE_CHESSPGN_COMMENTCLOSE, $this->yytext());
}
case 31:
{
    $this->yybegin(INMOVES);
    if ($this->yytext() == '(') {
        if ($this->debug) echo '->found rav ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_RAVOPEN, $this->yytext());
    } elseif (is_numeric($this->yytext())) {
        if ($this->debug) echo '->found digit ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_DIGIT, $this->yytext());
    }
}
case 32:
{
    $this->yybegin(INMOVES);
    if ($this->debug) echo 'found castle move ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_CASTLE, $this->yytext());
}
case 33:
{
    if ($this->debug) echo 'found move annotation ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_MOVEANNOT, $this->yytext());
}
case 34:
{
    if ($this->debug) echo 'tag contents ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_TAGNAME, $this->yytext());
}
case 35:
{
    if ($this->debug) echo 'comment contents ['.$this->yytext()."]\n";
    return array(FILE_CHESSPGN_COMMENT, $this->yytext());
}
case 37:
{
    $this->yybegin(INMOVES);
    if ($this->yytext() == '(') {
        if ($this->debug) echo '->found rav ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_RAVOPEN, $this->yytext());
    } elseif (is_numeric($this->yytext())) {
        if ($this->debug) echo '->found digit ['.$this->yytext()."]\n";
        return array(FILE_CHESSPGN_DIGIT, $this->yytext());
    }
}

                        }
                    }
                    $yy_initial = true;
                    $yy_state = $this->yy_state_dtrans[$this->yy_lexical_state];
                    $yy_next_state = YY_NO_STATE;
                    $yy_last_accept_state = YY_NO_STATE;
                    $this->yy_mark_start();
                    $yy_this_accept = $this->yy_acpt[$yy_state];
                    if (YY_NOT_ACCEPT != $yy_this_accept) {
                        $yy_last_accept_state = $yy_state;
                        $this->yy_buffer_end = $this->yy_buffer_index;
                    }
                }
            }
        }
        return null;
    }
}
