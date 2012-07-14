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
SPECIAL = [\\^$.\[\]|()?*+{}]
NOTSPECIAL = [^\\^$.\[\]|()?*+{}]
%init{
    $this->errors = array();
    $this->lastsubpatt = 0;
    $this->maxsubpatt = 0;
    $this->subpatternmap = array();
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
    protected $backrefsexist;
    protected $optstack;
    protected $optcount;
    protected static $upropflags = array('C'                      => preg_charset_flag::UPROPC,
                                         'Cc'                     => preg_charset_flag::UPROPCC,
                                         'Cf'                     => preg_charset_flag::UPROPCF,
                                         'Cn'                     => preg_charset_flag::UPROPCN,
                                         'Co'                     => preg_charset_flag::UPROPCO,
                                         'Cs'                     => preg_charset_flag::UPROPCS,
                                         'L'                      => preg_charset_flag::UPROPL,
                                         'Ll'                     => preg_charset_flag::UPROPLL,
                                         'Lm'                     => preg_charset_flag::UPROPLM,
                                         'Lo'                     => preg_charset_flag::UPROPLO,
                                         'Lt'                     => preg_charset_flag::UPROPLT,
                                         'Lu'                     => preg_charset_flag::UPROPLU,
                                         'M'                      => preg_charset_flag::UPROPM,
                                         'Mc'                     => preg_charset_flag::UPROPMC,
                                         'Me'                     => preg_charset_flag::UPROPME,
                                         'Mn'                     => preg_charset_flag::UPROPMN,
                                         'N'                      => preg_charset_flag::UPROPN,
                                         'Nd'                     => preg_charset_flag::UPROPND,
                                         'Nl'                     => preg_charset_flag::UPROPNL,
                                         'No'                     => preg_charset_flag::UPROPNO,
                                         'P'                      => preg_charset_flag::UPROPP,
                                         'Pc'                     => preg_charset_flag::UPROPPC,
                                         'Pd'                     => preg_charset_flag::UPROPPD,
                                         'Pe'                     => preg_charset_flag::UPROPPE,
                                         'Pf'                     => preg_charset_flag::UPROPPF,
                                         'Pi'                     => preg_charset_flag::UPROPPI,
                                         'Po'                     => preg_charset_flag::UPROPPO,
                                         'Ps'                     => preg_charset_flag::UPROPPS,
                                         'S'                      => preg_charset_flag::UPROPS,
                                         'Sc'                     => preg_charset_flag::UPROPSC,
                                         'Sk'                     => preg_charset_flag::UPROPSK,
                                         'Sm'                     => preg_charset_flag::UPROPSM,
                                         'So'                     => preg_charset_flag::UPROPSO,
                                         'Z'                      => preg_charset_flag::UPROPZ,
                                         'Zl'                     => preg_charset_flag::UPROPZL,
                                         'Zp'                     => preg_charset_flag::UPROPZP,
                                         'Zs'                     => preg_charset_flag::UPROPZS,
                                         'Arabic'                 => preg_charset_flag::ARABIC,
                                         'Armenian'               => preg_charset_flag::ARMENIAN,
                                         'Avestan'                => preg_charset_flag::AVESTAN,
                                         'Balinese'               => preg_charset_flag::BALINESE,
                                         'Bamum'                  => preg_charset_flag::BAMUM,
                                         'Bengali'                => preg_charset_flag::BENGALI,
                                         'Bopomofo'               => preg_charset_flag::BOPOMOFO,
                                         'Braille'                => preg_charset_flag::BRAILLE,
                                         'Buginese'               => preg_charset_flag::BUGINESE,
                                         'Buhid'                  => preg_charset_flag::BUHID,
                                         'Canadian_Aboriginal'    => preg_charset_flag::CANADIAN_ABORIGINAL,
                                         'Carian'                 => preg_charset_flag::CARIAN,
                                         'Cham'                   => preg_charset_flag::CHAM,
                                         'Cherokee'               => preg_charset_flag::CHEROKEE,
                                         'Common'                 => preg_charset_flag::COMMON,
                                         'Coptic'                 => preg_charset_flag::COPTIC,
                                         'Cuneiform'              => preg_charset_flag::CUNEIFORM,
                                         'Cypriot'                => preg_charset_flag::CYPRIOT,
                                         'Cyrillic'               => preg_charset_flag::CYRILLIC,
                                         'Deseret'                => preg_charset_flag::DESERET,
                                         'Devanagari'             => preg_charset_flag::DEVANAGARI,
                                         'Egyptian_Hieroglyphs'   => preg_charset_flag::EGYPTIAN_HIEROGLYPHS,
                                         'Ethiopic'               => preg_charset_flag::ETHIOPIC,
                                         'Georgian'               => preg_charset_flag::GEORGIAN,
                                         'Glagolitic'             => preg_charset_flag::GLAGOLITIC,
                                         'Gothic'                 => preg_charset_flag::GOTHIC,
                                         'Greek'                  => preg_charset_flag::GREEK,
                                         'Gujarati'               => preg_charset_flag::GUJARATI,
                                         'Gurmukhi'               => preg_charset_flag::GURMUKHI,
                                         'Han'                    => preg_charset_flag::HAN,
                                         'Hangul'                 => preg_charset_flag::HANGUL,
                                         'Hanunoo'                => preg_charset_flag::HANUNOO,
                                         'Hebrew'                 => preg_charset_flag::HEBREW,
                                         'Hiragana'               => preg_charset_flag::HIRAGANA,
                                         'Imperial_Aramaic'       => preg_charset_flag::IMPERIAL_ARAMAIC,
                                         'Inherited'              => preg_charset_flag::INHERITED,
                                         'Inscriptional_Pahlavi'  => preg_charset_flag::INSCRIPTIONAL_PAHLAVI,
                                         'Inscriptional_Parthian' => preg_charset_flag::INSCRIPTIONAL_PARTHIAN,
                                         'Javanese'               => preg_charset_flag::JAVANESE,
                                         'Kaithi'                 => preg_charset_flag::KAITHI,
                                         'Kannada'                => preg_charset_flag::KANNADA,
                                         'Katakana'               => preg_charset_flag::KATAKANA,
                                         'Kayah_Li'               => preg_charset_flag::KAYAH_LI,
                                         'Kharoshthi'             => preg_charset_flag::KHAROSHTHI,
                                         'Khmer'                  => preg_charset_flag::KHMER,
                                         'Lao'                    => preg_charset_flag::LAO,
                                         'Latin'                  => preg_charset_flag::LATIN,
                                         'Lepcha'                 => preg_charset_flag::LEPCHA,
                                         'Limbu'                  => preg_charset_flag::LIMBU,
                                         'Linear_B'               => preg_charset_flag::LINEAR_B,
                                         'Lisu'                   => preg_charset_flag::LISU,
                                         'Lycian'                 => preg_charset_flag::LYCIAN,
                                         'Lydian'                 => preg_charset_flag::LYDIAN,
                                         'Malayalam'              => preg_charset_flag::MALAYALAM,
                                         'Meetei_Mayek'           => preg_charset_flag::MEETEI_MAYEK,
                                         'Mongolian'              => preg_charset_flag::MONGOLIAN,
                                         'Myanmar'                => preg_charset_flag::MYANMAR,
                                         'New_Tai_Lue'            => preg_charset_flag::NEW_TAI_LUE,
                                         'Nko'                    => preg_charset_flag::NKO,
                                         'Ogham'                  => preg_charset_flag::OGHAM,
                                         'Old_Italic'             => preg_charset_flag::OLD_ITALIC,
                                         'Old_Persian'            => preg_charset_flag::OLD_PERSIAN,
                                         'Old_South_Arabian'      => preg_charset_flag::OLD_SOUTH_ARABIAN,
                                         'Old_Turkic'             => preg_charset_flag::OLD_TURKIC,
                                         'Ol_Chiki'               => preg_charset_flag::OL_CHIKI,
                                         'Oriya'                  => preg_charset_flag::ORIYA,
                                         'Osmanya'                => preg_charset_flag::OSMANYA,
                                         'Phags_Pa'               => preg_charset_flag::PHAGS_PA,
                                         'Phoenician'             => preg_charset_flag::PHOENICIAN,
                                         'Rejang'                 => preg_charset_flag::REJANG,
                                         'Runic'                  => preg_charset_flag::RUNIC,
                                         'Samaritan'              => preg_charset_flag::SAMARITAN,
                                         'Saurashtra'             => preg_charset_flag::SAURASHTRA,
                                         'Shavian'                => preg_charset_flag::SHAVIAN,
                                         'Sinhala'                => preg_charset_flag::SINHALA,
                                         'Sundanese'              => preg_charset_flag::SUNDANESE,
                                         'Syloti_Nagri'           => preg_charset_flag::SYLOTI_NAGRI,
                                         'Syriac'                 => preg_charset_flag::SYRIAC,
                                         'Tagalog'                => preg_charset_flag::TAGALOG,
                                         'Tagbanwa'               => preg_charset_flag::TAGBANWA,
                                         'Tai_Le'                 => preg_charset_flag::TAI_LE,
                                         'Tai_Tham'               => preg_charset_flag::TAI_THAM,
                                         'Tai_Viet'               => preg_charset_flag::TAI_VIET,
                                         'Tamil'                  => preg_charset_flag::TAMIL,
                                         'Telugu'                 => preg_charset_flag::TELUGU,
                                         'Thaana'                 => preg_charset_flag::THAANA,
                                         'Thai'                   => preg_charset_flag::THAI,
                                         'Tibetan'                => preg_charset_flag::TIBETAN,
                                         'Tifinagh'               => preg_charset_flag::TIFINAGH,
                                         'Ugaritic'               => preg_charset_flag::UGARITIC,
                                         'Vai'                    => preg_charset_flag::VAI,
                                         'Yi'                     => preg_charset_flag::YI
                                  );


    public function get_errors() {
        return $this->errors;
    }

    public function get_max_subpattern() {
        return $this->maxsubpatt;
    }

    public function get_subpattern_map() {
        return $this->subpatternmap;
    }

    public function backrefs_exist() {
        return $this->backrefsexist;
    }

    /**
     * Forms a preg_node with the given oprions.
     * @param userinscription a string typed by user and consumed by lexer.
     * @param name name of the class to create object of.
     * @param subtype subtype of the node, a constant of preg_node.
     * @param data something specific for the node.
     * @param leftborder used in quantifiers.
     * @param rightborder used in quantifiers.
     * @param lazy used in quantifiers.
     * @param greed used in quantifiers.
     * @param possessive used in quantifiers.
     * @param negative is this node negative.
     * @return an object of preg_node child class.
     */
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
                $data = new qtype_preg_string($data);
            }
            $flag->set_data($subtype, $data);
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

    /**
     * Forms a result to return from the lexer.
     * @param type is this a leaf or a node, should be a constant of preg_parser_yyParser.
     * @param value can be either a preg_node or a preg_lexem.
     * @return an object with fields "type" and "value".
     */
    protected function form_res($type, $value) {
        $result = new stdClass();
        $result->type = $type;
        $result->value = $value;
        return $result;
    }

    /**
     * Forms an interval from sequences like a-z, 0-9, etc. If a string contains
     * something like "x-z" in the end, it will be converted to "xyz".
     * @param cc a string containing characters and possible "x-y" sequence in the end.
     * @param cclength length of the cc - this may be a utf-8 string.
     */
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
                $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar, $this->yychar + $this->yylength() - 1, '');
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

    /**
     * Adds a named subpattern to the map.
     * @param name subpattern to be mapped.
     * @return number of this named subpattern.
     */
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

    /**
     * Calculates the character for a \cx sequence.
     * @param x substring of a \cx sequence.
     * @return character corresponding to the given sequence.
     */
    public function calculate_cx($x) {
        $code = qtype_preg_unicode::ord($x);
        if ($code > 127) {
            throw new Exception('The code of \'' . $x . '\' is ' . $code . ', but should be <= 127.');
        }
        $code ^= 0x40;
        return qtype_preg_unicode::code2utf8($code);
    }

    /**
     * Adds a flag to the lexer's charset when lexer is in the CHARCLASS state.
     * @param userinscription a string typed by user and consumed by lexer.
     * @param type type of the flag, should be a constant of preg_leaf_charset.
     * @param data can contain either subtype of a flag or characters for a charset.
     * @param negative is this flag negative.
     */
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
            $flag->set_data($type, $data);
            $flag->negative = $negative;
            $this->cc->flags[] = array($flag);
            $this->ccgotflag = true;
            break;
        }
    }

    /**
     * Returns a unicode property flag type corresponding to the consumed string.
     * @param str string consumed by the lexer, defines the property itself.
     * @return a constant of preg_leaf_charset if this property is known, null otherwise.
     */
    public function get_uprop_flag($str) {
        if (array_key_exists($str, self::$upropflags)) {
            return self::$upropflags[$str];
        } else {
            $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $this->yychar, $this->yychar + $this->yylength() - 1, $str);
            return null;
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

<YYINITIAL> "?"("?"|"+")? {
    $greed = $this->yylength() === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_finite_quant', null, null, 0, 1, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> "*"("?"|"+")? {
    $greed = $this->yylength() === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_infinite_quant', null, null, 0, null, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> "+"("?"|"+")? {
    $greed = $this->yylength() === 1;
    $lazy = !$greed && qtype_preg_unicode::substr($this->yytext(), 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    $res = $this->form_res(preg_parser_yyParser::QUANT, $this->form_node($this->yytext(), 'preg_node_infinite_quant', null, null, 1, null, $lazy, $greed, $possessive));
    return $res;
}
<YYINITIAL> "{"[0-9]+","[0-9]+"}"("?"|"+")? {
    $text = $this->yytext();
    $textlen = $this->yylength();
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

<YYINITIAL> "{"[0-9]+",}"("?"|"+")? {
    $text = $this->yytext();
    $textlen = $this->yylength();
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
<YYINITIAL> "{,"[0-9]+"}"("?"|"+")? {
    $text = $this->yytext();
    $textlen = $this->yylength();
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
<YYINITIAL> "{"[0-9]+"}"("?"|"+")? {
    $text = $this->yytext();
    $textlen = $this->yylength();
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
<YYINITIAL> "[^"|"[" {
    $this->cc = new preg_leaf_charset;
    $this->cc->indfirst = $this->yychar;
    $this->cc->userinscription = array();
    $this->cc->negative = $this->yylength() === 2;
    $this->cccharnumber = 0;
    $this->ccset = '';
    $this->yybegin(self::CHARCLASS);
}
<YYINITIAL> "(" {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->yytext(), $this->lastsubpatt));
    return $res;
}
<YYINITIAL> ")" {
    $this->pop_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CLOSEBRACK, new preg_lexem(0, $this->yychar, $this->yychar, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?#"[^)]*")" {       // Comment.
    return $this->nextToken();
}
<YYINITIAL> "(*"{NOTSPECIAL}*")" {
    // TODO
    return $this->nextToken();
}
<YYINITIAL> "(?>" {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $this->lastsubpatt));
    return $res;
}
<YYINITIAL> "(?<"{NOTSPECIAL}+">" {    // Named subpattern (?<name>...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> "(?'"{NOTSPECIAL}+"'" {    // Named subpattern (?'name'...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> "(?P<"{NOTSPECIAL}+">" {   // Named subpattern (?P<name>...).
    $this->push_opt_lvl();
    $num = $this->map_subpattern(qtype_preg_unicode::substr($this->yytext(), 4, $this->yylength() - 5));
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem_subpatt(preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), $num));
    return $res;
}
<YYINITIAL> "(?:" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?|" {
    $this->push_opt_lvl($this->lastsubpatt);    // Save the top-level subpattern number.
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?(?=" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?(?!" {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?(?<=" {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?(?<!" {
    $this->push_opt_lvl();
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::CONDSUBPATT, new preg_lexem(preg_node_cond_subpatt::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?=" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?!" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?<=" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?<!" {
    $this->push_opt_lvl();
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem(preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "." {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> {NOTSPECIAL} {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $this->yytext()));
    return $res;
}
<YYINITIAL> "|" {
    // Reset subpattern numeration inside a (?|...) group.
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum != -1) {
        $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;
    }
    $res = $this->form_res(preg_parser_yyParser::ALT, new preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> \\{SPECIAL} {
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
<YYINITIAL> "\g"[0-9][0-9]? {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, qtype_preg_unicode::substr($this->yytext(), 2)));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> ("\g{-"|"\g{")[0-9][0-9]?"}" {
    $num = (int)qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    // Is it a relative backreference? Is so, convert it to an absolute one.
    if ($num < 0) {
        $num = $this->lastsubpatt + $num + 1;
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $num));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> "\g{"{NOTSPECIAL}+"}" {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> "\k{"{NOTSPECIAL}+"}" {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> "\k'"{NOTSPECIAL}+"'" {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> "\k<"{NOTSPECIAL}+">" {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_backref', null, $str));
    $res->value->matcher = $this->matcher;
    $this->backrefsexist = true;
    return $res;
}
<YYINITIAL> "(?P="{NOTSPECIAL}+")" {    // Named backreference.
    $str = qtype_preg_unicode::substr($this->yytext(), 4, $this->yylength() - 5);
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
<YYINITIAL> "\a" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x07)));
    return $res;
}
<YYINITIAL> "\c". {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $this->calculate_cx(qtype_preg_unicode::substr($this->yytext(), 2))));
    return $res;
}
<YYINITIAL> "\e" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x1B)));
    return $res;
}
<YYINITIAL> "\f" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0C)));
    return $res;
}
<YYINITIAL> "\n" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0A)));
    return $res;
}
<YYINITIAL> ("\p"|"\P"). {
    $str = qtype_preg_unicode::substr($this->yytext(), 2);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype !== null) {
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::UPROP, $subtype, null, null, false, false, false, $negative));
    } else {
        $res = null;
    }
    return $res;
}
<YYINITIAL> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str !== 'Any') {
        $subtype = $this->get_uprop_flag($str);
        if ($subtype !== null) {
            $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::UPROP, $subtype, null, null, false, false, false, $negative));
        } else {
            $res = null;
        }
    } else {
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN, null, null, false, false, false, $negative));
    }
    return $res;
}
<YYINITIAL> "\r" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0D)));
    return $res;
}
<YYINITIAL> "\t" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x09)));
    return $res;
}
<YYINITIAL> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    if ($this->yylength() < 3) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $str));
    return $res;
}
<YYINITIAL> "\x{"[0-9a-fA-F]+"}" {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, qtype_preg_unicode::code2utf8(hexdec($str))));
    return $res;
}
<YYINITIAL> "\d"|"\D" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::DIGIT, null, null, false, false, false, ($this->yytext() === '\D')));
    return $res;
}
<YYINITIAL> "\h"|"\H" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::HSPACE, null, null, false, false, false, ($this->yytext() === '\H')));
    return $res;
}
<YYINITIAL> "\s"|"\S" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::SPACE, null, null, false, false, false, ($this->yytext() === '\S')));
    return $res;
}
<YYINITIAL> "\v"|"\V" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::VSPACE, null, null, false, false, false, ($this->yytext() === '\V')));
    return $res;
}
<YYINITIAL> "\w"|"\W" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::WORDCHAR, null, null, false, false, false, ($this->yytext() === '\W')));
    return $res;
}
<YYINITIAL> "\C" {
    // TODO: matches any one data unit. For now implemented the same way as dot.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> "\u"([0-9a-fA-F][0-9a-fA-F][0-9a-fA-F][0-9a-fA-F])? {
    if ($this->yylength() === 2) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::SET, $str));
    return $res;
}
<YYINITIAL> "\N" {
    // TODO: matches any character except new line characters. For now, the same as dot.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node(array($this->yytext()), 'preg_leaf_charset', preg_charset_flag::FLAG, preg_charset_flag::PRIN));
    return $res;
}
<YYINITIAL> "\K" {
    // TODO: reset start of match.
    throw new Exception('\K is not implemented yet');
}
<YYINITIAL> "\R" {
    // TODO: matches new line unicode sequences.
    // \B, \R, and \X are not special inside a character class.
    throw new Exception('\R is not implemented yet');
}
<YYINITIAL> "\X" {
    // TODO: matches  any number of Unicode characters that form an extended Unicode sequence.
    // \B, \R, and \X are not special inside a character class.
    throw new Exception('\R is not implemented yet');
}
<YYINITIAL> "\b"|"\B" {
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_WORDBREAK));
    $res->value->negative = ($this->yytext() === '\B');
    return $res;
}
<YYINITIAL> "\A" {
    // TODO: matches at the start of the subject. For now the same as ^.
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_CIRCUMFLEX));
    return $res;
}
<YYINITIAL> "\z"|"\Z" {
    // TODO: matches only at the end of the subject | matches at the end of the subject also matches before a newline at the end of the subject. For now the same as $.
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_assert', preg_leaf_assert::SUBTYPE_DOLLAR));
    return $res;
}
<YYINITIAL> "\G" {
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
<YYINITIAL> "(?i)" {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
}
<YYINITIAL> "(?-i)" {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->mod_top_opt(new qtype_preg_string(''), new qtype_preg_string('i'));
}
<YYINITIAL> "(?i:" {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_preg_string('i'), new qtype_preg_string(''));
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?-i:" {/*TODO: refactor this rule at adding support other modifier*/
    $text = $this->yytext();
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_preg_string(''), new qtype_preg_string('-i'));
    $res = $this->form_res(preg_parser_yyParser::OPENBRACK, new preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
    return $res;
}
<YYINITIAL> "(?"("R"|[0-9]+)")" {
    $res = $this->form_res(preg_parser_yyPARSER::PARSLEAF, $this->form_node($this->yytext(), 'preg_leaf_recursion', null, $this->yytext()));
    return $res;
}
<YYINITIAL> "\Q".*"\E"|"\Q".* {
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
<CHARCLASS> "[:alnum:]"|"[^:alnum:]" {
    $negative = ($this->yytext() === '[^:alnum:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALNUM, $negative);
}
<CHARCLASS> "[:alpha:]"|"[^:alpha:]" {
    $negative = ($this->yytext() === '[^:alpha:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ALPHA, $negative);
}
<CHARCLASS> "[:ascii:]"|"[^:ascii:]" {
    $negative = ($this->yytext() === '[^:ascii:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::ASCII, $negative);
}
<CHARCLASS> "\h"|"\H"|"[:blank:]"|"[^:blank:]" {
    $negative = ($this->yytext() === '\H' || $this->yytext() === '[^:blank:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::HSPACE, $negative);
}
<CHARCLASS> "[:cntrl:]"|"[^:cntrl:]" {
    $negative = ($this->yytext() === '[^:cntrl:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::CNTRL, $negative);
}
<CHARCLASS> "\d"|"\D"|"[:digit:]"|"[^:digit:]" {
    $negative = ($this->yytext() === '\D' || $this->yytext() === '[^:digit:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::DIGIT, $negative);
}
<CHARCLASS> "[:graph:]"|"[^:graph:]" {
    $negative = ($this->yytext() === '[^:graph:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::GRAPH, $negative);
}
<CHARCLASS> "[:lower:]"|"[^:lower:]" {
    $negative = ($this->yytext() === '[^:lower:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::LOWER, $negative);
}
<CHARCLASS> "[:print:]"|"[^:print:]" {
    $negative = ($this->yytext() === '[^:print:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PRIN, $negative);
}
<CHARCLASS> "[:punct:]"|"[^:punct:]" {
    $negative = ($this->yytext() === '[^:punct:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PUNCT, $negative);
}
<CHARCLASS> "\s"|"\S"|"[:space:]"|"[^:space:]"  {
    $negative = ($this->yytext() === '\S' || $this->yytext() === '[^:space:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::SPACE, $negative);
}
<CHARCLASS> "[:upper:]"|"[^:upper:]" {
    $negative = ($this->yytext() === '[^:upper:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::UPPER, $negative);
}
<CHARCLASS> "\w"|"\W"|"[:word:]"|"[^:word:]" {
    $negative = ($this->yytext() === '\W' || $this->yytext() === '[^:word:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::WORDCHAR, $negative);
}
<CHARCLASS> "[:xdigit:]"|"[^:xdigit:]" {
    $negative = ($this->yytext() === '[^:xdigit:]');
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::XDIGIT, $negative);
}
<CHARCLASS> "[:"[^\]]*":]"|"[^:"[^\]]*":]" {
    $this->errors[] = new preg_lexem(preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext());
}
<CHARCLASS> ("\p"|"\P"). {
    $str = qtype_preg_unicode::substr($this->yytext(), 2);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype !== null) {
        $this->add_flag_to_charset($this->yytext(), preg_charset_flag::UPROP, $subtype, $negative);
    }
}
<CHARCLASS> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $negative = (qtype_preg_unicode::substr($this->yytext(), 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str !== 'Any') {
        $subtype = $this->get_uprop_flag($str);
        if ($subtype !== null) {
            $this->add_flag_to_charset($this->yytext(), preg_charset_flag::UPROP, $subtype, $negative);
        }
    } else {
        $this->add_flag_to_charset($this->yytext(), preg_charset_flag::FLAG, preg_charset_flag::PRIN, $negative);
    }
}
<CHARCLASS> \\\\ {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '\\');
}
<CHARCLASS> "\[" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '[');
}
<CHARCLASS> "\]" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, ']');
}
<CHARCLASS> \\0[0-7][0-7][0-7]? {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($this->yytext(), 1))));
}
<CHARCLASS> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    if ($this->yylength() < 3) {
        $str = qtype_preg_unicode::substr($this->yytext(), 1);
    } else {
        $str = qtype_preg_unicode::code2utf8(hexdec(qtype_preg_unicode::substr($this->yytext(), 2)));
    }
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $str);
}
<CHARCLASS> "\x{"[0-9a-fA-F]+"}" {
    $str = qtype_preg_unicode::substr($this->yytext(), 3, $this->yylength() - 4);
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(hexdec($str)));
}
<CHARCLASS> "\a" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x07));
}
<CHARCLASS> "\c". {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $this->calculate_cx(qtype_preg_unicode::substr($this->yytext(), 2)));
}
<CHARCLASS> "\e" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x1B));
}
<CHARCLASS> "\f" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0C));
}
<CHARCLASS> "\n" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0A));
}
<CHARCLASS> "\r" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x0D));
}
<CHARCLASS> "\t" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::code2utf8(0x09));
}
<CHARCLASS> "^" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '^');
}
<CHARCLASS> "-" {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, '-');
}
<CHARCLASS> \\. {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, qtype_preg_unicode::substr($this->yytext(), 1, 1));
}
<CHARCLASS> [^\]] {
    $this->add_flag_to_charset($this->yytext(), preg_charset_flag::SET, $this->yytext());
}
<CHARCLASS> "]" {
    if (count($this->errors) === 0) {
        $this->cc->indlast = $this->yychar;
        $this->cc->israngecalculated = false;
        if ($this->ccset !== '') {
            $flag = new preg_charset_flag;
            $flag->set_data(preg_charset_flag::SET, new qtype_preg_string($this->ccset));
            $this->cc->flags[] = array($flag);
        }
        $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->cc);
    } else {
        $res = null;
    }
    $this->cc = null;
    $this->yybegin(self::YYINITIAL);
    return $res;
}
