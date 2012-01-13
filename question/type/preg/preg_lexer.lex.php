<?php # vim:ft=php
require_once($CFG->dirroot . '/question/type/preg/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');


class Yylex extends JLexBase  {
    const YY_BUFFER_SIZE = 512;
    const YY_F = -1;
    const YY_NO_STATE = -1;
    const YY_NOT_ACCEPT = 0;
    const YY_START = 1;
    const YY_END = 2;
    const YY_NO_ANCHOR = 4;
    const YY_BOL = 128;
    var $YY_EOF = 129;

    protected $errors;
    protected $maxsubpatt;
    protected $optstack;
    protected $optcount;
    //A reference to the matcher object to be passed to some nodes
    public $matcher = null;
    //Global modifiers as a string - defined for entire expression
    public $globalmodifiers = '';
    //Local modifiers - turned on (or off) using options in the expression
    //It's contains copy of a global modifiers at start, but could be changed later
    public $localmodifiers ='';
    public function get_errors() {
        return $this->errors;
    }
    public function get_max_subpattern() {
        return $this->maxsubpatt;
    }
    protected function form_node($name, $subtype = null, $data = null, $leftborder = null, $rightborder = null, $greed = true) {
        $result = new $name;
        if ($subtype !== null) {
            $result->subtype = $subtype;
        }
        //set i modifier for leafs
        if (is_a($result, 'preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->i) {
            $result->caseinsensitive = true;
        }
        switch($name) {
        case 'preg_leaf_charset':
            $result->charset = $data;
            break;
        case 'preg_leaf_backref':
            $result->number = $data;
            break;
        case 'preg_node_finite_quant':
            $result->rightborder = $rightborder;
        case 'preg_node_infinite_quant':
            $result->greed = $greed;
            $result->leftborder = $leftborder;
            break;
        case 'preg_leaf_option':
            $text = substr($data, 2, strlen($data) - 3);
            $index = strpos($text, '-');
            if ($index === false) {
                $result->posopt = $text;
            } else {
                $result->posopt = substr($text, 0, $index);
                $result->negopt = substr($text, $index + 1);
            }
            break;
        case 'preg_leaf_recursion':
            if ($data[2] == 'R') {
                $result->number = 0;
            } else {
                $result->number = substr($data, 2, strlen($data) - 3);
            }
            break;
        }
        $result->indfirst = $this->yychar;
        $text = $this->yytext();
        $result->indlast = $this->yychar + $this->yylength() - 1;
        return $result;
    }
    protected function form_res($type, $value) {
        $result->type = $type;
        $result->value = $value;
        return $result;
    }
    protected function form_num_interval(&$cc, $startchar, $endchar) {
        if(ord($startchar) < ord($endchar)) {
            $char = ord($startchar);
            while($char <= ord($endchar)) {
                $cc->charset .= chr($char);
                $char++;
            }
        } else {
            $cc->error = 1;
        }
    }
    protected function push_opt_lvl() {
        if ($this->optcount > 0) {
            $this->optstack[$this->optcount] = clone $this->optstack[$this->optcount - 1];
            $this->optcount++;
        } /*else
            error will be found in parser, lexer do nothing for this error (close unopened bracket)*/
    }
    protected function pop_opt_lvl() {
        if ($this->optcount > 0)
            $this->optcount--;
    }
    public function mod_top_opt($set, $unset) {
        for ($i = 0; $i < strlen($set); $i++) {
            if (strpos($unset, $set[$i])) {//set and unset modifier at the same time is error
                $text = $this->yytext;
                $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar - strlen($text), $this->yychar - 1);
                return;
            }
        }
        //if error does not exist, set and unset local modifier
        for ($i = 0; $i < strlen($set); $i++) {
            $this->optstack[$this->optcount - 1]->$set[$i] = true;
        }
        for ($i = 0; $i < strlen($unset); $i++) {
            $this->optstack[$this->optcount - 1]->$unset[$i] = false;
        }
    }
    protected $yy_count_chars = true;

    function __construct($stream) {
        parent::__construct($stream);
        $this->yy_lexical_state = self::YYINITIAL;

    $this->errors = array();
    $this->maxsubpatt = 0;
    $this->optstack = array();
    $this->optstack[0] = new stdClass;
    //set all modifier's fields to false, it must be set to correct values before initializing lexer and doing lexical anilysis
    $this->optstack[0]->i = false;
    $this->optcount = 1;
    }

