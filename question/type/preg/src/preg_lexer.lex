<?php # vim:ft=php
require_once($CFG->dirroot . '/question/type/preg/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');

%%
%function nextToken
%char
%state CHARCLASS
%init{
    $this->optstack = array();
    $this->optstack[0] = new stdClass;
    //set false all modifiers' fields, it must be set to correct values before initializing lexer and lexical aniliz
    $this->optstack[0]->i = false;
    $this->optcount = 1;
%init}
%{
    protected $errors = array();
    protected $maxsubpatt = 0;
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

    protected function form_node($name, $subtype = null, $charclass = null, $leftborder = null, $rightborder = null, $greed = true) {
        $result = new $name;
        if ($subtype !== null) {
            $result->subtype = $subtype;
        }
        //set i modifier for leafs
        if (is_a($result, 'preg_leaf')) {
            /*old style local modifier handling
            if(strpos($this->localmodifiers,'i')!==false) {
                $result->caseinsensitive = true;
            }*/
            if ($this->optcount>0 && $this->optstack[$this->optcount-1]->i) {
                 $result->caseinsensitive = true;
            }
        }
        if ($name == 'preg_leaf_charset') {
            $result->charset = $charclass;
        } elseif ($name == 'preg_leaf_backref') {
            $result->number = $charclass;//TODO: rename $charclass argument, because it may be number of backref
        } elseif ($name == 'preg_node_finite_quant' || $name == 'preg_node_infinite_quant') {
            $result->greed = $greed;
            $result->leftborder = $leftborder;
            if ($name == 'preg_node_finite_quant') {
                $result->rightborder = $rightborder;
            }
        } elseif ($name == 'preg_leaf_option') {
            $text = substr($charclass, 2, strlen($charclass)-3);
            $index = strpos($text, '-');
            if ($index === false) {
                $result->posopt = $text;
            } else {
                $result->posopt = substr($text, 0, $index);
                $result->negopt = substr($text, $index+1);
            }
        } elseif ($name == 'preg_leaf_recursion') {
            if ($charclass[2]=='R') {
                $result->number=0;
            } else { 
                $result->number = substr($charclass, 2, strlen($charclass)-3); 
            }
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
            $this->optstack[$this->optcount] = clone $this->optstack[$this->optcount-1];
            $this->optcount++;
        } /*else 
            error will be found in parser, lexer do nothing for this error (close unopened bracket)*/
    }
    protected function pop_opt_lvl() {
        if ($this->optcount>0)
            $this->optcount--;
    }
    public function mod_top_opt($set, $unset) {
        for ($i=0; $i<strlen($set); $i++) {
            if (strpos($unset, $set[$i])) {//set and unset modifier at the same time is error
                $text = $this->yytext;
                $this->errors[] = new preg_lexem (preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar-strlen($text), $this->yychar-1);
                return;
            }
        }
        //if error not exist, set and unset local modifier
        for ($i=0; $i<strlen($set); $i++) {
            $this->optstack[$this->optcount-1]->$set[$i] = true;
        }
        for ($i=0; $i<strlen($unset); $i++) {
            $this->optstack[$this->optcount-1]->$unset[$i] = false;
        }
    }
%}
%eof{
        if (isset($this->cc) && is_object($this->cc)) {//End of expression inside character class
            $this->errors[] = new preg_lexem (preg_node_error::SUBTYPE_UNCLOSED_CHARCLASS, $this->cc->indfirst, $this->yychar-1);
            $this->cc = null;
        }
%eof}
%%

