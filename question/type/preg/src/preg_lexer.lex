<?php

/**
 * Contains source info for generating lexer.
 * Lexer can return error tokens as leafs or fill the "casual" node's error field - depends on the node type.
 * A node's error field is usually filled if the it contains semantic errors but the syntax is correct:
 * for example, wrong quantifier borders {4,3}, wrong charset range z-a etc. Error leafs returned otherwise.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>, Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Represents a token returned from the lexer.
 */
class qtype_preg_token {
    /** Is this a leaf or a node, should be a constant of qtype_preg_yyParser. */
    public $type;
    /** The value can be either a qtype_preg_node or a qtype_preg_lexem. */
    public $value;

    public function __construct($type, $value) {
        $this->type = $type;
        $this->value = $value;
    }
}

class qtype_preg_optstack_item {
    public $modifiers;
    public $subpattnum;
    public $subpattname;
    public $parennum;

    public function __construct($modifiers, $subpattnum, $subpattname, $parennum) {
        $this->modifiers = $modifiers;
        $this->subpattnum = $subpattnum;
        $this->subpattname = $subpattname;
        $this->parennum = $parennum;
    }
}

%%
%class qtype_preg_lexer
%function nextToken
%char
%unicode
%state CHARSET
NOTSPECIALO = [^"\^$.[|()?*+{"]                          // Special characters outside character sets.
NOTSPECIALI = [^"\^-[]"]                                 // Special characters inside character sets.
MODIFIER    = [^"(|)<>#':=!PCR"0-9]                      // Excluding reserved (?... sequences, returning error if there is something weird.
ALNUM       = [^"!\"#$%&'()*+,-./:;<=>?[\]^`{|}~" \t\n]  // Used in subpattern\backreference names.
%init{
    $this->errors                    = array();
    $this->lastsubpatt               = 0;
    $this->maxsubpatt                = 0;
    $this->subpatternmap             = array();
    $this->backrefs                  = array();
    $this->optstack                  = array();
    $this->optstack[0]               = new qtype_preg_optstack_item(array('i' => false), -1, null, -1);
    $this->optcount                  = 1;
    $this->charset                   = null;
    $this->charsetcount              = 0;
    $this->charsetset                = '';
    $this->charsetuserinscription    = null;
    $this->charsetuserinscriptionraw = null;
    $this->handlingoptions           = new qtype_preg_handling_options();

%init}
%eof{
    // End of the expression inside a character class.
    if ($this->charset !== null) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET/*, $this->charsetuserinscription*/);
        $error->set_user_info($this->charset->indfirst, $this->yychar - 1, new qtype_preg_userinscription());
        $this->errors[] = $error;
    }
    // Check for backreferences to unexisting subpatterns.
    if (count($this->backrefs) > 0) {
        $maxbackrefnumber = -1;
        foreach ($this->backrefs as $leaf) {
            $number = $leaf->number;
            $error = false;
            if ((is_int($number) && $number > $this->maxsubpatt) || (is_string($number) && !array_key_exists($number, $this->subpatternmap))) {
                $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBPATT, $leaf->number);
                $error->set_user_info($leaf->indfirst, $leaf->indlast, new qtype_preg_userinscription());
                $this->errors[] = $error;
            }
        }
    }