    private function yy_do_eof () {
        if (false === $this->yy_eof_done) {

        if (isset($this->cc) && is_object($this->cc)) {//End of expression inside character class
            $this->errors[] = new preg_lexem (preg_node_error::SUBTYPE_UNCLOSED_CHARCLASS, $this->cc->indfirst, $this->yychar - 1);
            $this->cc = null;
        }
        }
        $this->yy_eof_done = true;
    }
    const YYINITIAL = 0;
    const CHARCLASS = 1;
    static $yy_state_dtrans = array(
        0,
        119
    );
    static $yy_acpt = array(
        /* 0 */ self::YY_NOT_ACCEPT,
        /* 1 */ self::YY_NO_ANCHOR,
        /* 2 */ self::YY_NO_ANCHOR,
        /* 3 */ self::YY_NO_ANCHOR,
        /* 4 */ self::YY_NO_ANCHOR,
        /* 5 */ self::YY_NO_ANCHOR,
        /* 6 */ self::YY_NO_ANCHOR,
        /* 7 */ self::YY_NO_ANCHOR,
        /* 8 */ self::YY_NO_ANCHOR,
        /* 9 */ self::YY_NO_ANCHOR,
        /* 10 */ self::YY_NO_ANCHOR,
        /* 11 */ self::YY_NO_ANCHOR,
        /* 12 */ self::YY_NO_ANCHOR,
        /* 13 */ self::YY_NO_ANCHOR,
        /* 14 */ self::YY_NO_ANCHOR,
        /* 15 */ self::YY_NO_ANCHOR,
        /* 16 */ self::YY_NO_ANCHOR,
        /* 17 */ self::YY_NO_ANCHOR,
        /* 18 */ self::YY_NO_ANCHOR,
        /* 19 */ self::YY_NO_ANCHOR,
        /* 20 */ self::YY_NO_ANCHOR,
        /* 21 */ self::YY_NO_ANCHOR,
        /* 22 */ self::YY_NO_ANCHOR,
        /* 23 */ self::YY_NO_ANCHOR,
        /* 24 */ self::YY_NO_ANCHOR,
        /* 25 */ self::YY_NO_ANCHOR,
        /* 26 */ self::YY_NO_ANCHOR,
        /* 27 */ self::YY_NO_ANCHOR,
        /* 28 */ self::YY_NO_ANCHOR,
        /* 29 */ self::YY_NO_ANCHOR,
        /* 30 */ self::YY_NO_ANCHOR,
        /* 31 */ self::YY_NO_ANCHOR,
        /* 32 */ self::YY_NO_ANCHOR,
        /* 33 */ self::YY_NO_ANCHOR,
        /* 34 */ self::YY_NO_ANCHOR,
        /* 35 */ self::YY_NO_ANCHOR,
        /* 36 */ self::YY_NO_ANCHOR,
        /* 37 */ self::YY_NO_ANCHOR,
        /* 38 */ self::YY_NO_ANCHOR,
        /* 39 */ self::YY_NO_ANCHOR,
        /* 40 */ self::YY_NO_ANCHOR,
        /* 41 */ self::YY_NO_ANCHOR,
        /* 42 */ self::YY_NO_ANCHOR,
        /* 43 */ self::YY_NO_ANCHOR,
        /* 44 */ self::YY_NO_ANCHOR,
        /* 45 */ self::YY_NO_ANCHOR,
        /* 46 */ self::YY_NO_ANCHOR,
        /* 47 */ self::YY_NO_ANCHOR,
        /* 48 */ self::YY_NO_ANCHOR,
        /* 49 */ self::YY_NO_ANCHOR,
        /* 50 */ self::YY_NO_ANCHOR,
        /* 51 */ self::YY_NO_ANCHOR,
        /* 52 */ self::YY_NO_ANCHOR,
        /* 53 */ self::YY_NO_ANCHOR,
        /* 54 */ self::YY_NO_ANCHOR,
        /* 55 */ self::YY_NO_ANCHOR,
        /* 56 */ self::YY_NO_ANCHOR,
        /* 57 */ self::YY_NO_ANCHOR,
        /* 58 */ self::YY_NO_ANCHOR,
        /* 59 */ self::YY_NO_ANCHOR,
        /* 60 */ self::YY_NO_ANCHOR,
        /* 61 */ self::YY_NO_ANCHOR,
        /* 62 */ self::YY_NO_ANCHOR,
        /* 63 */ self::YY_NO_ANCHOR,
        /* 64 */ self::YY_NO_ANCHOR,
        /* 65 */ self::YY_NO_ANCHOR,
        /* 66 */ self::YY_NO_ANCHOR,
        /* 67 */ self::YY_NO_ANCHOR,
        /* 68 */ self::YY_NO_ANCHOR,
        /* 69 */ self::YY_NO_ANCHOR,
        /* 70 */ self::YY_NO_ANCHOR,
        /* 71 */ self::YY_NO_ANCHOR,
        /* 72 */ self::YY_NO_ANCHOR,
        /* 73 */ self::YY_NO_ANCHOR,
        /* 74 */ self::YY_NO_ANCHOR,
        /* 75 */ self::YY_NO_ANCHOR,
        /* 76 */ self::YY_NO_ANCHOR,
        /* 77 */ self::YY_NO_ANCHOR,
        /* 78 */ self::YY_NO_ANCHOR,
        /* 79 */ self::YY_NOT_ACCEPT,
        /* 80 */ self::YY_NO_ANCHOR,
        /* 81 */ self::YY_NO_ANCHOR,
        /* 82 */ self::YY_NO_ANCHOR,
        /* 83 */ self::YY_NO_ANCHOR,
        /* 84 */ self::YY_NO_ANCHOR,
        /* 85 */ self::YY_NOT_ACCEPT,
        /* 86 */ self::YY_NO_ANCHOR,
        /* 87 */ self::YY_NO_ANCHOR,
        /* 88 */ self::YY_NOT_ACCEPT,
        /* 89 */ self::YY_NOT_ACCEPT,
        /* 90 */ self::YY_NOT_ACCEPT,
        /* 91 */ self::YY_NOT_ACCEPT,
        /* 92 */ self::YY_NOT_ACCEPT,
        /* 93 */ self::YY_NOT_ACCEPT,
        /* 94 */ self::YY_NOT_ACCEPT,
        /* 95 */ self::YY_NOT_ACCEPT,
        /* 96 */ self::YY_NOT_ACCEPT,
        /* 97 */ self::YY_NOT_ACCEPT,
        /* 98 */ self::YY_NOT_ACCEPT,
        /* 99 */ self::YY_NOT_ACCEPT,
        /* 100 */ self::YY_NOT_ACCEPT,
        /* 101 */ self::YY_NOT_ACCEPT,
        /* 102 */ self::YY_NOT_ACCEPT,
        /* 103 */ self::YY_NOT_ACCEPT,
        /* 104 */ self::YY_NOT_ACCEPT,
        /* 105 */ self::YY_NOT_ACCEPT,
        /* 106 */ self::YY_NOT_ACCEPT,
        /* 107 */ self::YY_NOT_ACCEPT,
        /* 108 */ self::YY_NOT_ACCEPT,
        /* 109 */ self::YY_NOT_ACCEPT,
        /* 110 */ self::YY_NOT_ACCEPT,
        /* 111 */ self::YY_NOT_ACCEPT,
        /* 112 */ self::YY_NOT_ACCEPT,
        /* 113 */ self::YY_NOT_ACCEPT,
        /* 114 */ self::YY_NOT_ACCEPT,
        /* 115 */ self::YY_NOT_ACCEPT,
        /* 116 */ self::YY_NOT_ACCEPT,
        /* 117 */ self::YY_NOT_ACCEPT,
        /* 118 */ self::YY_NOT_ACCEPT,
        /* 119 */ self::YY_NOT_ACCEPT,
        /* 120 */ self::YY_NOT_ACCEPT,
        /* 121 */ self::YY_NOT_ACCEPT,
        /* 122 */ self::YY_NOT_ACCEPT,
        /* 123 */ self::YY_NOT_ACCEPT,
        /* 124 */ self::YY_NOT_ACCEPT,
        /* 125 */ self::YY_NOT_ACCEPT,
        /* 126 */ self::YY_NOT_ACCEPT,
        /* 127 */ self::YY_NOT_ACCEPT,
        /* 128 */ self::YY_NO_ANCHOR,
        /* 129 */ self::YY_NO_ANCHOR,
        /* 130 */ self::YY_NO_ANCHOR,
        /* 131 */ self::YY_NOT_ACCEPT,
        /* 132 */ self::YY_NOT_ACCEPT,
        /* 133 */ self::YY_NOT_ACCEPT
    );
        static $yy_cmap = array(
 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17,
 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 14, 17, 17, 41, 17, 17, 26,
 9, 10, 2, 3, 6, 23, 16, 17, 5, 28, 28, 28, 28, 28, 28, 28, 21, 21, 12, 17,
 15, 13, 11, 1, 17, 30, 32, 30, 34, 30, 30, 24, 24, 24, 24, 24, 24, 24, 24, 24,
 27, 24, 43, 38, 24, 24, 24, 36, 24, 24, 24, 8, 19, 20, 40, 44, 17, 45, 31, 45,
 33, 45, 45, 22, 46, 42, 46, 25, 46, 46, 46, 46, 46, 46, 46, 37, 39, 46, 46, 35,
 29, 46, 46, 4, 18, 7, 17, 17, 0, 0,);

