<?php # vim:ft=php
require_once($CFG->dirroot . '/question/type/preg/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

%%
%class qtype_preg_lexer
%function nextToken
%char
%unicode
%state CHARCLASS
%init{
    $this->errors = array();
    $this->lastsubpatt = 0;
    $this->maxsubpatt = 0;
    $this->subpatternmap = array();
    $this->lexemcount = 0;
    $this->backrefsexist = false;
    $this->optstack = array();
    $this->optstack[0] = new stdClass;
    // Set all modifier's fields to false, it must be set to correct values before initializing lexer and doing lexical analysis.
    $this->optstack[0]->i = false;
    $this->optstack[0]->subpattnum = -1;
    $this->optstack[0]->parennum = -1;
    $this->optcount = 1;
%init}
%{
    const CHARSET_DUMMY = 100;
    const CHARSET_d = 101;
    const CHARSET_w = 102;
    const CHARSET_s = 103;
    const CHARSET_D = 104;
    const CHARSET_W = 105;
    const CHARSET_S = 106;
    const CHARSET_DOT = 107;

    // A reference to the matcher object to be passed to some nodes.
    public $matcher = null;

    protected $errors;
    protected $lastsubpatt;
    protected $maxsubpatt;
    protected $subpatternmap;
    protected $lexemcount;
    protected $backrefsexist;
    protected $optstack;
    protected $optcount;

    public function get_errors() {
        return $this->errors;
    }

    public function get_max_subpattern() {
        return $this->maxsubpatt;
    }

    public function get_subpattern_map() {
        return $this->subpatternmap;
    }

    public function get_lexem_count() {
        return $this->lexemcount;
    }

    public function backrefs_exist() {
        return $this->backrefsexist;
    }

    protected function form_node($name, $subtype = null, $data = null, $leftborder = null, $rightborder = null, $greed = true) {
        $result = new $name;
        if ($subtype !== null) {
            $result->subtype = $subtype;
        }
        // Set i modifier for leafs.
        if (is_a($result, 'preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->i) {
            $result->caseinsensitive = true;
        }
        switch($name) {
        case 'preg_leaf_charset':
            $flag = new preg_charset_flag;
            if ($subtype === self::CHARSET_DUMMY) {
                $flag->set_set(new qtype_preg_string($data));
            } else {
                switch ($subtype) {
                case self::CHARSET_d:
                    $flag->set_flag(preg_charset_flag::DIGIT);
                    break;
                case self::CHARSET_w:
                    $flag->set_flag(preg_charset_flag::WORDCHAR);
                    break;
                case self::CHARSET_s:
                    $flag->set_flag(preg_charset_flag::SPACE);
                    break;
                case self::CHARSET_D:
                    $flag->set_flag(preg_charset_flag::DIGIT);
                    $flag->negative = true;
                    break;
                case self::CHARSET_W:
                    $flag->set_flag(preg_charset_flag::WORDCHAR);
                    $flag->negative = true;
                    break;
                case self::CHARSET_S:
                    $flag->set_flag(preg_charset_flag::SPACE);
                    $flag->negative = true;
                    break;
                case self::CHARSET_DOT:
                    $flag->set_flag(preg_charset_flag::PRIN);
                    break;
                default:
                    break;
                }
            }
            $result->flags = array(array($flag));
            $result->israngecalculated = false;
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
            $text = qtype_preg_unicode::substr($data, 2, qtype_preg_unicode::strlen($data) - 3);
            $index = qtype_preg_unicode::strpos($text, '-');
            if ($index === false) {
                $result->posopt = $text;
            } else {
                $result->posopt = new qtype_preg_string(qtype_preg_unicode::substr($text, 0, $index));
                $result->negopt = new qtype_preg_string(qtype_preg_unicode::substr($text, $index + 1));
            }
            break;
        case 'preg_leaf_recursion':
            if ($data[2] === 'R') {
                $result->number = 0;
            } else {
                $result->number = qtype_preg_unicode::substr($data, 2, qtype_preg_unicode::strlen($data) - 3);
            }
            break;
        }
        $result->indfirst = $this->yychar;
        $result->indlast = $this->yychar + $this->yylength() - 1;
        return $result;
    }

    protected function form_res($type, $value) {
        $result = new stdClass();
        $result->type = $type;
        $result->value = $value;
        return $result;
    }

    protected function form_num_interval(&$cc, $startchar, $endchar) {
        if (qtype_preg_unicode::ord($startchar) < qtype_preg_unicode::ord($endchar)) {
            $char = qtype_preg_unicode::ord($startchar);
            while($char <= qtype_preg_unicode::ord($endchar)) {
                $cc .= qtype_preg_unicode::code2utf8($char);
                $char++;
            }
        } else {
            $cc->error = 1;
        }
    }
    protected function push_opt_lvl($subpattnum = -1) {
        if ($this->optcount > 0) {
            $this->optstack[$this->optcount] = clone $this->optstack[$this->optcount - 1];

            if ($subpattnum !== -1) {
                $this->optstack[$this->optcount]->subpattnum = $subpattnum;
                $this->optstack[$this->optcount]->parennum = $this->optcount;
            }
            $this->optcount++;
        } /*else
            error will be found in parser, lexer does nothing for this error (close unopened bracket)*/
    }
    protected function pop_opt_lvl() {
        if ($this->optcount > 0) {
            $item = $this->optstack[$this->optcount - 1];
            $this->optcount--;
            // Is it a pair for (?|
            if ($item->parennum === $this->optcount) {
                // Are we out of a (?|...) block?
                if ($this->optstack[$this->optcount - 1]->subpattnum !== -1) {
                    $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;    // Reset subpattern numeration.
                } else {
                    $this->lastsubpatt = $this->maxsubpatt;
                }
            }
        }
    }
    public function mod_top_opt($set, $unset) {
        for ($i = 0; $i < $set->length(); $i++) {
            if (qtype_preg_unicode::strpos($unset, $set[$i])) {// Setting and unsetting modifier at the same time is error.
                $text = $this->yytext;
                $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar - qtype_preg_unicode::strlen($text), $this->yychar - 1);
                return;
            }
        }
        // If error does not exist, set and unset local modifiers.
        for ($i = 0; $i < $set->length(); $i++) {
            $tmp = $set[$i];
            $this->optstack[$this->optcount - 1]->$tmp = true;
        }
        for ($i = 0; $i < $unset->length(); $i++) {
            $tmp = $unset[$i];
            $this->optstack[$this->optcount - 1]->$tmp = false;
        }
    }
%}
%eof{
        if (isset($this->cc) && is_object($this->cc)) {// End of the expression inside a character class.
            $this->errors[] = new preg_lexem (preg_node_error::SUBTYPE_UNCLOSED_CHARCLASS, $this->cc->indfirst, $this->yychar - 1);
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
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), qtype_preg_unicode::substr($text, qtype_preg_unicode::strpos($text, ',') + 1, qtype_preg_unicode::strlen($text) - 2 - qtype_preg_unicode::strpos($text, ','))));
    return $res;
}
<YYINITIAL> \{[0-9]+,\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1)));
    return $res;
}
<YYINITIAL> \{,[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, qtype_preg_unicode::substr($text, 2, qtype_preg_unicode::strlen($text) - 3)));
    return $res;
}
<YYINITIAL> \{[0-9]+\} {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1)));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), qtype_preg_unicode::substr($text, qtype_preg_unicode::strpos($text, ',') + 1, qtype_preg_unicode::strlen($text) - 2 - qtype_preg_unicode::strpos($text, ',')), false));
    return $res;
}
<YYINITIAL> \{[0-9]+,\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_infinite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), null, false));
    return $res;
}
<YYINITIAL> \{,[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, 0, qtype_preg_unicode::substr($text, 2, qtype_preg_unicode::strlen($text) - 3), false));
    return $res;
}
<YYINITIAL> \{[0-9]+\}\? {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node('preg_node_finite_quant', null, null, qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), qtype_preg_unicode::substr($text, 1, qtype_preg_unicode::strpos($text, ',') - 1), false));
    return $res;
}
<YYINITIAL> \[ {
    $this->cc = new preg_leaf_charset;
    $this->cc->negative = false;
    $this->cccharnumber = 0;
    $this->cc->indfirst = $this->yychar;
    $this->ccset = '';
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> \( {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->lastsubpatt));
    return $res;
}
<YYINITIAL> \(\?\#\{\{\) {        // Beginning of a lexem.
    $this->push_opt_lvl();
    $this->lexemcount++;
    $res = $this->form_res(preg_parser_yyParser::OPENLEXEM, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, -$this->lexemcount));
    return $res;
}
<YYINITIAL> \) {
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, new preg_lexem(0, $this->yychar, $this->yychar));
    return $res;
}
<YYINITIAL> \(\?\#\}\}\) {        // Ending of a lexem.
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSELEXEM, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\#[^)]*\) {        // Comment.
    return $this->nextToken();
}
<YYINITIAL> \(\?> {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->lastsubpatt));
    return $res;
}
<YYINITIAL> \(\?\<[a-zA-Z_0-9]+\> {    // Named subpattern (?<name>...).
    $this->push_opt_lvl();
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    if (!array_key_exists($str, $this->subpatternmap)) {    // This subpattern does not exists.
        $num = ++$this->lastsubpatt;
        $this->subpatternmap[$str] = $num;
    } else {                                                // Subpatterns with same names should have same numbers.
        $num = $this->subpatternmap[$str];
        // TODO check if we are inside a (?|...) group.
    }
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $num));
    return $res;
}
<YYINITIAL> \(\?\'[a-zA-Z_0-9]+\' {    // Named subpattern (?'name'...).
    $this->push_opt_lvl();
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    if (!array_key_exists($str, $this->subpatternmap)) {    // This subpattern does not exists.
        $num = ++$this->lastsubpatt;
        $this->subpatternmap[$str] = $num;
    } else {                                                // Subpatterns with same names should have same numbers.
        $num = $this->subpatternmap[$str];
        // TODO check if we are inside a (?|...) group.
    }
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $num));
    return $res;
}
<YYINITIAL> \(\?P\<[a-zA-Z_0-9]+\> {   // Named subpattern (?P<name>...).
    $this->push_opt_lvl();
    $str = qtype_preg_unicode::substr($this->yytext(), 4);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    if (!array_key_exists($str, $this->subpatternmap)) {    // This subpattern does not exists.
        $num = ++$this->lastsubpatt;
        $this->subpatternmap[$str] = $num;
    } else {                                                // Subpatterns with same names should have same numbers.
        $num = $this->subpatternmap[$str];
        // TODO check if we are inside a (?|...) group.
    }
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $num));
    return $res;
}
<YYINITIAL> \(\?: {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?\| {
    $this->push_opt_lvl($this->lastsubpatt);    // Save the top-level subpattern number.
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
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DOT));
    $res->value->flags[0][0]->type = preg_charset_flag::FLAG;
    $res->value->flags[0][0]->flag = preg_charset_flag::PRIN;
    return $res;
}
<YYINITIAL> [^\[\]\\*+?{}()|.^$] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, $this->yytext()));
    return $res;
}
<YYINITIAL> \| {
    // Reset subpattern numeration inside a (?|...) group.
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum != -1) {
        $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;
    }
    $res = $this->form_res(preg_parser_yyParser::ALT, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \\[\[\]?*+{}|().] {
    $text = $this->yytext();
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::substr($text, 1, 1)));
    return $res;
}
<YYINITIAL> \\[1-9][0-9]?[0-9]? {
    $numstr = substr($this->yytext(), 1);
    $numdec = intval($numstr, 10);
    if ($numdec < 10 || ($numdec <= $this->maxsubpatt && $numdec < 100)) {
        // Return a backreference.
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $numstr));
        $res->value->matcher =& $this->matcher;
        $this->backrefsexist = true;
    } else {
        // Return a character.
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < qtype_preg_unicode::strlen($numstr); $i++) {
            $tmp = qtype_preg_unicode::substr($numstr, $i, 1);
            if (intval($tmp) < 8) {
                $octal = $octal . $tmp;
            } else {
                $failed = true;
            }
        }
        if (qtype_preg_unicode::strlen($octal) == 0) {    // If no octal digits found, it should be 0.
            $octal = '0';
            $tail = $numstr;
        } else {                      // Octal digits found.
            $tail = qtype_preg_unicode::substr($numstr, qtype_preg_unicode::strlen($octal));
        }
        // Return a single lexem if all digits are octal, an array of lexems otherwise.
        if (qtype_preg_unicode::strlen($tail) == 0) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8(octdec($octal))));
        } else {
            $res = array();
            $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8(octdec($octal))));
            for ($i = 0; $i < qtype_preg_unicode::strlen($tail); $i++) {
                $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::substr($tail, $i, 1)));
            }
        }
    }
    return $res;
}
<YYINITIAL> \\g[0-9][0-9]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, qtype_preg_unicode::substr($this->yytext(), 2)));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\g\{-?[0-9][0-9]?\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $numdec = intval($str, 10);
    // Is it a relative backreference? Is so, convert it to an absolute one.
    if ($numdec < 0) {
        $numdec = $this->lastsubpatt + $numdec + 1;
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $numdec));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\g\{[a-zA-Z_0-9]+\} {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\{[a-zA-Z_0-9]+\} {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\'[a-zA-Z_0-9]+\' {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\<[a-zA-Z_0-9]+\> {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \(\?P=[a-zA-Z_0-9]+\) {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 4);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_backref', null, $str));
    $res->value->matcher =& $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\0[0-7]?[0-7]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\x[0-9a-fA-F]?[0-9a-fA-F]? {
    $code = 0;
    $str = $this->yytext();
    if (qtype_preg_unicode::strlen($str) > 1) {
        $code = hexdec(qtype_preg_unicode::substr($str, 1));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8($code)));
    return $res;
}
<YYINITIAL> \\x\{[0-9a-fA-F]*\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3);
    $str = qtype_preg_unicode::substr($str, 0, qtype_preg_unicode::strlen($str) - 1);
    $code = 0;
    if (qtype_preg_unicode::strlen($str) > 1) {
        $code = hexdec($str);
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8($code)));
    return $res;
}
<YYINITIAL> \\\\ {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, '\\'));
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
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_d));
    return $res;
}
<YYINITIAL> \\D {
    $PARSLEAF = $this->form_node('preg_leaf_charset', self::CHARSET_D);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\w {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_w));
    return $res;
}
<YYINITIAL> \\W {
    $PARSLEAF = $this->form_node('preg_leaf_charset', self::CHARSET_W);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\s {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_s));
    return $res;
}
<YYINITIAL> \\S {
    $PARSLEAF = $this->form_node('preg_leaf_charset', self::CHARSET_S);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $PARSLEAF);
    return $res;
}
<YYINITIAL> \\t {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::code2utf8(9)));
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
    $this->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
}
<YYINITIAL> \(\?-i\) {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt(new qtype_preg_string(''), new qtype_preg_string('i'));
}
<YYINITIAL> \(\?i: {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1));
    return $res;
}
<YYINITIAL> \(\?-i: {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_preg_string(''), new qtype_preg_string('-i'));
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
    $leaf = $this->form_node('preg_leaf_charset', self::CHARSET_DUMMY, qtype_preg_unicode::substr($text, 1, 1));
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $leaf);
    return $res;
}
<CHARCLASS> \\\\ {
    $this->ccset .= '\\';
    $this->cccharnumber++;
}
<CHARCLASS> \\\[ {
    $this->ccset .= '[';
    $this->cccharnumber++;
}
<CHARCLASS> \\\] {
    $this->ccset .= ']';
    $this->cccharnumber++;
}
<CHARCLASS> \\0[0-9][0-9]|[0-9][0-9][0-9] {
    $this->ccset .= qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($this->yytext(), 1)));
    $this->cccharnumber++;
}
<CHARCLASS> \\x[0-9][0-9] {
    $this->cccharnumber++;
    $this->ccset->charset .= qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 1)));
}
<CHARCLASS> \\d|\[:digit:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::DIGIT);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:xdigit:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::xdigit);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\s|\[:space:\]  {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::SPACE);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\w|\[:word:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::WORDCHAR);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:alnum:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ALNUM);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:alpha:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ALPHA);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:ascii:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ASCII);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:ctrl:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::CNTRL);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:graph:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::GRAPH);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:lower:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::LOWER);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:upper:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::UPPER);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:print:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::PRIN);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \[:punct:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::PUNCT);
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\D|\["^":digit:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::DIGIT);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":xdigit:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::xdigit);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\S|\["^":space:\]  {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::SPACE);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\W|\["^":word:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::WORDCHAR);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":alnum:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ALNUM);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":alpha:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ALPHA);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":ascii:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::ASCII);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":ctrl:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::CNTRL);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":graph:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::GRAPH);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":lower:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::LOWER);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":upper:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::UPPER);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":print:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::PRIN);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \["^":punct:\] {
    $this->cccharnumber++;
    $flag = new preg_charset_flag;
    $flag->set_flag(preg_charset_flag::PUNCT);
    $flag->negative = true;
    $this->cc->flags[] = array($flag);
    $this->ccgotflag=true;
}
<CHARCLASS> \\t {
    $this->cccharnumber++;
    $this->ccset .= qtype_preg_unicode::code2utf8(9);
}
<CHARCLASS> "^" {
    if ($this->cccharnumber) {
        $this->ccset .= '^';
    } else {
        $this->cc->negative = true;
    }
    $this->cccharnumber++;
}
<CHARCLASS> "^-" {
    if (!$this->cccharnumber) {
        $this->ccset .= '-';
        $this->cc->negative = true;
        $this->cccharnumber++;
    }
}
<CHARCLASS> - {
    if (!$this->cccharnumber) {
        $this->ccset .= '-';
    }
    $this->cccharnumber++;
}
<CHARCLASS> [^\[\]\\*+?{}()|.^$]-[^\[\]\\*+?{}()|.^$] {
    $text = $this->yytext();
    $this->form_num_interval($this->ccset, qtype_preg_unicode::substr($text, 0, 1), qtype_preg_unicode::substr($text, 2, 1));
}
<CHARCLASS> \\- {
    $this->ccset .= '-';
    $this->cccharnumber++;
}
<CHARCLASS> [^-\[\]\\^] {
    $this->ccset .= $this->yytext();
    $this->cccharnumber++;
}
<CHARCLASS> \] {
    $this->cc->indlast = $this->yychar;
    $this->cc->israngecalculated = false;
    if ($this->ccset !== '') {
        $flag = new preg_charset_flag;
        $flag->set_set(new qtype_preg_string($this->ccset));
        $this->cc->flags[] = array($flag);
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    $this->yybegin(self::YYINITIAL);
    $this->cc = null;
    return $res;
}