<YYINITIAL> \? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1));
    return $res;
}
<YYINITIAL> \* {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0));
    return $res;
}
<YYINITIAL> \+ {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1));
    return $res;
}
<YYINITIAL> \?\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, 1, false));
    return $res;
}
<YYINITIAL> \*\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 0, null, false));
    return $res;
}
<YYINITIAL> \+\? {
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, 1, null, false));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ','))));
    return $res;
}
<YYINITIAL> \{[0-9]+,\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
<YYINITIAL> \{,[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3)));
    return $res;
}
<YYINITIAL> \{[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1)));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, strpos($text, ',')+1, strlen($text)-2-strpos($text, ',')), false));
    return $res;
}
<YYINITIAL> \{[0-9]+,\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, substr($text, 1, strpos($text, ',') -1), null, false));
    return $res;
}
<YYINITIAL> \{,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, substr($text, 2, strlen($text) - 3), false));
    return $res;
}
<YYINITIAL> \{[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, substr($text, 1, strpos($text, ',') -1), substr($text, 1, strpos($text, ',') -1), false));
    return $res;
}
<YYINITIAL> \[ {
    $this->cc = new preg_leaf_charset;
    $this->cc->negative = false;
    $this->cccharnumber = 0;
    $this->cc->indfirst = $this->yychar;
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> \( {
    $this->push_opt_lvl();
    $this->maxsubpatt++;
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->maxsubpatt));
    return $res;
}
<YYINITIAL> \) {
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, new preg_lexem(0, $this->yychar, $this->yychar));
    return $res;
}
<YYINITIAL> \(\?> {
    $this->push_opt_lvl();
    $this->maxsubpatt++;
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->maxsubpatt));
    return $res;
}
<YYINITIAL> \(\?: {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\(\?= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\(\?! {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\(\?<= {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\(\?<! {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?! {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?<= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?<! {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \. {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_DOT));
    return $res;
}
<YYINITIAL> [^\[\]\\*+?{}()|.^$] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $this->yytext()));
    return $res;
}
<YYINITIAL> \| {
    $res = $this->form_res(preg_parser_yyParser::ALT, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \\[\[\]?*+{}|().] {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, $text[1]));
    return $res;
}
<YYINITIAL> \\[0-9][0-9]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, substr($this->yytext(), 1)));
    $res->value->matcher =& $this->matcher;
    return $res;
}
<YYINITIAL> \\0[0-9][0-9]?|[0-9][0-9][0-9] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(octdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\x[0-9][0-9] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(hexdec(substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\\\ {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '\\'));
    return $res;
}
<YYINITIAL> \\b {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    return $res;
}
<YYINITIAL> \\B {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    $res->value->negative = true;
    return $res;
}
<YYINITIAL> \\d {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, '0123456789'));
    return $res;
}
<YYINITIAL> \\D {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, '0123456789');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\w {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR));
    return $res;
}
<YYINITIAL> \\W {
    $PARSLEAF = $this->form_node('preg_leaf_meta', preg_leaf_meta::SUBTYPE_WORD_CHAR);
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\s {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, ' '));
    return $res;
}
<YYINITIAL> \\S {
    $PARSLEAF = $this->form_node('preg_leaf_charset', null, ' ');
    $PARSLEAF->negative = true;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\t {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', null, chr(9)));
    return $res;
}
<YYINITIAL> "^" {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $leaf);
    return $res;
}
<YYINITIAL> "$" {
    $leaf = $this->form_node('preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
<YYINITIAL> \(\?i\) {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt('i', '');
}
<YYINITIAL> \(\?-i\) {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt('', 'i');
}
<YYINITIAL> \(\?i: {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt('i', '');
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?-i: {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt('', '-i');
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?(R|[0-9]+)\) {
    $text = $this->yytext();
    $leaf = $this->form_node('preg_leaf_recursion', null, $text);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
<YYINITIAL> \\[^0-9a-zA-Z] {
    $text = $this->yytext();
    $leaf = $this->form_node('preg_leaf_charset', null, $text[1]);
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
<CHARCLASS> \\\\ {
    $this->cc->charset .= '\\';
    $this->cccharnumber++;
}
<CHARCLASS> \\\[ {
    $this->cc->charset .= '[';
    $this->cccharnumber++;
}
<CHARCLASS> \\\] {
    $this->cc->charset .= ']';
    $this->cccharnumber++;
}
<CHARCLASS> \\0[0-9][0-9]|[0-9][0-9][0-9] {
    $this->cc->charset .= chr(octdec(substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
<CHARCLASS> \\x[0-9][0-9] {
    $this->cccharnumber++;
    $this->cc->charset .= chr(hexdec(substr($this->yytext(), 1)));
}
<CHARCLASS> \\d {
    $this->cccharnumber++;
    $this->cc->charset .= '0123456789';
}
<CHARCLASS> \\w {
    $this->cc->w = true;
}
<CHARCLASS> \\W {
    $this->cc->W = true;
}
<CHARCLASS> \\s {
    $this->cccharnumber++;
    $this->cc->charset .= ' ';
}
<CHARCLASS> \\t {
    $this->cccharnumber++;
    $this->cc->charset .= chr(9);
}
<CHARCLASS> "^" {
    if ($this->cccharnumber) {
        $this->cc->charset .= '^';
    } else {
        $this->cc->negative = true;
    }
    $this->cccharnumber++;
}
<CHARCLASS> "^-" {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
        $this->cc->negative = true;
        $this->cccharnumber++;
    }
}
<CHARCLASS> - {
    if (!$this->cccharnumber) {
        $this->cc->charset .= '-';
    }
    $this->cccharnumber++;
}
<CHARCLASS> [0-9]-[0-9]|[a-z]-[a-z]|[A-Z]-[A-Z] {
    $text = $this->yytext();
    $this->form_num_interval($this->cc, $text[0], $text[2]);
}
<CHARCLASS> \\- {
    $this->cc->charset .= '-';
    $this->cccharnumber++;
}
<CHARCLASS> [^-\[\]\\^] {
    $this->cc->charset .= $this->yytext();
    $this->cccharnumber++;
}
<CHARCLASS> \] {
    $this->cc->indlast = $this->yychar;
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    $this->cc = null;
    return $res;
}