        static $yy_rmap = array(
 0, 1, 2, 3, 4, 1, 1, 5, 1, 1, 1, 1, 1, 1, 1, 1, 1, 6, 1, 1,
 7, 8, 1, 1, 1, 1, 1, 1, 1, 1, 1, 9, 1, 1, 1, 1, 10, 11, 1, 12,
 1, 1, 1, 1, 1, 1, 13, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
 1, 1, 1, 1, 1, 14, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 15,
 1, 1, 16, 1, 17, 18, 1, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31,
 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51,
 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65,);

        static $yy_nxt = array(
array(
 1, 2, 3, 4, 79, 5, 5, -1, 6, 7, 8, 5, 5, 5, 5, 5, 9, 5, 10, 85,
 -1, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5,
 11, 12, 5, 5, 5, 5, 5,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 13, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 14, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 15, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 90, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 128, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, 128, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 129, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 129, -1, -1, -1, -1, -1, -1, 129, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 104, 82, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 82, -1, -1, -1, -1, -1, -1, 82, -1, 82, 82, 82, 82, 82, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, 82, -1,
),
array(
 -1, 38, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 83, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 83, -1, -1, -1, -1, -1, -1, 83, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 47, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 48, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 58, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 75, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 88, 89, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 88, -1, -1, -1, -1, -1, -1, 88, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 86, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 86, -1, -1, -1, -1, -1, -1, 86, -1, 86, 86, 86, 86, 86, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, 86, -1,
),
array(
 -1, -1, -1, -1, -1, 121, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 121, -1, 122, -1, -1, -1, -1, 121, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 16, 16, 16, 16, 17, 18, 16, 16, 16, 16, 18, 18, 18, 18, 18, 16, 18, 16, 19,
 16, 20, 91, 18, -1, 92, 18, -1, 20, 21, -1, 22, 23, 24, 25, 26, 27, 28, 29, 30,
 18, 18, -1, -1, 18, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 125, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 88, 93, 31, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 88, -1, -1, -1, -1, -1, -1, 88, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 94, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 94, -1, -1, -1, -1, -1, -1, 94, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 95, -1, -1, -1, 96, -1, 32, 33, 34, 35, 97, -1, -1, -1, -1,
 -1, 95, -1, 98, -1, -1, -1, 99, 95, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, 100, 101, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 102, 36, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 36, -1, -1, -1, -1, -1, -1, 36, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, 103, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 131, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, 132, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 105, -1, 37, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 105, -1, -1, -1, -1, -1, -1, 105, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 94, -1, 39, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 94, -1, -1, -1, -1, -1, -1, 94, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 95, -1, -1, -1, -1, 40, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 95, -1, -1, -1, -1, -1, -1, 95, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, 106, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 41, 42, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, 107, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 133, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 43, -1, 44, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 40, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 108, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 108, 109, 110, 109, 109, -1, 109, 108, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109,
 -1, -1, 109, 109, 109, 109, 109,
),
array(
 -1, -1, -1, -1, -1, 111, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 111, 111, -1, 111, 111, -1, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111,
 -1, -1, 111, 111, 111, 111, 111,
),
array(
 -1, -1, -1, -1, -1, 104, -1, 45, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 104, -1, -1, -1, -1, -1, -1, 104, -1, 104, 104, 104, 104, 104, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, 104, -1,
),
array(
 -1, -1, -1, -1, -1, 105, -1, 46, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 105, -1, -1, -1, -1, -1, -1, 105, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 49, 50, 114, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 51, -1, 52, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 116, -1, 53, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 116, 109, -1, 109, 109, -1, 109, 116, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109,
 -1, -1, 109, 109, 109, 109, 109,
),
array(
 -1, -1, -1, -1, -1, 109, -1, 54, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 109, 109, -1, 109, 109, -1, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109,
 -1, -1, 109, 109, 109, 109, 109,
),
array(
 -1, -1, -1, -1, -1, 117, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 117, -1, -1, -1, -1, -1, -1, 117, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 111, -1, 55, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 111, 111, -1, 111, 111, -1, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111, 111,
 -1, -1, 111, 111, 111, 111, 111,
),
array(
 -1, -1, -1, -1, -1, 112, -1, -1, -1, -1, -1, 56, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 112, 112, -1, 112, 112, -1, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112,
 -1, -1, 112, 112, 112, 112, 112,
),
array(
 -1, -1, -1, -1, -1, 113, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 113, 113, -1, 113, 113, 57, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113,
 -1, -1, 113, 113, 113, 113, 113,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 59, 60, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 115, -1, -1, -1, -1, 61, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 115, 115, -1, 115, 115, -1, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115,
 -1, -1, 115, 115, 115, 115, 115,
),
array(
 -1, -1, -1, -1, -1, 109, -1, 53, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 109, 109, -1, 109, 109, -1, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109, 109,
 -1, -1, 109, 109, 109, 109, 109,
),
array(
 -1, -1, -1, -1, -1, 118, -1, 53, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 118, -1, -1, -1, -1, -1, -1, 118, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, 53, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 1, 62, 62, 62, 62, 84, 62, 62, -1, 62, 62, 62, 62, 62, 62, 62, 62, 62, 62, 120,
 63, 84, 87, 64, 130, 87, 62, 130, 84, 87, 130, 87, 130, 87, 130, 87, 130, 87, 130, 87,
 65, 62, 87, 130, 62, 87, 87,
),
array(
 -1, -1, -1, -1, -1, 123, -1, -1, 66, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 67,
 68, -1, -1, 69, -1, -1, -1, -1, -1, 124, -1, -1, -1, 70, -1, 71, 72, 73, -1, 74,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 76, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 76, -1, -1, -1, -1, -1, -1, 76, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 77, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 77, -1, -1, -1, -1, -1, -1, 77, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 121, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 121, -1, -1, -1, -1, -1, -1, 121, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 127, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 127, -1, -1, -1, -1, -1, -1, 127, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, 77, -1, -1, 77, -1, -1, -1, 77, -1, 77, -1, 77, -1, 77, -1, 77, -1, 77,
 -1, -1, 77, -1, -1, 77, 77,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, 77, -1, -1, 77, -1, -1, 77, -1, 77, -1, 77, -1, 77, -1, 77, -1,
 -1, -1, -1, 77, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 78, -1, -1, -1, -1, -1, -1, 78, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 80, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1, -1, 80, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 81, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 81, -1, -1, -1, -1, -1, -1, 81, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, 126, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, -1, -1, -1, -1, -1, -1,
),
array(
 -1, -1, -1, -1, -1, 112, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 112, 112, -1, 112, 112, -1, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112, 112,
 -1, -1, 112, 112, 112, 112, 112,
),
array(
 -1, -1, -1, -1, -1, 113, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 113, 113, -1, 113, 113, -1, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113, 113,
 -1, -1, 113, 113, 113, 113, 113,
),
array(
 -1, -1, -1, -1, -1, 115, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
 -1, 115, 115, -1, 115, 115, -1, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115, 115,
 -1, -1, 115, 115, 115, 115, 115,
),
);

