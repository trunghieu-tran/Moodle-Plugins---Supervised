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
    public $matcher = null;    // Matcher is passed to some nodes.
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

    protected function form_node($userinscription = '', $name, $subtype = null, $data = null, $leftborder = null, $rightborder = null, $lazy = false, $greed = true, $possessive = false, $negative = false) {
        $result = new $name;
        $result->userinscription = $userinscription;
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
            $flag->negative = $negative;
            if ($subtype === preg_charset_flag::SET) {
                $flag->set_set(new qtype_preg_string($data));
            } else if ($subtype === preg_charset_flag::FLAG) {
                $flag->set_flag($data);

            } else if ($subtype === preg_charset_flag::UPROP) {
                $flag->set_uprop($data);
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
            $result->leftborder = $leftborder;
            $result->lazy = $lazy;
            $result->greed = $greed;
            $result->possessive = $possessive;
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

    protected function form_num_interval(&$cc, &$cclength) {
        $actuallength = $cclength;
        if (qtype_preg_unicode::substr($cc, 0, 1) === '^') {
            $actuallength--;
        }
        // Check if there are enough characters in before.
        if ($actuallength < 3 || qtype_preg_unicode::substr($cc, $cclength - 2, 1) !== '-') {
            return;
        }
        $startchar = qtype_preg_unicode::substr($cc, $cclength - 3, 1);
        $endchar = qtype_preg_unicode::substr($cc, $cclength - 1, 1);
        $cc = qtype_preg_unicode::substr($cc, 0, $cclength - 3);
        $cclength -= 3;
        // Replace last 3 characters by all the characters between them.
        if (qtype_preg_unicode::ord($startchar) < qtype_preg_unicode::ord($endchar)) {
            $curord = qtype_preg_unicode::ord($startchar);
            $endord = qtype_preg_unicode::ord($endchar);
            while ($curord <= $endord) {
                $cc .= qtype_preg_unicode::code2utf8($curord++);
                $cclength++;
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
                $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar - qtype_preg_unicode::strlen($text), $this->yychar - 1, '');
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

    public function map_subpattern($name) {
        if (!array_key_exists($name, $this->subpatternmap)) {   // This subpattern does not exists.
            $num = ++$this->lastsubpatt;
            $this->subpatternmap[$name] = $num;
        } else {                                                // Subpatterns with same names should have same numbers.
            $num = $this->subpatternmap[$name];
            // TODO check if we are inside a (?|...) group.
        }
        return $num;
    }

    public function calculate_cx($x) {
        $code = qtype_preg_unicode::ord($x);
        if ($code > 127) {
            throw new Exception('The code of \'' . $x . '\' is ' . $code . ', but should be <= 127.');
        }
        $code ^= 0x40;
        return qtype_preg_unicode::code2utf8($code);
    }

    public function add_flag_to_charset($userinscription = '', $type, $data, $negative = false) {
        $this->cccharnumber++;
        $this->cc->userinscription[] = $userinscription;
        switch ($type) {
        case preg_charset_flag::SET:
            $this->ccset .= $data;
            $this->form_num_interval($this->ccset, $this->cccharnumber);
            break;
        case preg_charset_flag::FLAG:
        case preg_charset_flag::UPROP:
            $flag = new preg_charset_flag;
            if ($type === preg_charset_flag::FLAG) {
                $flag->set_flag($data);
            } else if ($type === preg_charset_flag::UPROP) {
                $flag->set_uprop($data);
            }
            $flag->negative = $negative;
            $this->cc->flags[] = array($flag);
            $this->ccgotflag = true;
            break;
        }
    }

    public function get_uprop_flag_type($str) {
        if ($str === 'C') {
            return preg_charset_flag::UPROPC;
        } else if ($str === 'Cc') {
            return preg_charset_flag::UPROPCC;
        } else if ($str === 'Cf') {
            return preg_charset_flag::UPROPCF;
        } else if ($str === 'Cn') {
            return preg_charset_flag::UPROPCN;
        } else if ($str === 'Co') {
            return preg_charset_flag::UPROPCO;
        } else if ($str === 'Cs') {
            return preg_charset_flag::UPROPCS;
        } else if ($str === 'L') {
            return preg_charset_flag::UPROPL;
        } else if ($str === 'Ll') {
            return preg_charset_flag::UPROPLL;
        } else if ($str === 'Lm') {
            return preg_charset_flag::UPROPLM;
        } else if ($str === 'Lo') {
            return preg_charset_flag::UPROPLO;
        } else if ($str === 'Lt') {
            return preg_charset_flag::UPROPLT;
        } else if ($str === 'Lu') {
            return preg_charset_flag::UPROPLU;
        } else if ($str === 'M') {
            return preg_charset_flag::UPROPM;
        } else if ($str === 'Mc') {
            return preg_charset_flag::UPROPMC;
        } else if ($str === 'Me') {
            return preg_charset_flag::UPROPME;
        } else if ($str === 'Mn') {
            return preg_charset_flag::UPROPMN;
        } else if ($str === 'N') {
            return preg_charset_flag::UPROPN;
        } else if ($str === 'Nd') {
            return preg_charset_flag::UPROPND;
        } else if ($str === 'Nl') {
            return preg_charset_flag::UPROPNL;
        } else if ($str === 'No') {
            return preg_charset_flag::UPROPNO;
        } else if ($str === 'P') {
            return preg_charset_flag::UPROPP;
        } else if ($str === 'Pc') {
            return preg_charset_flag::UPROPPC;
        } else if ($str === 'Pd') {
            return preg_charset_flag::UPROPPD;
        } else if ($str === 'Pe') {
            return preg_charset_flag::UPROPPE;
        } else if ($str === 'Pf') {
            return preg_charset_flag::UPROPPF;
        } else if ($str === 'Pi') {
            return preg_charset_flag::UPROPPI;
        } else if ($str === 'Po') {
            return preg_charset_flag::UPROPPO;
        } else if ($str === 'Ps') {
            return preg_charset_flag::UPROPPS;
        } else if ($str === 'S') {
            return preg_charset_flag::UPROPS;
        } else if ($str === 'Sc') {
            return preg_charset_flag::UPROPSC;
        } else if ($str === 'Sk') {
            return preg_charset_flag::UPROPSK;
        } else if ($str === 'Sm') {
            return preg_charset_flag::UPROPSM;
        } else if ($str === 'So') {
            return preg_charset_flag::UPROPSO;
        } else if ($str === 'Z') {
            return preg_charset_flag::UPROPZ;
        } else if ($str === 'Zl') {
            return preg_charset_flag::UPROPZL;
        } else if ($str === 'Zp') {
            return preg_charset_flag::UPROPZP;
        } else if ($str === 'Zs') {
            return preg_charset_flag::UPROPZS;
        } else if ($str === 'Arabic') {
            return preg_charset_flag::ARABIC;
        } else if ($str === 'Armenian') {
            return preg_charset_flag::ARMENIAN;
        } else if ($str === 'Avestan') {
            return preg_charset_flag::AVESTAN;
        } else if ($str === 'Balinese') {
            return preg_charset_flag::BALINESE;
        } else if ($str === 'Bamum') {
            return preg_charset_flag::BAMUM;
        } else if ($str === 'Bengali') {
            return preg_charset_flag::BENGALI;
        } else if ($str === 'Bopomofo') {
            return preg_charset_flag::BOPOMOFO;
        } else if ($str === 'Braille') {
            return preg_charset_flag::BRAILLE;
        } else if ($str === 'Buginese') {
            return preg_charset_flag::BUGINESE;
        } else if ($str === 'Buhid') {
            return preg_charset_flag::BUHID;
        } else if ($str === 'Canadian_Aboriginal') {
            return preg_charset_flag::CANADIAN_ABORIGINAL;
        } else if ($str === 'Carian') {
            return preg_charset_flag::CARIAN;
        } else if ($str === 'Cham') {
            return preg_charset_flag::CHAM;
        } else if ($str === 'Cherokee') {
            return preg_charset_flag::CHEROKEE;
        } else if ($str === 'Common') {
            return preg_charset_flag::COMMON;
        } else if ($str === 'Coptic') {
            return preg_charset_flag::COPTIC;
        } else if ($str === 'Cuneiform') {
            return preg_charset_flag::CUNEIFORM;
        } else if ($str === 'Cypriot') {
            return preg_charset_flag::CYPRIOT;
        } else if ($str === 'Cyrillic') {
            return preg_charset_flag::CYRILLIC;
        } else if ($str === 'Deseret') {
            return preg_charset_flag::DESERET;
        } else if ($str === 'Devanagari') {
            return preg_charset_flag::DEVANAGARI;
        } else if ($str === 'Egyptian_Hieroglyphs') {
            return preg_charset_flag::EGYPTIAN_HIEROGLYPHS;
        } else if ($str === 'Ethiopic') {
            return preg_charset_flag::ETHIOPIC;
        } else if ($str === 'Georgian') {
            return preg_charset_flag::GEORGIAN;
        } else if ($str === 'Glagolitic') {
            return preg_charset_flag::GLAGOLITIC;
        } else if ($str === 'Gothic') {
            return preg_charset_flag::GOTHIC;
        } else if ($str === 'Greek') {
            return preg_charset_flag::GREEK;
        } else if ($str === 'Gujarati') {
            return preg_charset_flag::GUJARATI;
        } else if ($str === 'Gurmukhi') {
            return preg_charset_flag::GURMUKHI;
        } else if ($str === 'Han') {
            return preg_charset_flag::HAN;
        } else if ($str === 'Hangul') {
            return preg_charset_flag::HANGUL;
        } else if ($str === 'Hanunoo') {
            return preg_charset_flag::HANUNOO;
        } else if ($str === 'Hebrew') {
            return preg_charset_flag::HEBREW;
        } else if ($str === 'Hiragana') {
            return preg_charset_flag::HIRAGANA;
        } else if ($str === 'Imperial_Aramaic') {
            return preg_charset_flag::IMPERIAL_ARAMAIC;
        } else if ($str === 'Inherited') {
            return preg_charset_flag::INHERITED;
        } else if ($str === 'Inscriptional_Pahlavi') {
            return preg_charset_flag::INSCRIPTIONAL_PAHLAVI;
        } else if ($str === 'Inscriptional_Parthian') {
            return preg_charset_flag::INSCRIPTIONAL_PARTHIAN;
        } else if ($str === 'Javanese') {
            return preg_charset_flag::JAVANESE;
        } else if ($str === 'Kaithi') {
            return preg_charset_flag::KAITHI;
        } else if ($str === 'Kannada') {
            return preg_charset_flag::KANNADA;
        } else if ($str === 'Katakana') {
            return preg_charset_flag::KATAKANA;
        } else if ($str === 'Kayah_Li') {
            return preg_charset_flag::KAYAH_LI;
        } else if ($str === 'Kharoshthi') {
            return preg_charset_flag::KHAROSHTHI;
        } else if ($str === 'Khmer') {
            return preg_charset_flag::KHMER;
        } else if ($str === 'Lao') {
            return preg_charset_flag::LAO;
        } else if ($str === 'Latin') {
            return preg_charset_flag::LATIN;
        } else if ($str === 'Lepcha') {
            return preg_charset_flag::LEPCHA;
        } else if ($str === 'Limbu') {
            return preg_charset_flag::LIMBU;
        } else if ($str === 'Linear_B') {
            return preg_charset_flag::LINEAR_B;
        } else if ($str === 'Lisu') {
            return preg_charset_flag::LISU;
        } else if ($str === 'Lycian') {
            return preg_charset_flag::LYCIAN;
        } else if ($str === 'Lydian') {
            return preg_charset_flag::LYDIAN;
        } else if ($str === 'Malayalam') {
            return preg_charset_flag::MALAYALAM;
        } else if ($str === 'Meetei_Mayek') {
            return preg_charset_flag::MEETEI_MAYEK;
        } else if ($str === 'Mongolian') {
            return preg_charset_flag::MONGOLIAN;
        } else if ($str === 'Myanmar') {
            return preg_charset_flag::MYANMAR;
        } else if ($str === 'New_Tai_Lue') {
            return preg_charset_flag::NEW_TAI_LUE;
        } else if ($str === 'Nko') {
            return preg_charset_flag::NKO;
        } else if ($str === 'Ogham') {
            return preg_charset_flag::OGHAM;
        } else if ($str === 'Old_Italic') {
            return preg_charset_flag::OLD_ITALIC;
        } else if ($str === 'Old_Persian') {
            return preg_charset_flag::OLD_PERSIAN;
        } else if ($str === 'Old_South_Arabian') {
            return preg_charset_flag::OLD_SOUTH_ARABIAN;
        } else if ($str === 'Old_Turkic') {
            return preg_charset_flag::OLD_TURKIC;
        } else if ($str === 'Ol_Chiki') {
            return preg_charset_flag::OL_CHIKI;
        } else if ($str === 'Oriya') {
            return preg_charset_flag::ORIYA;
        } else if ($str === 'Osmanya') {
            return preg_charset_flag::OSMANYA;
        } else if ($str === 'Phags_Pa') {
            return preg_charset_flag::PHAGS_PA;
        } else if ($str === 'Phoenician') {
            return preg_charset_flag::PHOENICIAN;
        } else if ($str === 'Rejang') {
            return preg_charset_flag::REJANG;
        } else if ($str === 'Runic') {
            return preg_charset_flag::RUNIC;
        } else if ($str === 'Samaritan') {
            return preg_charset_flag::SAMARITAN;
        } else if ($str === 'Saurashtra') {
            return preg_charset_flag::SAURASHTRA;
        } else if ($str === 'Shavian') {
            return preg_charset_flag::SHAVIAN;
        } else if ($str === 'Sinhala') {
            return preg_charset_flag::SINHALA;
        } else if ($str === 'Sundanese') {
            return preg_charset_flag::SUNDANESE;
        } else if ($str === 'Syloti_Nagri') {
            return preg_charset_flag::SYLOTI_NAGRI;
        } else if ($str === 'Syriac') {
            return preg_charset_flag::SYRIAC;
        } else if ($str === 'Tagalog') {
            return preg_charset_flag::TAGALOG;
        } else if ($str === 'Tagbanwa') {
            return preg_charset_flag::TAGBANWA;
        } else if ($str === 'Tai_Le') {
            return preg_charset_flag::TAI_LE;
        } else if ($str === 'Tai_Tham') {
            return preg_charset_flag::TAI_THAM;
        } else if ($str === 'Tai_Viet') {
            return preg_charset_flag::TAI_VIET;
        } else if ($str === 'Tamil') {
            return preg_charset_flag::TAMIL;
        } else if ($str === 'Telugu') {
            return preg_charset_flag::TELUGU;
        } else if ($str === 'Thaana') {
            return preg_charset_flag::THAANA;
        } else if ($str === 'Thai') {
            return preg_charset_flag::THAI;
        } else if ($str === 'Tibetan') {
            return preg_charset_flag::TIBETAN;
        } else if ($str === 'Tifinagh') {
            return preg_charset_flag::TIFINAGH;
        } else if ($str === 'Ugaritic') {
            return preg_charset_flag::UGARITIC;
        } else if ($str === 'Vai') {
            return preg_charset_flag::VAI;
        } else if ($str === 'Yi') {
            return preg_charset_flag::YI;
        } else {
            throw new Exception('Unknown unicode property: ' . $str);
        }
    }

%}
%eof{
        if (isset($this->cc) && is_object($this->cc)) { // End of the expression inside a character class.
            $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_UNCLOSED_CHARCLASS, $this->cc->indfirst, $this->yychar - 1, '');
            $this->cc = null;
        }
%eof}
%%

<YYINITIAL> \?(\?|\+)? {
    $greed = qtype_preg_unicode::strlen($this->yytext()) === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_finite_quant', null, null, 0, 1, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \*(\?|\+)? {
    $greed = qtype_preg_unicode::strlen($this->yytext()) === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_infinite_quant', null, null, 0, null, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \+(\?|\+)? {
    $greed = qtype_preg_unicode::strlen($this->yytext()) === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_infinite_quant', null, null, 1, null, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \{[0-9]+,[0-9]+\}(\?|\+)? {
    $text = $this->yytext();
    $textlen = qtype_preg_unicode::strlen($text);
    $lastchar = qtype_preg_unicode::substr($text, $textlen - 1, 1);
    $greed = ($lastchar === '}');
    $lazy = !$greed && $lastchar === '?';
    $possessive = !$greed && !$lazy;
    if (!$greed) {
        $textlen--;
    }
    $delimpos = qtype_preg_unicode::strpos($text, ',');
    $leftborder = (int)qtype_preg_unicode::substr($text, 1, $delimpos - 1);
    $rightborder = (int)qtype_preg_unicode::substr($text, $delimpos + 1, $textlen - 2 - $delimpos);
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_finite_quant', null, null, $leftborder, $rightborder, $lazy, $greed, $possessive));
    return $res;
}

<YYINITIAL> \{[0-9]+,\}(\?|\+)? {
    $text = $this->yytext();
    $textlen = qtype_preg_unicode::strlen($text);
    $lastchar = qtype_preg_unicode::substr($text, $textlen - 1, 1);
    $greed = ($lastchar === '}');
    $lazy = !$greed && $lastchar === '?';
    $possessive = !$greed && !$lazy;
    if (!$greed) {
        $textlen--;
    }
    $leftborder = (int)qtype_preg_unicode::substr($text, 1, $textlen - 1);
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_infinite_quant', null, null, $leftborder, null, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \{,[0-9]+\}(\?|\+)? {
    $text = $this->yytext();
    $textlen = qtype_preg_unicode::strlen($text);
    $lastchar = qtype_preg_unicode::substr($text, $textlen - 1, 1);
    $greed = ($lastchar === '}');
    $lazy = !$greed && $lastchar === '?';
    $possessive = !$greed && !$lazy;
    if (!$greed) {
        $textlen--;
    }
    $rightborder = (int)qtype_preg_unicode::substr($text, 2, $textlen - 3);
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_finite_quant', null, null, 0, $rightborder, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \{[0-9]+\}(\?|\+)? {
    $text = $this->yytext();
    $textlen = qtype_preg_unicode::strlen($text);
    $lastchar = qtype_preg_unicode::substr($text, $textlen - 1, 1);
    $greed = ($lastchar === '}');
    $lazy = !$greed && $lastchar === '?';
    $possessive = !$greed && !$lazy;
    if (!$greed) {
        $textlen--;
    }
    $count = (int)qtype_preg_unicode::substr($text, 1, $textlen - 2);
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_finite_quant', null, null, $count, $count, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> \[ {
    $this->cc = new preg_leaf_charset;
    $this->cc->indfirst = $this->yychar;
    $this->cc->userinscription = array();
    $this->cccharnumber = 0;
    $this->ccset = '';
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> \( {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->yytext(), $this->lastsubpatt));
    return $res;
}
<YYINITIAL> \(\?\#\{\{\) {        // Beginning of a lexem.
    $this->push_opt_lvl();
    $this->lexemcount++;
    $res = $this->form_res(preg_parser_yyParser::OPENLEXEM, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), -$this->lexemcount));
    return $res;
}
<YYINITIAL> \) {
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, new preg_lexem(0, $this->yychar, $this->yychar, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\#\}\}\) {        // Ending of a lexem.
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSELEXEM, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\#[^)]*\) {       // Comment.
    return $this->nextToken();
}
<YYINITIAL> \(\*[^\[\]\\*+?{}()|.^$]*\) {
    return $this->nextToken();
}
<YYINITIAL> \(\?> {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $this->lastsubpatt));
    return $res;
}
<YYINITIAL> \(\?\<[^\[\]\\*+?{}()|.^$]+\> {    // Named subpattern (?<name>...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> \(\?\'[^\[\]\\*+?{}()|.^$]+\' {    // Named subpattern (?'name'...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> \(\?P\<[^\[\]\\*+?{}()|.^$]+\> {   // Named subpattern (?P<name>...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 4, qtype_preg_unicode::strlen($this->yytext()) - 5));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> \(\?: {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\| {
    $this->push_opt_lvl($this->lastsubpatt);    // Save the top-level subpattern number.
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\(\?= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\(\?! {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\(\?<= {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?\(\?<! {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?! {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?<= {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?<! {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \. {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> [^\[\]\\*+?{}()|.^$] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $this->yytext()));
    return $res;
}
<YYINITIAL> \| {
    // Reset subpattern numeration inside a (?|...) group.
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum != -1) {
        $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;
    }
    $res = $this->form_res(preg_parser_yyParser::ALT, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \\[\[\]?*+{}|().] {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::substr($this->yytext(), 1, 1)));
    return $res;
}
<YYINITIAL> \\[1-9][0-9]?[0-9]? {
    $str = qtype_preg_unicode::substr($this->yytext(), 1);
    if ((int)$str < 10 || ((int)$str <= $this->maxsubpatt && (int)$str < 100)) {
        // Return a backreference.
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
        $res->value->matcher = $this->matcher;
        $this->backrefsexist = true;
    } else {
        // Return a character.
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < qtype_preg_unicode::strlen($str); $i++) {
            $tmp = qtype_preg_unicode::substr($str, $i, 1);
            if (intval($tmp) < 8) {
                $octal = $octal . $tmp;
            } else {
                $failed = true;
            }
        }
        if (qtype_preg_unicode::strlen($octal) === 0) {    // If no octal digits found, it should be 0.
            $octal = '0';
            $tail = $str;
        } else {                      // Octal digits found.
            $tail = qtype_preg_unicode::substr($str, qtype_preg_unicode::strlen($octal));
        }
        // Return a single lexem if all digits are octal, an array of lexems otherwise.
        if (qtype_preg_unicode::strlen($tail) === 0) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(octdec($octal))));
        } else {
            $res = array();
            $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(octdec($octal))));
            for ($i = 0; $i < qtype_preg_unicode::strlen($tail); $i++) {
                $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::substr($tail, $i, 1)));
            }
        }
    }
    return $res;
}
<YYINITIAL> \\g[0-9][0-9]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, qtype_preg_unicode::substr($this->yytext(), 2)));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\g\{-?[0-9][0-9]?\} {
    $num = (int)qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    // Is it a relative backreference? Is so, convert it to an absolute one.
    if ($num < 0) {
        $num = $this->lastsubpatt + $num + 1;
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $num));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\g\{[^\[\]\\*+?{}()|.^$]+\} {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\{[^\[\]\\*+?{}()|.^$]+\} {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\'[^\[\]\\*+?{}()|.^$]+\' {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\k\<[^\[\]\\*+?{}()|.^$]+\> {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \(\?P=[^\[\]\\*+?{}()|.^$]+\) {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 4, qtype_preg_unicode::strlen($this->yytext()) - 5);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> \\0[0-7]?[0-7]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($this->yytext(), 1)))));
    return $res;
}
<YYINITIAL> \\\\ {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, '\\'));
    return $res;
}
<YYINITIAL> \\a {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x07)));
    return $res;
}
<YYINITIAL> \\c. {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $this->calculate_cx(qtype_preg_unicode::substr($this->yytext(), 2))));
    return $res;
}
<YYINITIAL> \\e {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x1B)));
    return $res;
}
<YYINITIAL> \\f {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0C)));
    return $res;
}
<YYINITIAL> \\n {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0A)));
    return $res;
}
<YYINITIAL> (\\p|\\P)[CLMNPSZ] {
    $str = qtype_preg_unicode::substr($this->yytext(), 2);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::UPROP, $this->get_uprop_flag_type($str), null, null, false, false, false, $negative));
    return $res;
}
<YYINITIAL> (\\p|\\P)\{"^"?[a-z_A-Z]+\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str !== 'Any') {
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::UPROP, $this->get_uprop_flag_type($str), null, null, false, false, false, $negative));
    } else {
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN, null, null, false, false, false, $negative));
    }
    return $res;
}
<YYINITIAL> \\r {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0D)));
    return $res;
}
<YYINITIAL> \\t {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x09)));
    return $res;
}
<YYINITIAL> \\x[0-9a-fA-F]?[0-9a-fA-F]? {
    if (qtype_preg_unicode::strlen($this->yytext()) < 3) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $str));
    return $res;
}
<YYINITIAL> \\x\{[0-9a-fA-F]+\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(hexdec($str))));
    return $res;
}
<YYINITIAL> \\d|\\D {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::DIGIT, null, null, false, false, false, ($this->yytext() === '\D')));
    return $res;
}
<YYINITIAL> \\h|\\H {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::HSPACE, null, null, false, false, false, ($this->yytext() === '\H')));
    return $res;
}
<YYINITIAL> \\s|\\S {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::SPACE, null, null, false, false, false, ($this->yytext() === '\S')));
    return $res;
}
<YYINITIAL> \\v|\\V {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::VSPACE, null, null, false, false, false, ($this->yytext() === '\V')));
    return $res;
}
<YYINITIAL> \\w|\\W {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::WORDCHAR, null, null, false, false, false, ($this->yytext() === '\W')));
    return $res;
}
<YYINITIAL> \\C {
    // TODO: matches any one data unit. For now implemented the same way as dot.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> \\u([0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F])? {
    if (qtype_preg_unicode::strlen($this->yytext()) === 2) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $str));
    return $res;
}
<YYINITIAL> \\N {
    // TODO: matches any character except new line characters. For now, the same as dot.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> \\K {
    // TODO: reset start of match.
    throw new Exception('\K is not implemented yet');
}
<YYINITIAL> \\R {
    // TODO: matches new line unicode sequences.
    // \B, \R, and \X are not special inside a character class.
    throw new Exception('\R is not implemented yet');
}
<YYINITIAL> \\X {
    // TODO: matches  any number of Unicode characters that form an extended Unicode sequence.
    // \B, \R, and \X are not special inside a character class.
    throw new Exception('\R is not implemented yet');
}
<YYINITIAL> \\b|\\B {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    $res->value->negative = ($this->yytext() === '\B');
    return $res;
}
<YYINITIAL> \\A {
    // TODO: matches at the start of the subject. For now the same as ^.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX));
    return $res;
}
<YYINITIAL> \\z|\\Z {
    // TODO: matches only at the end of the subject | matches at the end of the subject also matches before a newline at the end of the subject. For now the same as $.
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR));
    return $res;
}
<YYINITIAL> \\G {
    // TODO: matches at the first matching position in the subject. For now the same as ^.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX));
    return $res;
}
<YYINITIAL> "^" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX));
    return $res;
}
<YYINITIAL> "$" {
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR));
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
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?-i: {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_preg_string(''), new qtype_preg_string('-i'));
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \(\?(R|[0-9]+)\) {
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_recursion', null, $this->yytext()));
    return $res;
}
<YYINITIAL> \\Q.*(\\E)? {
    $text = $this->yytext();
    $str = '';
    $epos = qtype_preg_unicode::strpos($text, '\\E');
    if ($epos === false) {
        $str = qtype_preg_unicode::substr($text, 2);
    } else {
        $str = qtype_preg_unicode::substr($text, 2, $epos - 2);
        // Here's a trick. Quantifiers are greed, so a part after '\Q...\E' can be matched by this rule. Reset $this->yy_buffer_index manually.
        $tail = qtype_preg_unicode::substr($text, $epos + 2);
        $this->yy_buffer_index -= qtype_preg_unicode::strlen($tail);
    }
    $res = array();
    for ($i = 0; $i < qtype_preg_unicode::strlen($str); $i++) {
        $res[] = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::substr($str, $i, 1)));
    }
    return $res;
}
<YYINITIAL> \\. {
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::substr($this->yytext(), 1, 1)));
    return $res;
}
<CHARCLASS> \[:alnum:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALNUM);
}
<CHARCLASS> \[:alpha:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALPHA);
}
<CHARCLASS> \[:ascii:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ASCII);
}
<CHARCLASS> \\h|\[:blank:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::HSPACE);
}
<CHARCLASS> \[:ctrl:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::CNTRL);
}
<CHARCLASS> \\d|\[:digit:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::DIGIT);
}
<CHARCLASS> \[:graph:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::GRAPH);
}
<CHARCLASS> \[:lower:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::LOWER);
}
<CHARCLASS> \[:print:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PRIN);
}
<CHARCLASS> \[:punct:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PUNCT);
}
<CHARCLASS> \\s|\[:space:\]  {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::SPACE);
}
<CHARCLASS> \[:upper:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::UPPER);
}
<CHARCLASS> \\w|\[:word:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::WORDCHAR);
}
<CHARCLASS> \[:xdigit:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::XDIGIT);
}
<CHARCLASS> \["^":alnum:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALNUM, true);
}
<CHARCLASS> \["^":alpha:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALNUM, true);
}
<CHARCLASS> \["^":ascii:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ASCII, true);
}
<CHARCLASS> \\H|\["^":blank:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::HSPACE, true);
}
<CHARCLASS> \["^":ctrl:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::CNTRL, true);
}
<CHARCLASS> \\D|\["^":digit:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::DIGIT, true);
}
<CHARCLASS> \["^":graph:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::GRAPH, true);
}
<CHARCLASS> \["^":lower:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::LOWER, true);
}
<CHARCLASS> \["^":print:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PRIN, true);
}
<CHARCLASS> \["^":punct:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PUNCT, true);
}
<CHARCLASS> \\S|\["^":space:\]  {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::SPACE, true);
}
<CHARCLASS> \["^":upper:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::UPPER, true);
}
<CHARCLASS> \\W|\["^":word:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::WORDCHAR, true);
}
<CHARCLASS> \["^":xdigit:\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::XDIGIT, true);
}
<CHARCLASS> (\\p|\\P)[CLMNPSZ] {
    $str = qtype_preg_unicode::substr($this->yytext(), 2);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::UPROP, $this->get_uprop_flag_type($str), $negative);
}
<CHARCLASS> (\\p|\\P)\{"^"?[a-z_A-Z]+\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str !== 'Any') {
        $this->add_flag_to_charset($this->yytext(), preg_charset_flag::UPROP, $this->get_uprop_flag_type($str), $negative);
    } else {
        $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PRIN, $negative);
    }
}
<CHARCLASS> \\\\ {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '\\');
}
<CHARCLASS> \\\[ {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '[');
}
<CHARCLASS> \\\] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, ']');
}
<CHARCLASS> \\0[0-7][0-7]|[0-7][0-7][0-7] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($this->yytext(), 1))));
}
<CHARCLASS> \\x[0-9a-fA-F]?[0-9a-fA-F]? {
    if (qtype_preg_unicode::strlen($this->yytext()) < 3) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $str);
}
<CHARCLASS> \\x\{[0-9a-fA-F]+\} {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, qtype_preg_unicode::strlen($this->yytext()) - 4);
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(hexdec($str)));
}
<CHARCLASS> \\a {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x07));
}
<CHARCLASS> \\c. {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $this->calculate_cx(qtype_preg_unicode::substr($this->yytext(), 2)));
}
<CHARCLASS> \\e {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x1B));
}
<CHARCLASS> \\f {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0C));
}
<CHARCLASS> \\n {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0A));
}
<CHARCLASS> \\r {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0D));
}
<CHARCLASS> \\t {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x09));
}
<CHARCLASS> "^" {
    if ($this->cccharnumber) {
        $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '^');
    } else {
        $this->cc->negative = true;
    }
}
<CHARCLASS> - {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '-');
}
<CHARCLASS> \\ {
    // Do nothing.
}
<CHARCLASS> [^-\[\]\\^] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $this->yytext());
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
    $this->cc = null;
    $this->yybegin(self::YYINITIAL);
    return $res;
}
