<?php

require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

%%
%class qtype_preg_lexer
%function nextToken
%char
%unicode
%state CHARSET
NOTSPECIAL = [^\\^$.\[\]|()?*+{}]                       // Characters that should be escaped.
MODIFIER   = [^"(|)<>#':=!PCR"0-9]                      // Excluding reserved (?... sequences, returning error if there is something weird.
ALNUM      = [^" !\"#$%&'()*+,-./:;<=>?[\\]^`{|}~"]     // Used in subpattern\backreference names.
ESCAPABLE  = [^0-9a-zA-Z]
%init{
    $this->matcher                 = null;
    $this->errors                  = array();
    $this->lastsubpatt             = 0;
    $this->maxsubpatt              = 0;
    $this->subpatternmap           = array();
    $this->backrefs                = array();
    $this->optstack                = array();
    $this->optstack[0]             = new stdClass;
    $this->optstack[0]->i          = false;
    $this->optstack[0]->subpattnum = -1;
    $this->optstack[0]->parennum   = -1;
    $this->optcount                = 1;
    $this->charset                 = null;
    $this->charsetcount            = 0;
    $this->charsetset              = '';
    $this->charsetuserinscription  = '';

%init}
%eof{
    // End of the expression inside a character class.
    if ($this->charset !== null) {
        $this->errors[] = $this->form_error($this->charsetuserinscription, qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET, $this->charset->indfirst, $this->yychar - 1);
    }
    // Check for backreferences to unexisting subpatterns.
    if (count($this->backrefs) > 0) {
        $maxbackrefnumber = -1;
        foreach ($this->backrefs as $leaf) {
            $number = $leaf->number;
            $error = false;
            if ((is_int($number) && $number > $this->maxsubpatt) || (is_string($number) && !array_key_exists($number, $this->subpatternmap))) {
                $this->errors[] = $this->form_error($leaf->userinscription, qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBPATT, $leaf->indfirst, $leaf->indlast, $leaf->number);
            }
        }
    }
%eof}
%{
    public $matcher;
    protected $errors;
    protected $lastsubpatt;
    protected $maxsubpatt;
    protected $subpatternmap;
    protected $backrefs;
    protected $optstack;
    protected $optcount;
    protected $charset;
    protected $charsetcount;
    protected $charsetset;
    protected $charsetuserinscription;
    protected static $upropflags = array('C'                      => qtype_preg_charset_flag::UPROPC,
                                         'Cc'                     => qtype_preg_charset_flag::UPROPCC,
                                         'Cf'                     => qtype_preg_charset_flag::UPROPCF,
                                         'Cn'                     => qtype_preg_charset_flag::UPROPCN,
                                         'Co'                     => qtype_preg_charset_flag::UPROPCO,
                                         'Cs'                     => qtype_preg_charset_flag::UPROPCS,
                                         'L'                      => qtype_preg_charset_flag::UPROPL,
                                         'Ll'                     => qtype_preg_charset_flag::UPROPLL,
                                         'Lm'                     => qtype_preg_charset_flag::UPROPLM,
                                         'Lo'                     => qtype_preg_charset_flag::UPROPLO,
                                         'Lt'                     => qtype_preg_charset_flag::UPROPLT,
                                         'Lu'                     => qtype_preg_charset_flag::UPROPLU,
                                         'M'                      => qtype_preg_charset_flag::UPROPM,
                                         'Mc'                     => qtype_preg_charset_flag::UPROPMC,
                                         'Me'                     => qtype_preg_charset_flag::UPROPME,
                                         'Mn'                     => qtype_preg_charset_flag::UPROPMN,
                                         'N'                      => qtype_preg_charset_flag::UPROPN,
                                         'Nd'                     => qtype_preg_charset_flag::UPROPND,
                                         'Nl'                     => qtype_preg_charset_flag::UPROPNL,
                                         'No'                     => qtype_preg_charset_flag::UPROPNO,
                                         'P'                      => qtype_preg_charset_flag::UPROPP,
                                         'Pc'                     => qtype_preg_charset_flag::UPROPPC,
                                         'Pd'                     => qtype_preg_charset_flag::UPROPPD,
                                         'Pe'                     => qtype_preg_charset_flag::UPROPPE,
                                         'Pf'                     => qtype_preg_charset_flag::UPROPPF,
                                         'Pi'                     => qtype_preg_charset_flag::UPROPPI,
                                         'Po'                     => qtype_preg_charset_flag::UPROPPO,
                                         'Ps'                     => qtype_preg_charset_flag::UPROPPS,
                                         'S'                      => qtype_preg_charset_flag::UPROPS,
                                         'Sc'                     => qtype_preg_charset_flag::UPROPSC,
                                         'Sk'                     => qtype_preg_charset_flag::UPROPSK,
                                         'Sm'                     => qtype_preg_charset_flag::UPROPSM,
                                         'So'                     => qtype_preg_charset_flag::UPROPSO,
                                         'Z'                      => qtype_preg_charset_flag::UPROPZ,
                                         'Zl'                     => qtype_preg_charset_flag::UPROPZL,
                                         'Zp'                     => qtype_preg_charset_flag::UPROPZP,
                                         'Zs'                     => qtype_preg_charset_flag::UPROPZS,
                                         'Xan'                    => qtype_preg_charset_flag::UPROPXAN,
                                         'Xps'                    => qtype_preg_charset_flag::UPROPXPS,
                                         'Xsp'                    => qtype_preg_charset_flag::UPROPXSP,
                                         'Xwd'                    => qtype_preg_charset_flag::UPROPXWD,
                                         'Arabic'                 => qtype_preg_charset_flag::ARABIC,
                                         'Armenian'               => qtype_preg_charset_flag::ARMENIAN,
                                         'Avestan'                => qtype_preg_charset_flag::AVESTAN,
                                         'Balinese'               => qtype_preg_charset_flag::BALINESE,
                                         'Bamum'                  => qtype_preg_charset_flag::BAMUM,
                                         'Bengali'                => qtype_preg_charset_flag::BENGALI,
                                         'Bopomofo'               => qtype_preg_charset_flag::BOPOMOFO,
                                         'Braille'                => qtype_preg_charset_flag::BRAILLE,
                                         'Buginese'               => qtype_preg_charset_flag::BUGINESE,
                                         'Buhid'                  => qtype_preg_charset_flag::BUHID,
                                         'Canadian_Aboriginal'    => qtype_preg_charset_flag::CANADIAN_ABORIGINAL,
                                         'Carian'                 => qtype_preg_charset_flag::CARIAN,
                                         'Cham'                   => qtype_preg_charset_flag::CHAM,
                                         'Cherokee'               => qtype_preg_charset_flag::CHEROKEE,
                                         'Common'                 => qtype_preg_charset_flag::COMMON,
                                         'Coptic'                 => qtype_preg_charset_flag::COPTIC,
                                         'Cuneiform'              => qtype_preg_charset_flag::CUNEIFORM,
                                         'Cypriot'                => qtype_preg_charset_flag::CYPRIOT,
                                         'Cyrillic'               => qtype_preg_charset_flag::CYRILLIC,
                                         'Deseret'                => qtype_preg_charset_flag::DESERET,
                                         'Devanagari'             => qtype_preg_charset_flag::DEVANAGARI,
                                         'Egyptian_Hieroglyphs'   => qtype_preg_charset_flag::EGYPTIAN_HIEROGLYPHS,
                                         'Ethiopic'               => qtype_preg_charset_flag::ETHIOPIC,
                                         'Georgian'               => qtype_preg_charset_flag::GEORGIAN,
                                         'Glagolitic'             => qtype_preg_charset_flag::GLAGOLITIC,
                                         'Gothic'                 => qtype_preg_charset_flag::GOTHIC,
                                         'Greek'                  => qtype_preg_charset_flag::GREEK,
                                         'Gujarati'               => qtype_preg_charset_flag::GUJARATI,
                                         'Gurmukhi'               => qtype_preg_charset_flag::GURMUKHI,
                                         'Han'                    => qtype_preg_charset_flag::HAN,
                                         'Hangul'                 => qtype_preg_charset_flag::HANGUL,
                                         'Hanunoo'                => qtype_preg_charset_flag::HANUNOO,
                                         'Hebrew'                 => qtype_preg_charset_flag::HEBREW,
                                         'Hiragana'               => qtype_preg_charset_flag::HIRAGANA,
                                         'Imperial_Aramaic'       => qtype_preg_charset_flag::IMPERIAL_ARAMAIC,
                                         'Inherited'              => qtype_preg_charset_flag::INHERITED,
                                         'Inscriptional_Pahlavi'  => qtype_preg_charset_flag::INSCRIPTIONAL_PAHLAVI,
                                         'Inscriptional_Parthian' => qtype_preg_charset_flag::INSCRIPTIONAL_PARTHIAN,
                                         'Javanese'               => qtype_preg_charset_flag::JAVANESE,
                                         'Kaithi'                 => qtype_preg_charset_flag::KAITHI,
                                         'Kannada'                => qtype_preg_charset_flag::KANNADA,
                                         'Katakana'               => qtype_preg_charset_flag::KATAKANA,
                                         'Kayah_Li'               => qtype_preg_charset_flag::KAYAH_LI,
                                         'Kharoshthi'             => qtype_preg_charset_flag::KHAROSHTHI,
                                         'Khmer'                  => qtype_preg_charset_flag::KHMER,
                                         'Lao'                    => qtype_preg_charset_flag::LAO,
                                         'Latin'                  => qtype_preg_charset_flag::LATIN,
                                         'Lepcha'                 => qtype_preg_charset_flag::LEPCHA,
                                         'Limbu'                  => qtype_preg_charset_flag::LIMBU,
                                         'Linear_B'               => qtype_preg_charset_flag::LINEAR_B,
                                         'Lisu'                   => qtype_preg_charset_flag::LISU,
                                         'Lycian'                 => qtype_preg_charset_flag::LYCIAN,
                                         'Lydian'                 => qtype_preg_charset_flag::LYDIAN,
                                         'Malayalam'              => qtype_preg_charset_flag::MALAYALAM,
                                         'Meetei_Mayek'           => qtype_preg_charset_flag::MEETEI_MAYEK,
                                         'Mongolian'              => qtype_preg_charset_flag::MONGOLIAN,
                                         'Myanmar'                => qtype_preg_charset_flag::MYANMAR,
                                         'New_Tai_Lue'            => qtype_preg_charset_flag::NEW_TAI_LUE,
                                         'Nko'                    => qtype_preg_charset_flag::NKO,
                                         'Ogham'                  => qtype_preg_charset_flag::OGHAM,
                                         'Old_Italic'             => qtype_preg_charset_flag::OLD_ITALIC,
                                         'Old_Persian'            => qtype_preg_charset_flag::OLD_PERSIAN,
                                         'Old_South_Arabian'      => qtype_preg_charset_flag::OLD_SOUTH_ARABIAN,
                                         'Old_Turkic'             => qtype_preg_charset_flag::OLD_TURKIC,
                                         'Ol_Chiki'               => qtype_preg_charset_flag::OL_CHIKI,
                                         'Oriya'                  => qtype_preg_charset_flag::ORIYA,
                                         'Osmanya'                => qtype_preg_charset_flag::OSMANYA,
                                         'Phags_Pa'               => qtype_preg_charset_flag::PHAGS_PA,
                                         'Phoenician'             => qtype_preg_charset_flag::PHOENICIAN,
                                         'Rejang'                 => qtype_preg_charset_flag::REJANG,
                                         'Runic'                  => qtype_preg_charset_flag::RUNIC,
                                         'Samaritan'              => qtype_preg_charset_flag::SAMARITAN,
                                         'Saurashtra'             => qtype_preg_charset_flag::SAURASHTRA,
                                         'Shavian'                => qtype_preg_charset_flag::SHAVIAN,
                                         'Sinhala'                => qtype_preg_charset_flag::SINHALA,
                                         'Sundanese'              => qtype_preg_charset_flag::SUNDANESE,
                                         'Syloti_Nagri'           => qtype_preg_charset_flag::SYLOTI_NAGRI,
                                         'Syriac'                 => qtype_preg_charset_flag::SYRIAC,
                                         'Tagalog'                => qtype_preg_charset_flag::TAGALOG,
                                         'Tagbanwa'               => qtype_preg_charset_flag::TAGBANWA,
                                         'Tai_Le'                 => qtype_preg_charset_flag::TAI_LE,
                                         'Tai_Tham'               => qtype_preg_charset_flag::TAI_THAM,
                                         'Tai_Viet'               => qtype_preg_charset_flag::TAI_VIET,
                                         'Tamil'                  => qtype_preg_charset_flag::TAMIL,
                                         'Telugu'                 => qtype_preg_charset_flag::TELUGU,
                                         'Thaana'                 => qtype_preg_charset_flag::THAANA,
                                         'Thai'                   => qtype_preg_charset_flag::THAI,
                                         'Tibetan'                => qtype_preg_charset_flag::TIBETAN,
                                         'Tifinagh'               => qtype_preg_charset_flag::TIFINAGH,
                                         'Ugaritic'               => qtype_preg_charset_flag::UGARITIC,
                                         'Vai'                    => qtype_preg_charset_flag::VAI,
                                         'Yi'                     => qtype_preg_charset_flag::YI
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

    public function get_backrefs() {
        return $this->backrefs;
    }

    public function mod_top_opt($set, $unset) {
        $allowed = 'i';
        $wrongfound = '';
        // Some sanity checks.
        for ($i = 0; $i < $set->length(); $i++) {
            $modname = $set[$i];
            if (qtype_poasquestion_string::strpos($allowed, $modname) === false) {
                $wrongfound .= $modname;
            }
        }
        for ($i = 0; $i < $unset->length(); $i++) {
            $modname = $unset[$i];
            if (qtype_poasquestion_string::strpos($allowed, $modname) === false && qtype_poasquestion_string::strpos($wrongfound, $modname) === false) {
                $wrongfound .= $modname;
            }
        }
        if ($wrongfound !== '') {
            $this->errors[] = $this->form_error($wrongfound, qtype_preg_node_error::SUBTYPE_UNKNOWN_MODIFIER, $this->yychar, $this->yychar + $this->yylength() - 1, $wrongfound);
        }
        $setunseterror = false;
        for ($i = 0; $i < $set->length(); $i++) {
            $modname = $set[$i];
            if ($unset->contains($modname) !== false && qtype_poasquestion_string::strpos($allowed, $modname) !== false) {
                // Setting and unsetting modifier at the same time is error.
                $this->errors[] = $this->form_error($modname, qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $this->yychar, $this->yychar + $this->yylength() - 1, $modname);
                $setunseterror = true;
            }
        }
        // If errors don't exist, set and unset local modifiers.
        if (!$setunseterror) {
            for ($i = 0; $i < $set->length(); $i++) {
                $modname = $set[$i];
                if (qtype_poasquestion_string::strpos($allowed, $modname) !== false) {
                    $this->optstack[$this->optcount - 1]->$modname = true;
                }
            }
            for ($i = 0; $i < $unset->length(); $i++) {
                $modname = $unset[$i];
                if (qtype_poasquestion_string::strpos($allowed, $modname) !== false) {
                    $this->optstack[$this->optcount - 1]->$modname = false;
                }
            }
        }
    }

    /**
     * Returns an error node.
     */
    public function form_error($userinscription, $subtype, $indfirst = -1, $indlast = -1, $addinfo = null) {
        $error = new qtype_preg_node_error();
        $error->subtype = $subtype;
        $error->addinfo = $addinfo;
        $this->set_node_source_info($error, $userinscription, $indfirst, $indlast);
        return $error;
    }

    /**
     * Sets user insctiption and indexes for the given node.
     */
    protected function set_node_source_info(&$node, $userinscription, $indfirst, $indlast) {
        $node->userinscription = $userinscription;
        $node->indfirst = $indfirst;
        $node->indlast = $indlast;
        // Set i modifier for leafs.
        if (is_a($node, 'qtype_preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->i) {
            $node->caseinsensitive = true;
        }
    }

    /**
     * Forms a result to return from the lexer.
     * @param type is this a leaf or a node, should be a constant of preg_parser_yyParser.
     * @param value can be either a qtype_preg_node or a qtype_preg_lexem.
     * @return an object with fields "type" and "value".
     */
    protected function form_res($type, $value) {
        $result = new stdClass();
        $result->type = $type;
        $result->value = $value;
        return $result;
    }

    /**
     * Returns a quantifier token.
     */
    protected function form_quant($text, $pos, $length, $infinite, $leftborder, $rightborder, $lazy, $greed, $possessive) {
        if ($infinite) {
            $node = new qtype_preg_node_infinite_quant();
        } else {
            $node = new qtype_preg_node_finite_quant();
            $node->rightborder = $rightborder;
        }
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        $node->leftborder = $leftborder;
        $node->lazy = $lazy;
        $node->greed = $greed;
        $node->possessive = $possessive;
        if (!$infinite && $leftborder > $rightborder) {
            $rightoffset = 0;
            $greed || $rightoffset++;
            $node->error = $this->form_error($leftborder . ',' . $rightborder, qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE, $pos + 1, $pos + $length - 2 - $rightoffset);
        }
        return $this->form_res(preg_parser_yyParser::QUANT, $node);
    }

    /**
     * Returns a control sequence token.
     */
    protected function form_control($text, $pos, $length) {
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== ')') {
            // return error - paren ) missing;
        }
        $node = new qtype_preg_leaf_control();
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        if ($text === '(*ACCEPT)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_ACCEPT;
        } else if ($text === '(*FAIL)' || $text === '(*F)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_FAIL;
        } else if ($text === '(*COMMIT)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_COMMIT;
        } else if ($text === '(*THEN)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_THEN;
        } else if ($text === '(*SKIP)' || $text === '(*SKIP:)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_SKIP;
        } else if ($text === '(*PRUNE)' || $text === '(*PRUNE:)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_PRUNE;
        } else if ($text === '(*CR)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_CR;
        } else if ($text === '(*LF)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_LF;
        } else if ($text === '(*CRLF)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_CRLF;
        } else if ($text === '(*ANYCRLF)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_ANYCRLF;
        } else if ($text === '(*ANY)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_ANY;
        } else if ($text === '(*BSR_ANYCRLF)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_BSR_ANYCRLF;
        } else if ($text === '(*BSR_UNICODE)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_BSR_UNICODE;
        } else if ($text === '(*NO_START_OPT)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_NO_START_OPT;
        } else if ($text === '(*UTF8)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_UTF8;
        } else if ($text === '(*UTF16)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_UTF16;
        } else if ($text === '(*UCP)') {
            $node->subtype = qtype_preg_leaf_control::SUBTYPE_UCP;
        } else {
            // There is a parameter or error,.
            $delimpos = qtype_poasquestion_string::strpos($text, ':');
            if ($delimpos !== false) {
                $subtype = qtype_poasquestion_string::substr($text, 2, $delimpos - 2);
                $name = qtype_poasquestion_string::substr($text, $delimpos + 1, $length - $delimpos - 2);
                if ($name === '') {
                    $node->error = $this->form_error($text, qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, $delimpos, $pos + $length - 1, $text);
                } else {
                    $node->name = $name;
                }

                if ($subtype === 'MARK' || $delimpos === 2) {
                    $node->subtype = qtype_preg_leaf_control::SUBTYPE_MARK_NAME;
                } else if ($subtype === 'PRUNE') {
                    $node->subtype = qtype_preg_leaf_control::SUBTYPE_MARK_NAME;
                    $node2 = new qtype_preg_leaf_control();
                    $this->set_node_source_info($node2, $text, $pos, $pos + $length - 1);
                    $node2->subtype = qtype_preg_leaf_control::SUBTYPE_PRUNE;
                    return array($this->form_res(preg_parser_yyParser::PARSLEAF, $node),
                                 $this->form_res(preg_parser_yyParser::PARSLEAF, $node2));
                } else if ($subtype === 'SKIP') {
                    $node->subtype = qtype_preg_leaf_control::SUBTYPE_SKIP_NAME;
                } else if ($subtype === 'THEN') {
                    $node->subtype = qtype_preg_leaf_control::SUBTYPE_MARK_NAME;
                    $node2 = new qtype_preg_leaf_control();
                    $this->set_node_source_info($node2, $text, $pos, $pos + $length - 1);
                    $node2->subtype = qtype_preg_leaf_control::SUBTYPE_THEN;
                    return array($this->form_res(preg_parser_yyParser::PARSLEAF, $node),
                                 $this->form_res(preg_parser_yyParser::PARSLEAF, $node2));
                }

            } else {
                $node->error = $this->form_error($text, qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE, $pos, $pos + $length - 1, $text);
            }
        }
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a named subpattern token.
     */
    protected function form_named_subpatt($text, $pos, $length, $namestartpos, $closetype) {
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== $closetype) {
            // Missing ending character.
            return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING, $pos, $pos + $length - 1, $text));
        } else {
            $this->push_opt_lvl();
            $name = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - 1);
            if ($name === '') {
                // Name is empty.
                return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, $pos, $pos + $length - 1, $text));
            } else {
                $num = (int)$this->map_subpattern($name);
                $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
                return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_SUBPATT, $pos, $pos + $length - 1, $text, $num));
            }
        }
    }

    /**
     * Returns a conditional subpattern token.
     */
    protected function form_cond_subpatt($text, $pos, $length, $subtype, $ending = '', $numeric = true, $namestartpos = 0) {
        $this->push_opt_lvl();
        if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
            $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
            $this->push_opt_lvl();
            return $this->form_res(preg_parser_yyParser::CONDSUBPATT, new qtype_preg_lexem($subtype, $pos, $pos + $length - 1, $text));
        } else {
            $endlength = strlen($ending);
            if (qtype_poasquestion_string::substr($text, $length - $endlength) !== $ending) {
                // Unclosed condition.
                return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING, $pos, $pos + $length - 1));
            }

            if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION) {
                $tmp = qtype_poasquestion_string::substr($text, 4, 1);
                $secondnode = new qtype_preg_lexem(null, -1, -1, '');
                if ($tmp === '&') {
                    // (?(R&
                    $name = qtype_poasquestion_string::substr($text, 5, $length - 6);
                    if ($name === '') {
                        $secondnode = $this->form_error($text, qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, $pos, $pos + $length - 1);
                    }
                    $data = $name;
                } else {
                    // (?(Rnumber)
                    $tmp = qtype_poasquestion_string::substr($text, 4, $length - 5);
                    if ($tmp !== '' && !ctype_digit($tmp)) {
                        // Error: digits expected.
                        $secondnode = $this->form_error($text, qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER, $pos, $pos + $length - 1);
                        $data = 0;
                    } else {
                        $data = (int)$tmp;
                    }
                }
                return array($this->form_res(preg_parser_yyParser::CONDSUBPATT, new qtype_preg_lexem_subpatt(qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION, $pos, $pos + $length - 1, $text, $data)),
                             $this->form_res(preg_parser_yyParser::PARSLEAF, $secondnode),
                             $this->form_res(preg_parser_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, '')));
            } else if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE) {
                return array($this->form_res(preg_parser_yyParser::CONDSUBPATT, new qtype_preg_lexem(qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE, $pos, $pos + $length - 1, $text)),
                             $this->form_res(preg_parser_yyParser::PARSLEAF, new qtype_preg_lexem(null, -1, -1, '')),
                             $this->form_res(preg_parser_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, '')));
            } else if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT) {
                if ($numeric) {
                    //(?("("+"
                    $str = qtype_poasquestion_string::substr($text, 3, $length - 4);
                    $tmp = qtype_poasquestion_string::substr($str, 0, 1);
                    $sign = 0;
                    $tmp === '+' && $sign++;
                    $tmp === '-' && $sign--;
                    if ($sign !== 0) {
                        $str = qtype_poasquestion_string::substr($str, 1);
                    }
                    $secondnode = new qtype_preg_lexem(null, -1, -1, '');
                    if ($str !== '' && !ctype_digit($str)) {
                        // Error: digits expected.
                        $secondnode = $this->form_error($text, qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER, $pos, $pos + $length - 1);
                        $num = 0;
                    } else {
                        if ($sign !== 0) {
                            $num = $sign * (int)$str + $this->lastsubpatt;
                            if ($sign < 0) {
                                $num++;
                            }
                        } else {
                            $num = (int)$str;
                        }
                        if ($num === 0) {
                            $secondnode = $this->form_error($text, qtype_preg_node_error::SUBTYPE_CONSUBPATT_ZERO_CONDITION, $pos, $pos + $length - 1, $num);
                        }
                    }
                    return array($this->form_res(preg_parser_yyParser::CONDSUBPATT, new qtype_preg_lexem_subpatt(qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, $pos, $pos + $length - 1, $text, $num)),
                                 $this->form_res(preg_parser_yyParser::PARSLEAF, $secondnode),
                                 $this->form_res(preg_parser_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, '')));
                } else {
                    $name = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - $endlength);
                    if ($name === '') {
                        $secondnode = $this->form_error($text, qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, $pos, $pos + $length - 1);
                    } else {
                        $secondnode = new qtype_preg_lexem(null, -1, -1, '');
                    }
                    $this->push_opt_lvl();
                    return array($this->form_res(preg_parser_yyParser::CONDSUBPATT, new qtype_preg_lexem_subpatt(qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, $pos, $pos + $length - 1, $text, $name)),
                                 $this->form_res(preg_parser_yyParser::PARSLEAF, $secondnode),
                                 $this->form_res(preg_parser_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, '')));
                }
            }


        }
    }

    /**
     * Returns a named backreference token.
     */
    protected function form_named_backref($text, $pos, $length, $namestartpos, $opentype, $closetype) {
        if (qtype_poasquestion_string::substr($text, $namestartpos - 1, 1) !== $opentype) {
            return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING, $pos, $pos + $length - 1, $opentype));
        }
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== $closetype) {
            return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING, $pos, $pos + $length - 1, $closetype));
        }
        $name = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - 1);
        if ($name === '') {
            return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, $pos, $pos + $length - 1, $text));
        } else {
            $node = new qtype_preg_leaf_backref();
            $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
            $node->number = $name;
            $node->matcher = $this->matcher;
            $this->backrefs[] = $node;
            if ($name === 0) {
                $node->error = $this->form_error($text, qtype_preg_node_error::SUBTYPE_BACKREF_TO_ZERO, $pos, $pos + $length - 1, $name);
            }
            return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
        }
    }

    /**
     * Returns a backreference token.
     */
    protected function form_backref($text, $pos, $length, $number) {
        $node = new qtype_preg_leaf_backref();
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        $node->number = $number;
        $node->matcher = $this->matcher;
        $this->backrefs[] = $node;
        if ($number === 0) {
            $node->error = $this->form_error($text, qtype_preg_node_error::SUBTYPE_BACKREF_TO_ZERO, $pos, $pos + $length - 1, $number);
        }
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a simple assertion token.
     */
    protected function form_simple_assertion($text, $pos, $length, $subtype, $negative = false) {
        $node = new qtype_preg_leaf_assert();
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        $node->subtype = $subtype;
        $node->negative = $negative;
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a character set token.
     */
    protected function form_charset($text, $pos, $length, $subtype, $data, $negative = false) {
        $node = new qtype_preg_leaf_charset();
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        $node->subtype = $subtype;
        $node->israngecalculated = false;
        if ($data !== null) {
            $flag = new qtype_preg_charset_flag;
            $flag->negative = $negative;
            if ($subtype === qtype_preg_charset_flag::SET) {
                $data = new qtype_poasquestion_string($data);
            }
            $flag->set_data($subtype, $data);
            $node->flags = array(array($flag));
        }
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a recursion token.
     */
    protected function form_recursion($text, $pos, $length, $number) {
        $node = new qtype_preg_leaf_recursion();
        $this->set_node_source_info($node, $text, $pos, $pos + $length - 1);
        if ($number[2] === 'R') {
            $node->number = 0;
        } else {
            $node->number = qtype_poasquestion_string::substr($number, 2, qtype_poasquestion_string::strlen($number) - 3);
        }
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $node);
    }

    /**
     * Forms an interval from sequences like a-z, 0-9, etc. If a string contains
     * something like "x-z" in the end, it will be converted to "xyz".
     * @param cc a string containing characters and possible "x-y" sequence in the end.
     * @param cclength length of the cc - this may be a utf-8 string.
     * @return mixed null if everything is correct, an error object otherwise.
     */
    protected function form_num_interval(&$cc, &$cclength) {
        // Check if there are enough characters in before.
        if ($cclength < 3 || qtype_poasquestion_string::substr($cc, $cclength - 2, 1) !== '-') {
            return;
        }
        $startchar = qtype_poasquestion_string::substr($cc, $cclength - 3, 1);
        $endchar = qtype_poasquestion_string::substr($cc, $cclength - 1, 1);
        if (qtype_poasquestion_string::ord($startchar) <= qtype_poasquestion_string::ord($endchar)) {
            // Modify userinscription;
            $userinscriptionlength = qtype_poasquestion_string::strlen($this->charset->userinscription[0]);
            $this->charset->userinscription[0] = qtype_poasquestion_string::substr($this->charset->userinscription[0], 0, $userinscriptionlength - 3);
            $this->charset->userinscription[] = $startchar . '-' . $endchar;
            // Replace last 3 characters by all the characters between them.
            $cc = qtype_poasquestion_string::substr($cc, 0, $cclength - 3);
            $cclength -= 3;
            $curord = qtype_poasquestion_string::ord($startchar);
            $endord = qtype_poasquestion_string::ord($endchar);
            while ($curord <= $endord) {
                $cc .= qtype_poasquestion_string::code2utf8($curord++);
                $cclength++;
            }
            return null;
        } else {
            // Delete last 3 characters.
            $cclength -= 3;
            $cc = qtype_poasquestion_string::substr($cc, 0, $cclength);
            // Return the error node.
            return $this->form_error($startchar . '-' . $endchar, qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE, $this->yychar - 2, $this->yychar + $this->yylength() - 1);
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
        } // Else the error will be found in parser, lexer does nothing for this error (closing unopened bracket).
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

    /**
     * Adds a named subpattern to the map.
     * @param name subpattern to be mapped.
     * @return number of this named subpattern.
     */
    protected function map_subpattern($name) {
        if (!array_key_exists($name, $this->subpatternmap)) {   // This subpattern does not exists.
            $num = ++$this->lastsubpatt;
            $this->subpatternmap[$name] = (int)$num;
        } else {                                                // Subpatterns with same names should have same numbers.
            $num = $this->subpatternmap[$name];
            // TODO check if we are inside a (?|...) group.
        }
        return $num;
    }

    /**
     * Calculates the character for a \cx sequence.
     * @param cx the sequence itself.
     * @return character corresponding to the given sequence.
     */
    protected function calculate_cx($cx, &$error) {
        $x = qtype_poasquestion_string::substr($cx, 2);
        $code = qtype_poasquestion_string::ord($x);
        if ($code > 127) {
            $error = $this->form_error($cx, qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII, $this->yychar, $this->yychar + $this->yylength() - 1, $cx);
        } else {
            $error = null;
        }
        $code ^= 0x40;
        return qtype_poasquestion_string::code2utf8($code);
    }

    /**
     * Adds a flag to the lexer's charset when lexer is in the CHARSET state.
     * @param userinscription a string typed by user and consumed by lexer.
     * @param type type of the flag, should be a constant of qtype_preg_leaf_charset.
     * @param data can contain either subtype of a flag or characters for a charset.
     * @param negative is this flag negative.
     */
    protected function add_flag_to_charset($userinscription = '', $type, $data, $negative = false) {
        $this->charsetuserinscription .= $userinscription;
        switch ($type) {
        case qtype_preg_charset_flag::SET:
            $this->charsetcount += qtype_poasquestion_string::strlen($data);
            $this->charsetset .= $data;
            $this->charset->userinscription[0] .= $userinscription;
            $error = $this->form_num_interval($this->charsetset, $this->charsetcount);
            if ($error !== null) {
                $this->charset->error[] = $error;
            }
            break;
        case qtype_preg_charset_flag::FLAG:
        case qtype_preg_charset_flag::UPROP:
            $flag = new qtype_preg_charset_flag;
            $flag->set_data($type, $data);
            $flag->negative = $negative;
            $this->charset->flags[] = array($flag);
            $this->charset->userinscription[] = $userinscription;
            break;
        }
    }

    /**
     * Returns the string inside a \Q...\E sequence and restores yy_buffer_index because quantifiers are greed.
     * @param text the \Q...\E sequence.
     * @return the string between \Q and \E.
     */
    protected function recognize_qe_sequence($text) {
        $text = $this->yytext();
        $str = '';
        $epos = qtype_poasquestion_string::strpos($text, '\E');
        if ($epos === false) {
            $str = qtype_poasquestion_string::substr($text, 2);
        } else {
            $str = qtype_poasquestion_string::substr($text, 2, $epos - 2);
            // Here's a trick. Quantifiers are greed, so a part after '\Q...\E' can be matched by this rule. Reset $this->yy_buffer_index manually.
            $tail = qtype_poasquestion_string::substr($text, $epos + 2);
            $this->yy_buffer_index -= qtype_poasquestion_string::strlen($tail);
        }
        return $str;
    }

    /**
     * Returns a unicode property flag type corresponding to the consumed string.
     * @param str string consumed by the lexer, defines the property itself.
     * @param error will be an error object if the property is unknown.
     * @return a constant of qtype_preg_leaf_charset if this property is known, null otherwise.
     */
    protected function get_uprop_flag($str, &$error) {
        if (array_key_exists($str, self::$upropflags)) {
            $error = null;
            return self::$upropflags[$str];
        } else {
            $error = $this->form_error($this->yytext(), qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $this->yychar, $this->yychar + $this->yylength() - 1, $str);
            return null;
        }
    }
%}

%%

<YYINITIAL> "?"("?"|"+")? {                     // Quantifier ?
    $text = $this->yytext();
    $greed = $this->yylength() === 1;
    $lazy = qtype_poasquestion_string::substr($text, 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    return $this->form_quant($text, $this->yychar, $this->yylength(), false, 0, 1, $lazy, $greed, $possessive);
}
<YYINITIAL> "*"("?"|"+")? {                     // Quantifier *
    $text = $this->yytext();
    $greed = $this->yylength() === 1;
    $lazy = qtype_poasquestion_string::substr($text, 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    return $this->form_quant($text, $this->yychar, $this->yylength(), true, 0, null, $lazy, $greed, $possessive);
}
<YYINITIAL> "+"("?"|"+")? {                     // Quantifier +
    $text = $this->yytext();
    $greed = $this->yylength() === 1;
    $lazy = qtype_poasquestion_string::substr($text, 1, 1) === '?';
    $possessive = !$greed && !$lazy;
    return $this->form_quant($text, $this->yychar, $this->yylength(), true, 1, null, $lazy, $greed, $possessive);
}
<YYINITIAL> "{"[0-9]+","[0-9]+"}"("?"|"+")? {   // Quantifier {m,n}
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_poasquestion_string::substr($text, $textlen - 1, 1);
    $greed = $lastchar === '}';
    $lazy = $lastchar === '?';
    $possessive = !$greed && !$lazy;
    $greed || $textlen--;
    $delimpos = qtype_poasquestion_string::strpos($text, ',');
    $leftborder = (int)qtype_poasquestion_string::substr($text, 1, $delimpos - 1);
    $rightborder = (int)qtype_poasquestion_string::substr($text, $delimpos + 1, $textlen - 2 - $delimpos);
    return $this->form_quant($text, $this->yychar, $this->yylength(), false, $leftborder, $rightborder, $lazy, $greed, $possessive);
}

<YYINITIAL> "{"[0-9]+",}"("?"|"+")? {           // Quantifier {m,}
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_poasquestion_string::substr($text, $textlen - 1, 1);
    $greed = $lastchar === '}';
    $lazy = $lastchar === '?';
    $possessive = !$greed && !$lazy;
    $greed || $textlen--;
    $leftborder = (int)qtype_poasquestion_string::substr($text, 1, $textlen - 1);
    return $this->form_quant($text, $this->yychar, $this->yylength(), true, $leftborder, null, $lazy, $greed, $possessive);
}
<YYINITIAL> "{,"[0-9]+"}"("?"|"+")? {           // Quantifier {,n}
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_poasquestion_string::substr($text, $textlen - 1, 1);
    $greed = ($lastchar === '}');
    $lazy = !$greed && $lastchar === '?';
    $possessive = !$greed && !$lazy;
    $greed || $textlen--;
    $rightborder = (int)qtype_poasquestion_string::substr($text, 2, $textlen - 3);
    return $this->form_quant($text, $this->yychar, $this->yylength(), false, 0, $rightborder, $lazy, $greed, $possessive);
}
<YYINITIAL> "{"[0-9]+"}" {                      // Quantifier {m}
    $text = $this->yytext();
    $count = (int)qtype_poasquestion_string::substr($text, 1, $this->yylength() - 2);
    return $this->form_quant($text, $this->yychar, $this->yylength(), false, $count, $count, false, true, false);
}
<YYINITIAL> "[^"|"["|"[^]"|"[]" {               // Beginning of a charset: [^ or [ or [^] or []
    $text = $this->yytext();
    $this->charset = new qtype_preg_leaf_charset();
    $this->charset->indfirst = $this->yychar;
    $this->charset->userinscription = array('');
    $this->charset->negative = ($text === '[^' || $text === '[^]');
    $this->charset->error = array();
    $this->charsetcount = 0;
    $this->charsetset = '';
    $this->charsetuserinscription = $text;
    if ($text === '[^]' || $text === '[]') {
        $this->add_flag_to_charset(']', qtype_preg_charset_flag::SET, ']');
    }
    $this->yybegin(self::CHARSET);
}
<YYINITIAL> "(" {                               // Beginning of a subpattern
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, $this->yytext(), (int)$this->lastsubpatt));
}
<YYINITIAL> ")" {
    $this->pop_opt_lvl();
    return $this->form_res(preg_parser_yyParser::CLOSEBRACK, new qtype_preg_lexem(0, $this->yychar, $this->yychar, $this->yytext()));
}
<YYINITIAL> "(?#"[^)]*")"? {                    // Comment
    $text = $this->yytext();
    if (qtype_poasquestion_string::substr($text, $this->yylength() - 1, 1) !== ')') {
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
    } else {
        return $this->nextToken();
    }
}
<YYINITIAL> "(*"[^)]*")"? {                     // Control sequence
    return $this->form_control($this->yytext(), $this->yychar, $this->yylength());
}
<YYINITIAL> "(?>" {
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext(), (int)$this->lastsubpatt));
}
<YYINITIAL> "(?<"{ALNUM}*">"? {                 // Named subpattern (?<name>...)
    return $this->form_named_subpatt($this->yytext(), $this->yychar, $this->yylength(), 3, '>');
}
<YYINITIAL> "(?'"{ALNUM}*"'"? {                 // Named subpattern (?'name'...)
    return $this->form_named_subpatt($this->yytext(), $this->yychar, $this->yylength(), 3, '\'');
}
<YYINITIAL> "(?P<"{ALNUM}*">"? {                // Named subpattern (?P<name>...)
    return $this->form_named_subpatt($this->yytext(), $this->yychar, $this->yylength(), 4, '>');
}
<YYINITIAL> "(?:" {
    $this->push_opt_lvl();
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?|" {                             // Duplicate subpattern numbers gropu
    $this->push_opt_lvl($this->lastsubpatt);    // Save the top-level subpattern number.
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?(?=" {                           // Conditional subpattern - assertion
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_PLA);
}
<YYINITIAL> "(?(?!" {                           // Conditional subpattern - assertion
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_NLA);
}
<YYINITIAL> "(?(?<=" {                          // Conditional subpattern - assertion
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_PLB);
}
<YYINITIAL> "(?(?<!" {                          // Conditional subpattern - assertion
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_NLB);
}
<YYINITIAL> "(?(R"[^"<>()'"]*")"? {             // Conditional subpattern - recursion
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION, ')');
}
<YYINITIAL> "(?(DEFINE"")"? {                   // Conditional subpattern
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_DEFINE, ')');
}
<YYINITIAL> "(?(<"[^"'<>()?!="]*(">)")? {       // Conditional subpattern - named
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, '>)', false, 4);
}
<YYINITIAL> "(?('"[^"'<>()?!="]*("')")? {       // Conditional subpattern - named
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, '\')', false, 4);
}
<YYINITIAL> "(?("[0-9+-]+")"? {                 // Conditional subpattern - numeric
    return $this->form_cond_subpatt($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, ')', true);
}
<YYINITIAL> "(?("[^"'<>()?!="]*")"? {           // Conditional subpattern - named or numeric
    $text = $this->yytext();
    $rightoffset = 0;
    qtype_poasquestion_string::substr($text, $this->yylength() - 1, 1) === ')' && $rightoffset++;
    $data = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 3 - $rightoffset);
    $sign = qtype_poasquestion_string::substr($data, 0, 1);
    if ($sign === '+' || $sign === '-') {
        $data = qtype_poasquestion_string::substr($data, 1);    }
    $numeric = $data !== '' && ctype_digit($data);
    return $this->form_cond_subpatt($text, $this->yychar, $this->yylength(), qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT, ')', $numeric, 3);
}
<YYINITIAL> "(?=" {
    $this->push_opt_lvl();
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?!" {
    $this->push_opt_lvl();
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?<=" {
    $this->push_opt_lvl();
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?<!" {
    $this->push_opt_lvl();
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "(?C"[0-9]*")"? {
    // TODO: callouts. For now this rule will return either error or exception :)
    $text = $this->yytext();
    if (qtype_poasquestion_string::substr($text, $this->yylength() - 1, 1) !== ')') {
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
    }
    throw new Exception('Callouts are not implemented yet');
    $number = (int)qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    if ($number > 255) {
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_CALLOUT_BIG_NUMBER, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
    } else {

    }
}
<YYINITIAL> "." {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
}
<YYINITIAL> {NOTSPECIAL} {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $text);
}
<YYINITIAL> "|" {
    // Reset subpattern numeration inside a (?|...) group.
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum != -1) {
        $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;
    }
    return $this->form_res(preg_parser_yyParser::ALT, new qtype_preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1, $this->yytext()));
}
<YYINITIAL> "\g"[0-9][0-9]? {
    $text = $this->yytext();
    return $this->form_backref($text, $this->yychar, $this->yylength(), (int)qtype_poasquestion_string::substr($text, 2));
}
<YYINITIAL> ("\g{-"|"\g{")[0-9][0-9]?"}" {
    $text = $this->yytext();
    $num = (int)qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    // Is it a relative backreference? Is so, convert it to an absolute one.
    if ($num < 0) {
        $num = $this->lastsubpatt + $num + 1;
    }
    return $this->form_backref($text, $this->yychar, $this->yylength(), (int)$num);
}
<YYINITIAL> "\g""{"?{ALNUM}*"}"? {              // Named backreference.
    return $this->form_named_backref($this->yytext(), $this->yychar, $this->yylength(), 3, '{', '}');
}
<YYINITIAL> "\k""{"?{ALNUM}*"}"? {              // Named backreference.
    return $this->form_named_backref($this->yytext(), $this->yychar, $this->yylength(), 3, '{', '}');
}
<YYINITIAL> "\k""'"?{ALNUM}*"'"? {              // Named backreference.
    return $this->form_named_backref($this->yytext(), $this->yychar, $this->yylength(), 3, '\'', '\'');
}
<YYINITIAL> "\k""<"?{ALNUM}*">"? {              // Named backreference.
    return $this->form_named_backref($this->yytext(), $this->yychar, $this->yylength(), 3, '<', '>');
}
<YYINITIAL> "(?P""="?{ALNUM}*")"? {             // Named backreference.
    return $this->form_named_backref($this->yytext(), $this->yychar, $this->yylength(), 4, '=', ')');
}
<YYINITIAL> "\a" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x07));
}
<YYINITIAL> "\c". {
    $text = $this->yytext();
    $error = null;
    $char = $this->calculate_cx($text, $error);
    if ($error !== null) {
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $error);
    } else {
        return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $char);
    }
}
<YYINITIAL> "\e" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x1B));
}
<YYINITIAL> "\f" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0C));
}
<YYINITIAL> "\n" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0A));
}
<YYINITIAL> ("\p"|"\P"). {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 2);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str, $error);
    $res = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::UPROP, $subtype, $negative);
    if ($error !== null) {
        $res->value->error = array($error);
    }
    return $res;
}
<YYINITIAL> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $circumflex = (qtype_poasquestion_string::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_poasquestion_string::substr($str, 1);
    }
    if ($str === 'Any') {
        $res = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN, $negative);
    } else {
        $subtype = $this->get_uprop_flag($str, $error);
        $res = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::UPROP, $subtype, $negative);
        if ($error !== null) {
            $res->value->error = array($error);
        }
    }
    return $res;
}
<YYINITIAL> "\r" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0D));
}
<YYINITIAL> "\t" {
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x09));
}
<YYINITIAL> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    if ($this->yylength() < 3) {
        $str = qtype_poasquestion_string::substr($text, 1);
    } else {
        $str = qtype_poasquestion_string::code2utf8(hexdec(qtype_poasquestion_string::substr($text, 2)));
    }
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $str);
}
<YYINITIAL> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $code = (int)hexdec($str);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, $this->yychar, $this->yychar + $this->yylength() - 1, '0x' . $str));
    } else {
        return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
    }
}
<YYINITIAL> "\d"|"\D" {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT, $text === '\D');
}
<YYINITIAL> "\h"|"\H" {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::HSPACE, $text === '\H');
}
<YYINITIAL> "\s"|"\S" {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE, $text === '\S');
}
<YYINITIAL> "\v"|"\V" {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::VSPACE, $text === '\V');
}
<YYINITIAL> "\w"|"\W" {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD, $text === '\W');
}
<YYINITIAL> "\C" {
    // TODO: matches any one data unit. For now implemented the same way as dot.
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
}
<YYINITIAL> "\N" {
    // TODO: matches any character except new line characters. For now, the same as dot.
    return $this->form_charset(array($this->yytext()), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
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
    $text = $this->yytext();
    return $this->form_simple_assertion($text, $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_B, $text === '\B');
}
<YYINITIAL> "\A" {
    return $this->form_simple_assertion($text, $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_A);
}
<YYINITIAL> "\z"|"\Z" {
    $text = $this->yytext();
    return $this->form_simple_assertion($text, $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_Z, $text === '\Z');
}
<YYINITIAL> "\G" {
    return $this->form_simple_assertion($text, $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_G);
}
<YYINITIAL> "^" {
    return $this->form_simple_assertion($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
}
<YYINITIAL> "$" {
    return $this->form_simple_assertion($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_DOLLAR);
}
<YYINITIAL> "(?"{MODIFIER}*-?{MODIFIER}*")" {
    $text = $this->yytext();
    $delimpos = qtype_poasquestion_string::strpos($text, '-');
    if ($delimpos !== false) {
        $set = qtype_poasquestion_string::substr($text, 2, $delimpos - 2);
        $unset = qtype_poasquestion_string::substr($text, $delimpos + 1, $this->yylength() - $delimpos - 2);
    } else {
        $set = qtype_poasquestion_string::substr($text, 2, $this->yylength() - 3);
        $unset = '';
    }
    $this->mod_top_opt(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
    return $this->nextToken();
}
<YYINITIAL> "(?"{MODIFIER}*-?{MODIFIER}*":" {
    $text = $this->yytext();
    $delimpos = qtype_poasquestion_string::strpos($text, '-');
    if ($delimpos !== false) {
        $set = qtype_poasquestion_string::substr($text, 2, $delimpos - 2);
        $unset = qtype_poasquestion_string::substr($text, $delimpos + 1, $this->yylength() - $delimpos - 2);
    } else {
        $set = qtype_poasquestion_string::substr($text, 2, $this->yylength() - 3);
        $unset = '';
    }
    $this->push_opt_lvl();
    $this->mod_top_opt(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
    return $this->form_res(preg_parser_yyParser::OPENBRACK, new qtype_preg_lexem('grouping', $this->yychar, $this->yychar + $this->yylength() - 1, $text));
}
<YYINITIAL> "(?"("R"|[0-9]+)")" {
    $text = $this->yytext();
    return $this->form_recursion($text, $this->yychar, $this->yylength(), $text);
}
<YYINITIAL> "\Q".*"\E"|"\Q".* {
    $text = $this->yytext();
    $str = $this->recognize_qe_sequence($text);
    $res = array();
    for ($i = 0; $i < qtype_poasquestion_string::strlen($str); $i++) {
        $res[] = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($str, $i, 1));
    }
    return $res;
}
<YYINITIAL> "\c" {
    return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error('\c', qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN, $this->yychar, $this->yychar + $this->yylength() - 1, '\c'));
}
<YYINITIAL> "\u"|"\U"|"\l"|"\L"|"\N{"{ALNUM}*"}" {
    $text = $this->yytext();
    return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
}
<YYINITIAL> \\[1-9][0-9]?[0-9]? {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 1);
    if ((int)$str < 10 || ((int)$str <= $this->maxsubpatt && (int)$str < 100)) {
        // Return a backreference.
        $res = $this->form_backref($text, $this->yychar, $this->yylength(), (int)$str);
    } else {
        // Return a character.
        $octal = '';
        $failed = false;
        for ($i = 0; !$failed && $i < qtype_poasquestion_string::strlen($str); $i++) {
            $tmp = qtype_poasquestion_string::substr($str, $i, 1);
            if (intval($tmp) < 8) {
                $octal = $octal . $tmp;
            } else {
                $failed = true;
            }
        }
        if (qtype_poasquestion_string::strlen($octal) === 0) {    // If no octal digits found, it should be 0.
            $octal = '0';
            $tail = $str;
        } else {                      // Octal digits found.
            $tail = qtype_poasquestion_string::substr($str, qtype_poasquestion_string::strlen($octal));
        }
        // Return a single lexem if all digits are octal, an array of lexems otherwise.
        if (qtype_poasquestion_string::strlen($tail) === 0) {
            $res = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec($octal)));
        } else {
            $res = array();
            $res[] = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec($octal)));
            for ($i = 0; $i < qtype_poasquestion_string::strlen($tail); $i++) {
                $res[] = $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($tail, $i, 1));
            }
        }
    }
    return $res;
}
<YYINITIAL> \\0[0-7]?[0-7]? {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec(qtype_poasquestion_string::substr($text, 1))));
}
<YYINITIAL> \\{ESCAPABLE} {
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($text, 1, 1));
}
<YYINITIAL> \\. {           // ERROR: incorrect escape sequence.
    $text = $this->yytext();
    return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_INVALID_ESCAPE_SEQUENCE, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
}
<YYINITIAL> \\ {           // ERROR: \ at end of pattern.
    return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error('\\', qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN, $this->yychar, $this->yychar + $this->yylength() - 1, '\\'));
}
<YYINITIAL> "[:"[^\]]*":]"|"[:^"[^\]]*":]"|"[."[^\]]*".]"|"[="[^\]]*"=]" {      // ERROR: POSIX class outside character set.
    $text = $this->yytext();
    return $this->form_res(preg_parser_yyParser::PARSLEAF, $this->form_error($text, qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET, $this->yychar, $this->yychar + $this->yylength() - 1, $text));
}
<YYINITIAL> . {                 // Just to avoid exceptions.
    $text = $this->yytext();
    return $this->form_charset(array($text), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $text);
}
<CHARSET> "[:alnum:]"|"[:^alnum:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^alnum:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALNUM, $negative);
}
<CHARSET> "[:alpha:]"|"[:^alpha:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^alpha:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ALPHA, $negative);
}
<CHARSET> "[:ascii:]"|"[:^ascii:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^ascii:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::ASCII, $negative);
}
<CHARSET> "\h"|"\H"|"[:blank:]"|"[:^blank:]" {
    $text = $this->yytext();
    $negative = ($text === '\H' || $text === '[:^blank:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::HSPACE, $negative);
}
<CHARSET> "[:cntrl:]"|"[:^cntrl:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^cntrl:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::CNTRL, $negative);
}
<CHARSET> "\d"|"\D"|"[:digit:]"|"[:^digit:]" {
    $text = $this->yytext();
    $negative = ($text === '\D' || $text === '[:^digit:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT, $negative);
}
<CHARSET> "[:graph:]"|"[:^graph:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^graph:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::GRAPH, $negative);
}
<CHARSET> "[:lower:]"|"[:^lower:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^lower:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::LOWER, $negative);
}
<CHARSET> "[:print:]"|"[:^print:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^print:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN, $negative);
}
<CHARSET> "[:punct:]"|"[:^punct:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^punct:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PUNCT, $negative);
}
<CHARSET> "\s"|"\S"|"[:space:]"|"[:^space:]"  {
    $text = $this->yytext();
    $negative = ($text === '\S' || $text === '[:^space:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE, $negative);
}
<CHARSET> "[:upper:]"|"[:^upper:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^upper:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::UPPER, $negative);
}
<CHARSET> "\w"|"\W"|"[:word:]"|"[:^word:]" {
    $text = $this->yytext();
    $negative = ($text === '\W' || $text === '[:^word:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD, $negative);
}
<CHARSET> "[:xdigit:]"|"[:^xdigit:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^xdigit:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::XDIGIT, $negative);
}
<CHARSET> "[:"[^\]]*":]"|"[:^"[^\]]*":]"|"[."[^\]]*".]"|"[="[^\]]*"=]" {
    $text = $this->yytext();
    $this->charset->userinscription[] = $text;
    $this->charset->error[] = $this->form_error($text, qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS, $this->yychar, $this->yychar + $this->yylength() - 1, $text);
    $this->charsetuserinscription .= $text;
}
<CHARSET> ("\p"|"\P"). {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 2);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str, $error);
    if ($error !== null) {
        $this->charset->userinscription[] = $text;
        $this->charset->error[] = $error;
        $this->charsetuserinscription .= $text;
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::UPROP, $subtype, $negative);
    }
}
<CHARSET> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $circumflex = (qtype_poasquestion_string::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_poasquestion_string::substr($str, 1);
    }
    if ($str === 'Any') {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN, $negative);
    } else {
        $subtype = $this->get_uprop_flag($str, $error);
        if ($error !== null) {
            $this->charset->userinscription[] = $text;
            $this->charset->error[] = $error;
            $this->charsetuserinscription .= $text;
        } else {
            $this->add_flag_to_charset($text, qtype_preg_charset_flag::UPROP, $subtype, $negative);
        }
    }
}
<CHARSET> \\0[0-7][0-7][0-7]? {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec(qtype_poasquestion_string::substr($text, 1))));
}
<CHARSET> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    if ($this->yylength() < 3) {
        $str = qtype_poasquestion_string::substr($text, 1);
    } else {
        $str = qtype_poasquestion_string::code2utf8(hexdec(qtype_poasquestion_string::substr($text, 2)));
    }
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $str);
}
<CHARSET> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $code = (int)hexdec($str);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $this->charset->error[] = $this->form_error($text, qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, $this->yychar, $this->yychar + $this->yylength() - 1, '0x' . $str);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
    }
}
<CHARSET> "\a" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x07));
}
<CHARSET> "\c". {
    $text = $this->yytext();
    $error = null;
    $char = $this->calculate_cx($text, $error);
    if ($error !== null) {
        $this->charset->error[] = $error;
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $char);
    }
}
<CHARSET> "\e" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x1B));
}
<CHARSET> "\f" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0C));
}
<CHARSET> "\n" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0A));
}
<CHARSET> "\N" {
    // TODO: matches any character except new line characters. For now, the same as dot.
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
}
<CHARSET> "\r" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0D));
}
<CHARSET> "\t" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x09));
}
<CHARSET> "\u"|"\U"|"\l"|"\L"|"\N{"{ALNUM}*"}" {
    $text = $this->yytext();
    $this->charset->error[] = $this->form_error($text, qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, $this->yychar, $this->yychar + $this->yylength() - 1, $text);
}
<CHARSET> "\Q".*"\E" {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $this->recognize_qe_sequence($text));
}
<CHARSET> \\. {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($text, 1, 1));
}
<CHARSET> [^\]] {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $text);
}
<CHARSET> "]" {
    $this->charset->indlast = $this->yychar;
    $this->charset->israngecalculated = false;
    if ($this->charsetset !== '') {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::SET, new qtype_poasquestion_string($this->charsetset));
        $this->charset->flags[] = array($flag);
    }
    if ($this->charset->userinscription[0] === '') {
        array_shift($this->charset->userinscription);
    }
    if (count($this->charset->error) === 0) {
        $this->charset->error = null;
    }
    $res = $this->form_res(preg_parser_yyParser::PARSLEAF, $this->charset);
    $this->charset = null;
    $this->charsetcount = 0;
    $this->charsetset = '';
    $this->charsetuserinscription = '';
    $this->yybegin(self::YYINITIAL);
    return $res;
}