%eof}
%{
    public $handlingoptions;                // Regex handling options set from the outside.
    protected $errors;                      // Array of lexical errors found.
    protected $lastsubpatt;                 // Number of the last lexed subpattern, used to deal with (?| ... ) constructions.
    protected $maxsubpatt;                  // Max subpattern number.
    protected $subpatternmap;               // Map of subpatterns name => number.
    protected $backrefs;                    // Array of backreference leafs. Used ad the end of lexical analysis to check for backrefs to unexisting subpatterns.
    protected $optstack;                    // Stack containing additional information about subpatterns (modifiers, current subpattern name, etc).
    protected $optcount;                    // Number of items in the above stack.
    protected $charset;                     // An instance of qtype_preg_leaf_charset, used when in CHARSET state.
    protected $charsetcount;                // Number of characters in the charset excluding flags.
    protected $charsetset;                  // Characters of the charset.
    protected $charsetuserinscription;      // User inscriptions for flags and ranges.
    protected $charsetuserinscriptionraw;   // User inscriptions char by char (can also be \x... or anything representing one character).
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

    /**
     * Returns array of error nodes.
     */
    public function mod_top_opt($set, $unset) {
        $allowed = new qtype_poasquestion_string('i');
        $setunset = new qtype_poasquestion_string($set . $unset);
        $wrongfound = '';
        $errors = array();
        $text = $this->yytext();

        // Are there unknown/unsupported modifiers?
        for ($i = 0; $i < $setunset->length(); $i++) {
            $modname = $setunset[$i];
            if ($allowed->contains($modname) === false) {
                $wrongfound .= $modname;
            }
        }
        if ($wrongfound !== '') {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_MODIFIER, htmlspecialchars($wrongfound));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            $errors[] = $error;
        }

        $setunseterror = false;
        for ($i = 0; $i < $set->length(); $i++) {
            $modname = $set[$i];
            if ($unset->contains($modname) !== false && $allowed->contains($modname) !== false) {
                // Setting and unsetting modifier at the same time is error.
                $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, htmlspecialchars($modname));
                $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
                $errors[] = $error;
                $setunseterror = true;
            }
        }

        // If errors don't exist, set and unset local modifiers.
        if (!$setunseterror) {
            for ($i = 0; $i < $set->length(); $i++) {
                $modname = $set[$i];
                if (qtype_poasquestion_string::strpos($allowed, $modname) !== false) {
                    $this->optstack[$this->optcount - 1]->modifiers[$modname] = true;
                }
            }
            for ($i = 0; $i < $unset->length(); $i++) {
                $modname = $unset[$i];
                if (qtype_poasquestion_string::strpos($allowed, $modname) !== false) {
                    $this->optstack[$this->optcount - 1]->modifiers[$modname] = false;
                }
            }
        }

        return $errors;
    }

    /**
     * Returns a quantifier token.
     */
    protected function form_quant($text, $pos, $length, $infinite, $leftborder, $rightborder, $lazy, $greed, $possessive) {
        if ($infinite) {
            $node = new qtype_preg_node_infinite_quant($leftborder, $lazy, $greed, $possessive);
        } else {
            $node = new qtype_preg_node_finite_quant($leftborder, $rightborder, $lazy, $greed, $possessive);
        }
        $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
        if (!$infinite && $leftborder > $rightborder) {
            $rightoffset = 0;
            $greed || $rightoffset++;
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE, htmlspecialchars($leftborder . ',' . $rightborder));
            $error->set_user_info($pos + 1, $pos + $length - 2 - $rightoffset, new qtype_preg_userinscription());
            $node->error = $error;
        }
        return new qtype_preg_token(qtype_preg_yyParser::QUANT, $node);
    }

    /**
     * Returns a control sequence token.
     */
    protected function form_control($text, $pos, $length) {
        // Error: missing ) at end.
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== ')') {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_CONTROL_ENDING, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }

        switch ($text) {
        case '(*ACCEPT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ACCEPT);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*FAIL)':
        case '(*F)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_FAIL);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*COMMIT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_COMMIT);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*THEN)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_THEN);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*SKIP)':
        case '(*SKIP:)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_SKIP);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*PRUNE)':
        case '(*PRUNE:)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_PRUNE);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*CR)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_CR);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*LF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_LF);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*CRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_CRLF);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*ANYCRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ANYCRLF);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*ANY)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ANY);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*BSR_ANYCRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_BSR_ANYCRLF);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*BSR_UNICODE)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_BSR_UNICODE);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*NO_START_OPT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_NO_START_OPT);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*UTF8)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UTF8);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*UTF16)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UTF16);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        case '(*UCP)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UCP);
            $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        default:
            $delimpos = qtype_poasquestion_string::strpos($text, ':');

            // Error: unknown control sequence.
            if ($delimpos === false) {
                $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE, htmlspecialchars($text));
                $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
            }

            // There is a parameter separated by ":"
            $subtype = qtype_poasquestion_string::substr($text, 2, $delimpos - 2);
            $name = qtype_poasquestion_string::substr($text, $delimpos + 1, $length - $delimpos - 2);

            // Error: empty name.
            if ($name === '') {
                $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, htmlspecialchars($text));
                $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
            }

            if ($subtype === 'MARK' || $delimpos === 2) {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
            } else if ($subtype === 'PRUNE') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                $node2 = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_PRUNE);
                $node2->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                return array(new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node),
                             new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node2));
            } else if ($subtype === 'SKIP') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_SKIP_NAME, $name);
                $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
            } else if ($subtype === 'THEN') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                $node2 = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_THEN);
                $node2->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
                return array(new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node),
                             new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node2));
            } else {
                $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE, htmlspecialchars($text));
                $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
            }
        }
    }

    /**
     * Returns a named subpattern token.
     */
    protected function form_named_subpatt($text, $pos, $length, $namestartpos, $closetype) {
        $this->push_opt_lvl();

        // Error: missing closing characters.
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== $closetype) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_SUBPATT_ENDING, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
        }

        $name = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - 1);

        // Error: empty name.
        if ($name === '') {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
        }

        $num = $this->map_subpattern($name);

        // Error: subpatterns with same names should have different numbers.
        if ($num === null) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBPATT_NAMES, htmlspecialchars($name));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
        }

        // Are we inside a (?| group?
        $insidedup = ($this->optcount > 1 && $this->optstack[$this->optcount - 2]->subpattnum !== -1);

        // First occurence of a named subpattern inside a (?| group.
        if ($insidedup && $this->optstack[$this->optcount - 2]->subpattname === null) {
            $this->optstack[$this->optcount - 2]->subpattname = $name;
        }

        // Error: different names for subpatterns of the same number.
        if ($insidedup && $this->optstack[$this->optcount - 2]->subpattname !== $name) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBPATT_NAMES, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
        }

        return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_SUBPATT, $pos, $pos + $length - 1, new qtype_preg_userinscription($text), $num));
    }

    /**
     * Returns a conditional subpattern token.
     */
    protected function form_cond_subpatt($text, $pos, $length, $subtype, $ending = '', $numeric = true, $namestartpos = 0) {
        $this->push_opt_lvl();

        // Conditional subpatterns with assertions is a separate story.
        if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLA || $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLA ||
            $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_PLB || $subtype === qtype_preg_node_cond_subpatt::SUBTYPE_NLB) {
            $this->push_opt_lvl();
            return new qtype_preg_token(qtype_preg_yyParser::CONDSUBPATT, new qtype_preg_lexem($subtype, $pos, $pos + $length - 1, new qtype_preg_userinscription($text)));
        }

        $endlength = strlen($ending);

        // Error: unclosed condition.
        if (qtype_poasquestion_string::substr($text, $length - $endlength) !== $ending) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBPATT_ENDING, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
        }

        $data = null;
        $secondnode = new qtype_preg_lexem(null, -1, -1, null);

        // Recursion.
        if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_RECURSION) {
            if (qtype_poasquestion_string::substr($text, 4, 1) === '&') {   // (?(R&
                $data = qtype_poasquestion_string::substr($text, 5, $length - 6);
                // Error: empty name.
                if ($data === '') {
                    $secondnode = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, htmlspecialchars($text));
                    $secondnode->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                }
            } else {                                                        // (?(Rnumber)
                $tmp = qtype_poasquestion_string::substr($text, 4, $length - 5);
                // Error: digits expected.
                if ($tmp !== '' && !ctype_digit($tmp)) {
                    $secondnode = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER, htmlspecialchars($tmp));
                    $secondnode->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                    $data = 0;
                }
                $data = (int)$tmp;
            }
        }

        // Subpattern.
        if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT && $numeric) {
            $str = qtype_poasquestion_string::substr($text, 3, $length - 4);
            $tmp = qtype_poasquestion_string::substr($str, 0, 1);
            $sign = 0;
            $tmp === '+' && $sign++;
            $tmp === '-' && $sign--;
            if ($sign !== 0) {
                $str = qtype_poasquestion_string::substr($str, 1);
            }
            if ($str !== '' && !ctype_digit($str)) {
                // Error: digits expected.
                $secondnode = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_WRONG_CONDSUBPATT_NUMBER, htmlspecialchars($str));
                $secondnode->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            } else {
                if ($sign !== 0) {
                    $data = $sign * (int)$str + $this->lastsubpatt;
                    if ($sign < 0) {
                        $data++;
                    }
                } else {
                    $data = (int)$str;
                }
                // Error: reference to the whole expression.
                if ($data === 0) {
                    $secondnode = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CONSUBPATT_ZERO_CONDITION, htmlspecialchars($data));
                    $secondnode->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
                }
            }
        }

        if ($subtype === qtype_preg_node_cond_subpatt::SUBTYPE_SUBPATT && !$numeric) {
            $data = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - $endlength);
            // Error: empty name.
            if ($data === '') {
                $secondnode = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, htmlspecialchars($text));
                $secondnode->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            }
        }

        // Subtype "DEFINE" has no need to be processed.

        return array(new qtype_preg_token(qtype_preg_yyParser::CONDSUBPATT, new qtype_preg_lexem_subpatt($subtype, $pos, $pos + $length - 1, new qtype_preg_userinscription($text), $data)),
                     new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $secondnode),
                     new qtype_preg_token(qtype_preg_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, null)));
    }

    /**
     * Returns a named backreference token.
     */
    protected function form_named_backref($text, $pos, $length, $namestartpos, $opentype, $closetype) {
        // Error: missing opening characters.
        if (qtype_poasquestion_string::substr($text, $namestartpos - 1, 1) !== $opentype) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING, htmlspecialchars($opentype));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }

        // Error: missing closing characters.
        if (qtype_poasquestion_string::substr($text, $length - 1, 1) !== $closetype) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING, htmlspecialchars($closetype));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }

        $name = qtype_poasquestion_string::substr($text, $namestartpos, $length - $namestartpos - 1);

        // Error: empty name.
        if ($name === '') {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SUBPATT_NAME_EXPECTED, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }

        $node = new qtype_preg_leaf_backref($name);
        $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
        if (is_a($node, 'qtype_preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->modifiers['i']) {
            $node->caseinsensitive = true;
        }
        $this->backrefs[] = $node;
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a backreference token.
     */
    protected function form_backref($text, $pos, $length, $number) {
        // Error: backreference to the whole expression.
        if ($number === 0) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_BACKREF_TO_ZERO, htmlspecialchars($text));
            $error->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }

        $node = new qtype_preg_leaf_backref($number);
        $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
        if (is_a($node, 'qtype_preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->modifiers['i']) {
            $node->caseinsensitive = true;
        }
        $this->backrefs[] = $node;
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a simple assertion token.
     */
    protected function form_simple_assertion($text, $pos, $length, $subtype, $negative = false) {
        $node = new qtype_preg_leaf_assert();
        $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
        $node->subtype = $subtype;
        $node->negative = $negative;
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a character set token.
     */
    protected function form_charset($text, $pos, $length, $subtype, $data, $negative = false) {
        $node = new qtype_preg_leaf_charset();
        $uitype = ($subtype === qtype_preg_charset_flag::SET) ? qtype_preg_userinscription::TYPE_GENERAL : qtype_preg_userinscription::TYPE_CHARSET_FLAG;
        $node->set_user_info($pos, $pos + $length - 1, array(new qtype_preg_userinscription($text, $uitype)));
        if (is_a($node, 'qtype_preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->modifiers['i']) {
            $node->caseinsensitive = true;
        }
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
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
    }

    /**
     * Returns a recursion token.
     */
    protected function form_recursion($text, $pos, $length, $number) {
        $node = new qtype_preg_leaf_recursion();
        $node->set_user_info($pos, $pos + $length - 1, new qtype_preg_userinscription($text));
        if (is_a($node, 'qtype_preg_leaf') && $this->optcount > 0 && $this->optstack[$this->optcount - 1]->modifiers['i']) {
            $node->caseinsensitive = true;
        }
        if ($number[2] === 'R') {
            $node->number = 0;
        } else {
            $node->number = qtype_poasquestion_string::substr($number, 2, qtype_poasquestion_string::strlen($number) - 3);
        }
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
    }

    /**
     * Forms an interval from sequences like a-z, 0-9, etc. If a string contains
     * something like "x-z" in the end, it will be converted to "xyz".
     * @return mixed null if everything is correct, an error object otherwise.
     */
    protected function form_num_interval() {
        // Check if there are enough characters in before.
        if ($this->charsetcount < 3 || qtype_poasquestion_string::substr($this->charsetset, $this->charsetcount - 2, 1) !== '-') {
            return null;
        }
        $startchar = qtype_poasquestion_string::substr($this->charsetset, $this->charsetcount - 3, 1);
        $endchar = qtype_poasquestion_string::substr($this->charsetset, $this->charsetcount - 1, 1);

        // Modify userinscription;
        $userinscriptionend = array_pop($this->charsetuserinscriptionraw);
        array_pop($this->charsetuserinscriptionraw);
        $userinscriptionstart = array_pop($this->charsetuserinscriptionraw);
        $this->charsetuserinscription[] = new qtype_preg_userinscription($userinscriptionstart->data . '-' . $userinscriptionend->data);

        if (qtype_poasquestion_string::ord($startchar) <= qtype_poasquestion_string::ord($endchar)) {
            // Replace last 3 characters by all the characters between them.
            $this->charsetset = qtype_poasquestion_string::substr($this->charsetset, 0, $this->charsetcount - 3);
            $this->charsetcount -= 3;
            $curord = qtype_poasquestion_string::ord($startchar);
            $endord = qtype_poasquestion_string::ord($endchar);
            while ($curord <= $endord) {
                $this->charsetset .= qtype_poasquestion_string::code2utf8($curord++);
                $this->charsetcount++;
            }
            return null;
        } else {
            // Delete last 3 characters.
            $this->charsetcount -= 3;
            $this->charsetset = qtype_poasquestion_string::substr($this->charsetset, 0, $this->charsetcount);
            // Return the error node.
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE, htmlspecialchars($startchar . '-' . $endchar));
            $error->set_user_info($this->yychar - 2, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            return $error;
        }
    }

    protected function push_opt_lvl($subpattnum = -1) {
        if ($this->optcount > 0) {
            $this->optstack[$this->optcount] = clone $this->optstack[$this->optcount - 1];
            if ($subpattnum !== -1) {
                $this->optstack[$this->optcount]->subpattnum = $subpattnum;
                $this->optstack[$this->optcount]->parennum = $this->optcount;
            }
            $this->optstack[$this->optcount]->subpattname = null;   // Reset it anyway.
            $this->optcount++;
        } // Else the error will be found in parser, lexer does nothing for this error (closing unopened bracket).
    }

    protected function pop_opt_lvl() {
        if ($this->optcount > 0) {
            $item = $this->optstack[$this->optcount - 1];
            $this->optcount--;
            // Is it a pair for some opening paren?
            if ($item->parennum === $this->optcount) {
                // Are we out of a (?|...) block?
                if ($this->optstack[$this->optcount - 1]->subpattnum !== -1) {
                    // Inside.
                    $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;    // Reset subpattern numeration.
                } else {
                    // Outside.
                    $this->lastsubpatt = $this->maxsubpatt;
                }
            }
        }
    }

    /**
     * Adds a named subpattern to the map.
     * @param name subpattern to be mapped.
     */
    protected function map_subpattern($name) {
        if (!array_key_exists($name, $this->subpatternmap)) {   // This subpattern does not exists.
            $num = ++$this->lastsubpatt;
            $this->subpatternmap[$name] = $num;
        } else {                                                // Subpatterns with same names should have same numbers.
            if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum === -1) {
                return null;
            }
            $num = $this->subpatternmap[$name];
            $this->lastsubpatt++;

        }
        $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
        return (int)$num;
    }

    /**
     * Calculates the character for a \cx sequence.
     * @param cx the sequence itself.
     * @return character corresponding to the given sequence.
     */
    protected function calculate_cx($cx) {
        $x = qtype_poasquestion_string::substr($cx, 2);
        $code = qtype_poasquestion_string::ord($x);
        if ($code > 127) {
            return null;
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
     * @param appendtoend if true, new characters are concatenated from right, from left otherwise.
     */
    protected function add_flag_to_charset($text, $type, $data, $negative = false, $appendtoend = true) {
        switch ($type) {
        case qtype_preg_charset_flag::SET:
            $this->charsetuserinscriptionraw[] = new qtype_preg_userinscription($text);
            $this->charsetcount++;
            if ($appendtoend) {
                $this->charsetset .= $data;
            } else {
                $this->charsetset = $data . $this->charsetset;
            }
            $error = $this->form_num_interval();
            if ($error !== null) {
                $this->charset->error[] = $error;
            }
            break;
        case qtype_preg_charset_flag::FLAG:
        case qtype_preg_charset_flag::UPROP:
            $this->charsetuserinscription[] = new qtype_preg_userinscription($text, qtype_preg_userinscription::TYPE_CHARSET_FLAG);
            $flag = new qtype_preg_charset_flag;
            $flag->set_data($type, $data);
            $flag->negative = $negative;
            $this->charset->flags[] = array($flag);
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
     * @return a constant of qtype_preg_leaf_charset if this property is known, null otherwise.
     */
    protected function get_uprop_flag($str) {
        if (array_key_exists($str, self::$upropflags)) {
            $error = null;
            return self::$upropflags[$str];
        }
        return null;
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
    $this->charset->negative = ($text === '[^' || $text === '[^]');
    $this->charset->error = array();
    $this->charset->userinscription = array();
    $this->charsetcount = 0;
    $this->charsetset = '';
    $this->charsetuserinscription = array();
    $this->charsetuserinscriptionraw = array();
    if ($text === '[^]' || $text === '[]') {
        $this->add_flag_to_charset(']', qtype_preg_charset_flag::SET, ']');
    }
    $this->yybegin(self::CHARSET);
}
<YYINITIAL> "(" {                               // Beginning of a subpattern
    $this->push_opt_lvl();
    $this->lastsubpatt++;
    $this->maxsubpatt = max($this->maxsubpatt, $this->lastsubpatt);
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_SUBPATT, $this->yychar, $this->yychar, new qtype_preg_userinscription('('), $this->lastsubpatt));
}
<YYINITIAL> ")" {
    $this->pop_opt_lvl();
    return new qtype_preg_token(qtype_preg_yyParser::CLOSEBRACK, new qtype_preg_lexem(0, $this->yychar, $this->yychar, new qtype_preg_userinscription(')')));
}
<YYINITIAL> "(?#"[^)]*")"? {                    // Comment
    $text = $this->yytext();
    if (qtype_poasquestion_string::substr($text, $this->yylength() - 1, 1) !== ')') {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING, htmlspecialchars($text));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
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
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem_subpatt(qtype_preg_node_subpatt::SUBTYPE_ONCEONLY, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?>'), $this->lastsubpatt));
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
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_subpatt::SUBTYPE_GROUPING, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?:')));
}
<YYINITIAL> "(?|" {                             // Duplicate subpattern numbers gropu
    $this->push_opt_lvl($this->lastsubpatt);    // Save the top-level subpattern number.
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_subpatt::SUBTYPE_GROUPING, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?|')));
}
<YYINITIAL> "(?()" {                            // Error - empty condition
    $text = $this->yytext();
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CONDSUBPATT_ASSERT_EXPECTED, htmlspecialchars($text));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return array(new qtype_preg_token(qtype_preg_yyParser::CONDSUBPATT, new qtype_preg_lexem(null, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription(''))),
                 new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error),
                 new qtype_preg_token(qtype_preg_yyParser::CLOSEBRACK, new qtype_preg_lexem(null, -1, -1, null)));
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
<YYINITIAL> "(?(DEFINE"")"? {                   // Conditional subpattern - define
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
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_PLA, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?=')));
}
<YYINITIAL> "(?!" {
    $this->push_opt_lvl();
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_NLA, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?!')));
}
<YYINITIAL> "(?<=" {
    $this->push_opt_lvl();
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_PLB, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?<=')));
}
<YYINITIAL> "(?<!" {
    $this->push_opt_lvl();
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_assert::SUBTYPE_NLB, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('(?<!')));
}
<YYINITIAL> "(?C"[0-9]*")"? {
    // TODO: callouts. For now this rule will return either error or exception :)
    $text = $this->yytext();
    if (qtype_poasquestion_string::substr($text, $this->yylength() - 1, 1) !== ')') {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING, htmlspecialchars($text));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    }
    throw new Exception('Callouts are not implemented yet');
    $number = (int)qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    if ($number > 255) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CALLOUT_BIG_NUMBER, htmlspecialchars($text));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    } else {

    }
}
<YYINITIAL> "." {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
}
<YYINITIAL> "|" {
    // Reset subpattern numeration inside a (?|...) group.
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->subpattnum != -1) {
        $this->lastsubpatt = $this->optstack[$this->optcount - 1]->subpattnum;
    }
    return new qtype_preg_token(qtype_preg_yyParser::ALT, new qtype_preg_lexem(0, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription('|')));
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
    return $this->form_backref($text, $this->yychar, $this->yylength(), $num);
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
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x07));
}
<YYINITIAL> "\c". {
    $text = $this->yytext();
    $char = $this->calculate_cx($text);
    if ($char === null) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII, htmlspecialchars($text));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    } else {
        return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $char);
    }
}
<YYINITIAL> "\e" {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x1B));
}
<YYINITIAL> "\f" {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0C));
}
<YYINITIAL> "\n" {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0A));
}
<YYINITIAL> ("\p"|"\P"). {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 2);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype === null) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, htmlspecialchars($str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    } else {
        return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::UPROP, $subtype, $negative);
    }
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
        $res = $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN, $negative);
    } else {
        $subtype = $this->get_uprop_flag($str);
        if ($subtype === null) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, htmlspecialchars($str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        } else {
            return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::UPROP, $subtype, $negative);
        }
    }
    return $res;
}
<YYINITIAL> "\r" {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x0D));
}
<YYINITIAL> "\t" {
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x09));
}
<YYINITIAL> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    if ($this->yylength() < 3) {
        $str = qtype_poasquestion_string::substr($text, 1);
        return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $str);
    } else {
        $code = hexdec(qtype_poasquestion_string::substr($text, 2));
        if ($code > qtype_preg_unicode::max_possible_code()) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, htmlspecialchars('0x' . $str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        } else if (0xd800 <= $code && $code <= 0xdfff) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, htmlspecialchars('0x' . $str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        } else {
            return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
        }
    }
}
<YYINITIAL> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $code = hexdec($str);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, htmlspecialchars('0x' . $str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, htmlspecialchars('0x' . $str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
    } else {
        return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
    }
}
<YYINITIAL> "\d"|"\D" {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::DIGIT, $text === '\D');
}
<YYINITIAL> "\h"|"\H" {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::HSPACE, $text === '\H');
}
<YYINITIAL> "\s"|"\S" {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::SPACE, $text === '\S');
}
<YYINITIAL> "\v"|"\V" {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::VSPACE, $text === '\V');
}
<YYINITIAL> "\w"|"\W" {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::WORD, $text === '\W');
}
<YYINITIAL> "\C" {
    // TODO: matches any one data unit. For now implemented the same way as dot.
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
}
<YYINITIAL> "\N" {
    // TODO: matches any character except new line characters. For now, the same as dot.
    return $this->form_charset($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_charset_flag::FLAG, qtype_preg_charset_flag::PRIN);
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
    return $this->form_simple_assertion($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_A);
}
<YYINITIAL> "\z"|"\Z" {
    $text = $this->yytext();
    return $this->form_simple_assertion($text, $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_Z, $text === '\Z');
}
<YYINITIAL> "\G" {
    return $this->form_simple_assertion($this->yytext(), $this->yychar, $this->yylength(), qtype_preg_leaf_assert::SUBTYPE_ESC_G);
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
    $errors = $this->mod_top_opt(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
    if (count($errors) > 0) {
        $res = array();
        foreach ($errors as $error) {
            $res[] = new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }
        return $res;
    } else {
        if ($this->handlingoptions->preserveallnodes) {
            $node = new qtype_preg_leaf_option(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
            $node->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription($text));
            return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
        } else {
            return $this->nextToken();
        }
    }
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
    $errors = $this->mod_top_opt(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
    if (count($errors) > 0) {
        $res = array();
        foreach ($errors as $error) {
            $res[] = new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
        }
        return $res;
    } else {
        if ($this->handlingoptions->preserveallnodes) {
            $node = new qtype_preg_leaf_option(new qtype_poasquestion_string($set), new qtype_poasquestion_string($unset));
            $node->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription($text));
            $res = array();
            $res[] = new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_subpatt::SUBTYPE_GROUPING, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription($text)));
            $res[] = new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $node);
            return $res;
        } else {
            return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, new qtype_preg_lexem(qtype_preg_node_subpatt::SUBTYPE_GROUPING, $this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription($text)));
        }
    }
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
        $res[] = $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($str, $i, 1));
    }
    return $res;
}
<YYINITIAL> "\c" {
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN, htmlspecialchars('\c'));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
}
<YYINITIAL> "\u"|"\U"|"\l"|"\L"|"\N{"{ALNUM}*"}" {
    $text = $this->yytext();
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, htmlspecialchars($text));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
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
            $res = $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec($octal)));
        } else {
            $res = array();
            $res[] = $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec($octal)));
            for ($i = 0; $i < qtype_poasquestion_string::strlen($tail); $i++) {
                $res[] = $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($tail, $i, 1));
            }
        }
    }
    return $res;
}
<YYINITIAL> \\0[0-7]?[0-7]? {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec(qtype_poasquestion_string::substr($text, 1))));
}
<YYINITIAL> \\. {
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::substr($text, 1, 1));
}
<YYINITIAL> "(?<". {       // ERROR: Unrecognized character after (?<
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_LBA, htmlspecialchars('(?<'));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return new qtype_preg_token(qtype_preg_yyParser::OPENBRACK, $error);
}
<YYINITIAL> \\ {           // ERROR: \ at end of pattern.
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN, htmlspecialchars('\\'));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
}
<YYINITIAL> "[:"[^\]]*":]"|"[:^"[^\]]*":]"|"[."[^\]]*".]"|"[="[^\]]*"=]" {      // ERROR: POSIX class outside character set.
    $text = $this->yytext();
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET, htmlspecialchars($text));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    return new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $error);
}
<YYINITIAL> . {                 // Just to avoid exceptions.
    $text = $this->yytext();
    return $this->form_charset($text, $this->yychar, $this->yylength(), qtype_preg_charset_flag::SET, $text);
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
<CHARSET> "\h"|"\H"|"\v"|"\V"|"[:blank:]"|"[:^blank:]" {
    $text = $this->yytext();
    $negative = ($text === '\H' || $text === '\V' || $text === '[:^blank:]');
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
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS, htmlspecialchars($text));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    $this->charset->error[] = $error;
    $this->charsetuserinscription[] = new qtype_preg_userinscription($text);
}
<CHARSET> ("\p"|"\P"). {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 2);
    $negative = (qtype_poasquestion_string::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype === null) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, htmlspecialchars($str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        $this->charset->error[] = $error;
        $this->charsetuserinscription[] = new qtype_preg_userinscription($text, qtype_preg_userinscription::TYPE_CHARSET_FLAG);
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
        $subtype = $this->get_uprop_flag($str);
        if ($subtype === null) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, htmlspecialchars($str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            $this->charset->error[] = $error;
            $this->charsetuserinscription[] = new qtype_preg_userinscription($text, qtype_preg_userinscription::TYPE_CHARSET_FLAG);
        } else {
            $this->add_flag_to_charset($text, qtype_preg_charset_flag::UPROP, $subtype, $negative);
        }
    }
}
<CHARSET> \\[0-7][0-7]?[0-7]? {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(octdec(qtype_poasquestion_string::substr($text, 1))));
}
<CHARSET> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    if ($this->yylength() < 3) {
        $str = qtype_poasquestion_string::substr($text, 1);
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $str);
    } else {
        $code = hexdec(qtype_poasquestion_string::substr($text, 2));
        if ($code > qtype_preg_unicode::max_possible_code()) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, htmlspecialchars('0x' . $str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            $this->charset->error[] = $error;
            $this->charsetuserinscription[] = new qtype_preg_userinscription($text);
        } else if (0xd800 <= $code && $code <= 0xdfff) {
            $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, htmlspecialchars('0x' . $str));
            $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
            $this->charset->error[] = $error;
        } else {
            $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
        }
    }
}
<CHARSET> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $str = qtype_poasquestion_string::substr($text, 3, $this->yylength() - 4);
    $code = hexdec($str);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, htmlspecialchars('0x' . $str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        $this->charset->error[] = $error;
        $this->charsetuserinscription[] = new qtype_preg_userinscription($text);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, htmlspecialchars('0x' . $str));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        $this->charset->error[] = $error;
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8($code));
    }
}
<CHARSET> "\a" {
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::SET, qtype_poasquestion_string::code2utf8(0x07));
}
<CHARSET> "\c". {
    $text = $this->yytext();
    $char = $this->calculate_cx($text);
    if ($char === null) {
        $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII, htmlspecialchars($text));
        $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
        $this->charset->error[] = $error;
        $this->charsetuserinscription[] = new qtype_preg_userinscription($text);
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
    $error = new qtype_preg_node_error(qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, htmlspecialchars($text));
    $error->set_user_info($this->yychar, $this->yychar + $this->yylength() - 1, new qtype_preg_userinscription());
    $this->charset->error[] = $error;
    $this->charsetuserinscription[] = new qtype_preg_userinscription($text);
}
<CHARSET> "\Q".*"\E" {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $this->recognize_qe_sequence($text));
}
<CHARSET> \\. {
    $text = $this->yytext();
    $char = qtype_poasquestion_string::substr($text, 1, 1);
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::SET, $char, false, $char !== '-');
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
    $tmp = '';
    foreach ($this->charsetuserinscriptionraw as $userinscription) {
        $tmp .= $userinscription->data;
    }
    if ($tmp !== '') {
        $this->charset->userinscription[] = new qtype_preg_userinscription($tmp);
    }
    foreach ($this->charsetuserinscription as $userinscription) {
        $this->charset->userinscription[] = $userinscription;
    }
    if (count($this->charset->error) === 0) {
        $this->charset->error = null;
    }
    if ($this->optcount > 0 && $this->optstack[$this->optcount - 1]->modifiers['i']) {
        $this->charset->caseinsensitive = true;
    }
    $res = new qtype_preg_token(qtype_preg_yyParser::PARSLEAF, $this->charset);
    $this->charset = null;
    $this->charsetcount = 0;
    $this->charsetset = '';
    $this->charsetuserinscription = array();
    $this->charsetuserinscriptionraw = array();
    $this->yybegin(self::YYINITIAL);
    return $res;
}