    public function /*Yytoken*/ nextToken ()
 {
        $yy_anchor = self::YY_NO_ANCHOR;
        $yy_state = self::$yy_state_dtrans[$this->yy_lexical_state];
        $yy_next_state = self::YY_NO_STATE;
        $yy_last_accept_state = self::YY_NO_STATE;
        $yy_initial = true;

        $this->yy_mark_start();
        $yy_this_accept = self::$yy_acpt[$yy_state];
        if (self::YY_NOT_ACCEPT != $yy_this_accept) {
            $yy_last_accept_state = $yy_state;
            $this->yy_mark_end();
        }
        while (true) {
            if ($yy_initial && $this->yy_at_bol) $yy_lookahead = self::YY_BOL;
            else $yy_lookahead = $this->yy_advance();
            $yy_next_state = self::$yy_nxt[self::$yy_rmap[$yy_state]][self::$yy_cmap[$yy_lookahead]];
            if ($this->YY_EOF == $yy_lookahead && true == $yy_initial) {
                $this->yy_do_eof();
                return null;
            }
            if (self::YY_F != $yy_next_state) {
                $yy_state = $yy_next_state;
                $yy_initial = false;
                $yy_this_accept = self::$yy_acpt[$yy_state];
                if (self::YY_NOT_ACCEPT != $yy_this_accept) {
                    $yy_last_accept_state = $yy_state;
                    $this->yy_mark_end();
                }
            }
            else {
                if (self::YY_NO_STATE == $yy_last_accept_state) {
                    throw new Exception("Lexical Error: Unmatched Input.");
                }
                else {
                    $yy_anchor = self::$yy_acpt[$yy_last_accept_state];
                    if (0 != (self::YY_END & $yy_anchor)) {
                        $this->yy_move_end();
                    }
                    $this->yy_to_mark();
                    switch ($yy_last_accept_state) {
                        case 1:

                        case -2:
                            break;
                        case 2:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1));
    return $res;
}
                        case -3:
                            break;
                        case 3:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0));
    return $res;
}
                        case -4:
                            break;
                        case 4:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1));
    return $res;
}
                        case -5:
                            break;
                        case 5:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $this->yytext()));
    return $res;
}
                        case -6:
                            break;
                        case 6:
                            {
    $this->cc = new preg_leaf_charset;
    $this->cc->negative = false;
    $this->cccharnumber = 0;
    $this->cc->indfirst = $this->yychar;
    $this->yybegin(self::CHARCLASS);
}
                        case -7:
                            break;
                        case 7:
                            {
    $this->push_opt_lvl();
    $this->maxsubpatt++;
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->maxsubpatt));
    return $res;
}
                        case -8:
                            break;
                        case 8:
                            {
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, new preg_lexem(0, $this->yychar, $this->yychar));
    return $res;
}
                        case -9:
                            break;
                        case 9:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_DOT));
    return $res;
}
                        case -10:
                            break;
                        case 10:
                            {
    $res = $this->form_res(preg_parser_yyParser::ALT, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -11:
                            break;
                        case 11:
                            {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $leaf);
    return $res;
}
                        case -12:
                            break;
                        case 12:
                            {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
                        case -13:
                            break;
                        case 13:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1, false));
    return $res;
}
                        case -14:
                            break;
                        case 14:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0, null, false));
    return $res;
}
                        case -15:
                            break;
                        case 15:
                            {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1, null, false));
    return $res;
}
                        case -16:
                            break;
                        case 16:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $text[1]));
    return $res;
}
                        case -17:
                            break;
                        case 17:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
                        case -18:
                            break;
                        case 18:
                            {
    $text = $this->yytext();
    $leaf = $this->form_node('preg_leaf_charset', null, $text[1]);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
                        case -19:
                            break;
                        case 19:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '\\'));
    return $res;
}
                        case -20:
                            break;
                        case 20:
                            {
    $numstr = substr($this->yytext(), 1);
    $numdec = intval($numstr, 10);
    if ($numdec < 10 || ($numdec <= $this->maxsubpatt && $numdec < 100)) {
        // return a backreference
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $numstr));
        $res->value->matcher =& $this->matcher;
    } else {
        // return a character
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < strlen($numstr); $i++) {
            if (intval($numstr[$i]) < 8) {
                $octal = $octal . $numstr[$i];
            } else {
                $failed = true;
            }
        }
        if (strlen($octal) == 0) {    // if no octal digits found, it should be 0
            $octal = '0';
            $tail = $numstr;
        } else {                                        // octal digits found
            $tail = substr($numstr, strlen($octal));
        }
        // return a single lexem if all digits are octal, an array of lexems otherwise
        if (strlen($tail) == 0) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
        } else {
            $res = array();
            $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
            for ($i = 0; $i < strlen($tail); $i++) {
                $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $tail[$i]));
            }
        }
    }
    return $res;
}
                        case -21:
                            break;
                        case 21:
                            {
    $code = 0;
    $str = $this->yytext();
    if (strlen($str) > 1) {
        $code = hexdec(substr($str, 1));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr($code)));
    return $res;
}
                        case -22:
                            break;
                        case 22:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    return $res;
}
                        case -23:
                            break;
                        case 23:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    $res->value->negative = true;
    return $res;
}
                        case -24:
                            break;
                        case 24:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '0123456789'));
    return $res;
}
                        case -25:
                            break;
                        case 25:
                            {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, '0123456789');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
                        case -26:
                            break;
                        case 26:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR));
    return $res;
}
                        case -27:
                            break;
                        case 27:
                            {
    $PARSLEAF = $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR);
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
                        case -28:
                            break;
                        case 28:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, ' '));
    return $res;
}
                        case -29:
                            break;
                        case 29:
                            {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, ' ');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
                        case -30:
                            break;
                        case 30:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(9)));
    return $res;
}
                        case -31:
                            break;
                        case 31:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') - 1), substr($text, 1, strpos($text, ',') - 1)));
    return $res;
}
                        case -32:
                            break;
                        case 32:
                            {
    $this->push_opt_lvl();
    $this->maxsubpatt++;
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->maxsubpatt));
    return $res;
}
                        case -33:
                            break;
                        case 33:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -34:
                            break;
                        case 34:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -35:
                            break;
                        case 35:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -36:
                            break;
                        case 36:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, substr($this->yytext(), 2)));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -37:
                            break;
                        case 37:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') - 1)));
    return $res;
}
                        case -38:
                            break;
                        case 38:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') - 1), substr($text, 1, strpos($text, ',') - 1), false));
    return $res;
}
                        case -39:
                            break;
                        case 39:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3)));
    return $res;
}
                        case -40:
                            break;
                        case 40:
                            {
    $text = $this->yytext();
    $leaf = $this->form_node('preg_leaf_recursion', null, $text);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
                        case -41:
                            break;
                        case 41:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -42:
                            break;
                        case 42:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -43:
                            break;
                        case 43:
                            {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt('i', '');
}
                        case -44:
                            break;
                        case 44:
                            {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt('i', '');
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -45:
                            break;
                        case 45:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $code = 0;
    if (strlen($str) > 1) {
        $code = hexdec($str);
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr($code)));
    return $res;
}
                        case -46:
                            break;
                        case 46:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') - 1), substr($text, strpos($text, ',') + 1, strlen($text) - 2 - strpos($text, ','))));
    return $res;
}
                        case -47:
                            break;
                        case 47:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') - 1), null, false));
    return $res;
}
                        case -48:
                            break;
                        case 48:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3), false));
    return $res;
}
                        case -49:
                            break;
                        case 49:
                            {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -50:
                            break;
                        case 50:
                            {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -51:
                            break;
                        case 51:
                            {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt('', 'i');
}
                        case -52:
                            break;
                        case 52:
                            {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt('', '-i');
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -53:
                            break;
                        case 53:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -54:
                            break;
                        case 54:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -55:
                            break;
                        case 55:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -56:
                            break;
                        case 56:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -57:
                            break;
                        case 57:
                            {
    $str = substr($this->yytext(), 3);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -58:
                            break;
                        case 58:
                            {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') - 1), substr($text, strpos($text, ',') + 1, strlen($text) - 2 - strpos($text, ',')), false));
    return $res;
}
                        case -59:
                            break;
                        case 59:
                            {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -60:
                            break;
                        case 60:
                            {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
                        case -61:
                            break;
                        case 61:
                            {
    $str = substr($this->yytext(), 4);
    $str = substr($str, 0, strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -62:
                            break;
                        case 62:
                            {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
                        case -63:
                            break;
                        case 63:
                            {
    $this->cc->indlast = $this->yychar;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    $this->cc = null;
    return $res;
}
                        case -64:
                            break;
                        case 64:
                            {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
    }
    $this->cccharnumber++;
}
                        case -65:
                            break;
                        case 65:
                            {
    if ($this->cccharnumber) {
        $this->cc->charset .= '^';
    } else {
        $this->cc->negative = true;
    }
    $this->cccharnumber++;
}
                        case -66:
                            break;
                        case 66:
                            {
    $this->cc->charset .= '[';
    $this->cccharnumber++;
}
                        case -67:
                            break;
                        case 67:
                            {
    $this->cc->charset .= '\\';
    $this->cccharnumber++;
}
                        case -68:
                            break;
                        case 68:
                            {
    $this->cc->charset .= ']';
    $this->cccharnumber++;
}
                        case -69:
                            break;
                        case 69:
                            {
    $this->cc->charset .= '-';
    $this->cccharnumber++;
}
                        case -70:
                            break;
                        case 70:
                            {
    $this->cccharnumber++;
    $this->cc->charset .= '0123456789';
}
                        case -71:
                            break;
                        case 71:
                            {
    $this->cc->w = true;
}
                        case -72:
                            break;
                        case 72:
                            {
    $this->cc->W = true;
}
                        case -73:
                            break;
                        case 73:
                            {
    $this->cccharnumber++;
    $this->cc->charset .= ' ';
}
                        case -74:
                            break;
                        case 74:
                            {
    $this->cccharnumber++;
    $this->cc->charset .= chr(9);
}
                        case -75:
                            break;
                        case 75:
                            {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
        $this->cc->negative = true;
        $this->cccharnumber++;
    }
}
                        case -76:
                            break;
                        case 76:
                            {
    $this->cc->charset .= chr(octdec(substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
                        case -77:
                            break;
                        case 77:
                            {
    $text = $this->yytext();
    $this->form_num_interval($this->cc, $text[0], $text[2]);
}
                        case -78:
                            break;
                        case 78:
                            {
    $this->cccharnumber++;
    $this->cc->charset .= chr(hexdec(substr($this->yytext(), 1)));
}
                        case -79:
                            break;
                        case 80:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
                        case -80:
                            break;
                        case 81:
                            {
    $numstr = substr($this->yytext(), 1);
    $numdec = intval($numstr, 10);
    if ($numdec < 10 || ($numdec <= $this->maxsubpatt && $numdec < 100)) {
        // return a backreference
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $numstr));
        $res->value->matcher =& $this->matcher;
    } else {
        // return a character
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < strlen($numstr); $i++) {
            if (intval($numstr[$i]) < 8) {
                $octal = $octal . $numstr[$i];
            } else {
                $failed = true;
            }
        }
        if (strlen($octal) == 0) {    // if no octal digits found, it should be 0
            $octal = '0';
            $tail = $numstr;
        } else {                                        // octal digits found
            $tail = substr($numstr, strlen($octal));
        }
        // return a single lexem if all digits are octal, an array of lexems otherwise
        if (strlen($tail) == 0) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
        } else {
            $res = array();
            $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
            for ($i = 0; $i < strlen($tail); $i++) {
                $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $tail[$i]));
            }
        }
    }
    return $res;
}
                        case -81:
                            break;
                        case 82:
                            {
    $code = 0;
    $str = $this->yytext();
    if (strlen($str) > 1) {
        $code = hexdec(substr($str, 1));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr($code)));
    return $res;
}
                        case -82:
                            break;
                        case 83:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, substr($this->yytext(), 2)));
    $res->value->matcher =& $this->matcher;
    return $res;
}
                        case -83:
                            break;
                        case 84:
                            {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
                        case -84:
                            break;
                        case 86:
                            {
    $code = 0;
    $str = $this->yytext();
    if (strlen($str) > 1) {
        $code = hexdec(substr($str, 1));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr($code)));
    return $res;
}
                        case -85:
                            break;
                        case 87:
                            {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
                        case -86:
                            break;
                        case 128:
                            {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
                        case -87:
                            break;
                        case 129:
                            {
    $numstr = substr($this->yytext(), 1);
    $numdec = intval($numstr, 10);
    if ($numdec < 10 || ($numdec <= $this->maxsubpatt && $numdec < 100)) {
        // return a backreference
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $numstr));
        $res->value->matcher =& $this->matcher;
    } else {
        // return a character
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < strlen($numstr); $i++) {
            if (intval($numstr[$i]) < 8) {
                $octal = $octal . $numstr[$i];
            } else {
                $failed = true;
            }
        }
        if (strlen($octal) == 0) {    // if no octal digits found, it should be 0
            $octal = '0';
            $tail = $numstr;
        } else {                                        // octal digits found
            $tail = substr($numstr, strlen($octal));
        }
        // return a single lexem if all digits are octal, an array of lexems otherwise
        if (strlen($tail) == 0) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
        } else {
            $res = array();
            $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec($octal))));
            for ($i = 0; $i < strlen($tail); $i++) {
                $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $tail[$i]));
            }
        }
    }
    return $res;
}
                        case -88:
                            break;
                        case 130:
                            {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
                        case -89:
                            break;
                        default:
                        $this->yy_error('INTERNAL',false);
                    case -1:
                    }
                    $yy_initial = true;
                    $yy_state = self::$yy_state_dtrans[$this->yy_lexical_state];
                    $yy_next_state = self::YY_NO_STATE;
                    $yy_last_accept_state = self::YY_NO_STATE;
                    $this->yy_mark_start();
                    $yy_this_accept = self::$yy_acpt[$yy_state];
                    if (self::YY_NOT_ACCEPT != $yy_this_accept) {
                        $yy_last_accept_state = $yy_state;
                        $this->yy_mark_end();
                    }
                }
            }
        }
    }
}
