<?php

/**
 * Contains source info for generation of lexer.
 *
 * If there are named subexpressions or backreferences, the returning nodes will contain not names but
 * their automatically-assigned numbers. To deal with names, the lexer saves a map name => number.
 *
 * As for error tokens, an error may be returned in 3 ways:
 *   a) as a regular node, but with non-null error field of it;
 *   b) as leafs, if it's not possible to act using the error field.
 *
 * The error field is usually filled if the node contains semantic errors but the syntax is correct:
 * for example, wrong quantifier borders {4,3}, wrong charset range z-a, etc.
 *
 * Additionally, all the errors found are also stored in the lexer's errors array and can be retrieved
 * by using the get_error_nodes() method.
 *
 * All nodes returned from the lexer should have valid userinscription and indexes.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Valeriy Streltsov <vostreltsov@gmail.com>, Dmitriy Kolesov <xapuyc7@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/jlex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_parser.php');
require_once($CFG->dirroot . '/question/type/preg/preg_nodes.php');
require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

/**
 * Class providing information for current subexpression. Stack of such objects
 * grows when hitting '(' and similar parens; decreases when hitting ')'
 */
class qtype_preg_opt_stack_item {
    public $options;
    public $dup_subexpr_number;
    public $dup_subexpr_name;
    public $parennum;

    public function __construct($options, $dup_subexpr_number, $dup_subexpr_name, $parennum) {
        $this->options = $options;
        $this->dup_subexpr_number = $dup_subexpr_number;
        $this->dup_subexpr_name = $dup_subexpr_name;
        $this->parennum = $parennum;
    }

    public function __clone() {
        $this->options = clone $this->options;
    }
}

%%
%class qtype_preg_lexer
%function nextToken
%char
%line
%unicode
%state YYCOMMENTEXT
%state YYCOMMENT
%state YYQEOUT
%state YYQEIN
%state YYCHARSET

QUANTTYPE  = ("?"|"+")?                                 // Greedy, lazy or possessive quantifiers.
MODIFIER   = [imsxuADSUXJ]                              // Recognizable modifiers letters.
ALNUM      = [^"!\"#$%&'()*+,-./:;<=>?[\]^`{|}~" \t\n]  // Used in subexpression\backreference names.
DIGIT      = [0123456789]                               // Simply decimal digit.
ANY        = (.|[\r\n])                                 // Any character.
SIGN       = ("+"|"-")                                  // Sign of an integer.
WHITESPACE = [\x09\x0A\x0B\x0C\x0D\x20\x85\xA0]         // Whitespace character.

%init{
    $this->options = new qtype_preg_handling_options();
    $this->opt_stack[0] = new qtype_preg_opt_stack_item(clone $this->options, -1, null, -1);
%init}
%eof{
    // End of the regex inside a character class.
    if ($this->yy_lexical_state == self::YYCHARSET) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET, '');
        $position = new qtype_preg_position($this->state_begin_position->indfirst, $this->yychar + $this->yylength() - 1,
                                            $this->state_begin_position->linefirst, $this->yyline,
                                            $this->state_begin_position->colfirst, $this->yycol + $this->yylength() - 1);
        $error->set_user_info($position, $this->charset->userinscription);
    }

    // End of the regex inside a comment.
    if ($this->yy_lexical_state == self::YYCOMMENT) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING, '');
        $position = new qtype_preg_position($this->state_begin_position->indfirst, $this->yychar + $this->yylength() - 1,
                                            $this->state_begin_position->linefirst, $this->yyline,
                                            $this->state_begin_position->colfirst, $this->yycol + $this->yylength() - 1);
        $error->set_user_info($position, array(new qtype_preg_userinscription('(?#')));
    }

    // Check for references to unexisting subexpressions.
    foreach ($this->nodes_with_subexpr_refs as $node) {
        if (is_int($node->number)) {
            if ($node->number > $this->maxsubexpr) {
                // Error: unexisting subexpression.
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR, $node->number, $node);
                $error->set_user_info($node->position, $node->userinscription);
            }
            continue;   // No need for further checks if it's an integer number.
        }

        // Convert names to numbers.
        if (isset($node->name)) {
            $number = isset($this->subexpr_name_to_number_map[$node->name]) ? $this->subexpr_name_to_number_map[$node->name] : null;

            if ($number === null && !($node->type == qtype_preg_node::TYPE_NODE_COND_SUBEXPR && $node->name === '')) {
                // Error: unexisting subexpression.
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNEXISTING_SUBEXPR, $node->name, $node);
                $error->set_user_info($node->position, $node->userinscription);
            }

            // For matchers: replace name with number for simple usage.
            //if (!$this->options->preserveallnodes) {
                $node->number = $number;
            //}
        }
    }

    // Form the subexpr reference map.
    foreach ($this->nodes_with_subexpr_refs as $node) {
        if (!array_key_exists($node->number, $this->subexpr_refs_map)) {
            $this->subexpr_refs_map[$node->number] = array();
        }
        $this->subexpr_refs_map[$node->number][] = $node;
    }

%eof}
%{
    // Regex handling options set from the outside.
    protected $options = null;

    // All modifiers occured in the regex.
    protected $allmodifiers = array();

    // Positions skipped because preserveallnodes option was set to false.
    protected $skipped_positions = array();

    // Array of lexical errors found.
    protected $errors = array();

    // Subexpression number corresponding to the whole regex.
    // By default is 0, but greater when lexing templates.
    protected $initialsubexpr = 0;

    // Number of the last lexed subexpression, used to deal with (?| ... ) constructions.
    protected $lastsubexpr = 0;

    // Max subexpression number.
    protected $maxsubexpr = 0;

    // Map of subexpression names => numbers.
    protected $subexpr_name_to_number_map = array();

    // Map of subexpression numbers => names.
    protected $subexpr_number_to_name_map = array();

    // Array of nodes which have references to subexpressions: backreferences, conditional subexpressions, subexpression calls.
    protected $nodes_with_subexpr_refs = array();

    // Array of subexpr references: subexpr number => array of reference nodes.
    protected $subexpr_refs_map = array();

    // Stack containing additional information about subexpressions (options, current subexpression name, etc).
    protected $opt_stack = array();

    // Comment string.
    protected $comment = '';

    // Comment length.
    protected $comment_length = 0;

    // \Q...\E sequence.
    protected $qe_sequence = '';

    // \Q...\E sequence length.
    protected $qe_sequence_length = 0;

    // An instance of qtype_preg_leaf_charset, used in YYCHARSET state.
    protected $charset = null;

    // An instance of qtype_preg_position, used in when yybegin is invoked.
    protected $state_begin_position = null;

    // Number of characters in the charset excluding flags.
    protected $charset_count = 0;

    // Characters of the charset.
    protected $charset_set = '';

    public static function tokenize_regex($regex, $options, &$lexer, &$hastemplates) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $lexer = new qtype_preg_lexer($pseudofile);
        $lexer->set_options($options);
        $hastemplates = false;

        $result = array();
        while (($token = $lexer->nextToken()) !== null) {
            if (is_array($token)) {
                foreach ($token as $curtoken) {
                    $result[] = $curtoken;
                    $hastemplates = $hastemplates || ($curtoken->type === qtype_preg_parser::TEMPLATEPARSELEAF || $curtoken->type === qtype_preg_parser::TEMPLATEOPENBRACK);
                }
            } else {
                $result[] = $token;
                $hastemplates = $hastemplates || ($token->type === qtype_preg_parser::TEMPLATEPARSELEAF || $token->type === qtype_preg_parser::TEMPLATEOPENBRACK);
            }
        }
        return $result;
    }

    public static function char_escape_sequences_outside_charset() {
        return array('\a',
                     '\c',
                     '\e',
                     '\f',
                     '\n',
                     '\r',
                     '\t',
                     '\0',
                     // \ddd
                     '\o',
                     '\x');
    }

    public static function char_escape_sequences_inside_charset() {
        return array('\a',
                     '\b',
                     '\c',
                     '\e',
                     '\f',
                     '\n',
                     '\r',
                     '\t',
                     '\0',
                     // \ddd
                     '\o',
                     '\x');
    }

    public static function code_of_char_escape_sequence($seq) {
        static $codes = array('\a' => 0x07,
                     '\b' => 0x08,
                     /*'\c',*/
                     '\e' => 0x1B,
                     '\f' => 0x0C,
                     '\n' => 0x0A,
                     '\r' => 0x0D,
                     '\t' => 0x09,
                     // \0dd
                     // \ddd
                     // \o
                     // \x
                        );

        $len = core_text::strlen($seq);

        if ($len < 2) {
            return null;
        }

        $octal = core_text::substr($seq, 1);
        if (self::ctype_octal($octal)) {
            return octdec($octal);
        }

        if (array_key_exists($seq, $codes)) {
            return $codes[$seq];
        }

        if ($seq[1] == 'c') {
            $x = core_text::strtoupper(core_text::substr($seq, 2));
            $code = core_text::utf8ord($x);
            if ($code > 127) {
                return null;
            }
            $code ^= 0x40;
            return $code;
        }

        if ($seq[1] == '0') {
            if ($len == 2) {
                return 0;
            }
            $start = 2;
            $end = $len - 1;
            return octdec(core_text::substr($seq, $start, $end - $start + 1));
        }

        if ($seq[1] == 'o') {
            $start = 3;
            $end = $len - 2;    // always followed by {...}
            return octdec(core_text::substr($seq, $start, $end - $start + 1));
        }

        if ($seq[1] == 'x') {
            if ($len == 2) {
                return 0;
            }
            $start = 2;
            $end = $len - 1;
            if ($seq[2] == '{') {
                $start++;
                $end--;
            }
            return hexdec(core_text::substr($seq, $start, $end - $start + 1));
        }

        return null;
    }

    public function get_skipped_positions() {
        return $this->skipped_positions;
    }

    public function get_all_modifiers() {
        return $this->allmodifiers;
    }

    public function get_error_nodes() {
        return $this->errors;
    }

    public function get_last_subexpr() {
        return $this->lastsubexpr;
    }

    public function get_max_subexpr() {
        return $this->maxsubexpr;
    }

    public function get_subexpr_name_to_number_map() {
        return $this->subexpr_name_to_number_map;
    }

    public function get_nodes_with_subexpr_refs() {
        return $this->nodes_with_subexpr_refs;
    }

    public function get_subexpr_refs_map() {
        return $this->subexpr_refs_map;
    }

    public function set_options($options) {
        $this->options = $options;
        $this->modify_top_options_stack_item($options->modifiers, 0);
    }

    protected static function ctype_octal($str) {
        $str = new qtype_poasquestion\string($str);
        for ($i = 0; $i < $str->length(); $i++) {
            $ch = $str[$i];
            if (!ctype_digit($ch) || (int)$ch > 7) {
                return false;
            }
        }
        return true;
    }

    protected function modify_top_options_stack_item($set, $unset) {
        $errors = array();

        // Setting and unsetting modifier at the same time is error.
        $setunset = $set & $unset;
        if ($setunset) {
            foreach (qtype_preg_handling_options::get_all_modifiers() as $mod) {
                if ($mod & $setunset) {
                    $modname = qtype_preg_handling_options::modifier_to_char($mod);
                    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SET_UNSET_MODIFIER, $modname);
                    $errors[] = $error;
                }
            }
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // Unset and set local modifiers.
        $stackitem = end($this->opt_stack);
        $stackitem->options->unset_modifier($unset);
        $stackitem->options->set_modifier($set);

        foreach (qtype_preg_handling_options::get_all_modifiers() as $mod) {
            if ($set & $mod && !in_array($mod, $this->allmodifiers)) {
                $this->allmodifiers[] = $mod;
            }
        }

        return null;
    }

    /**
     * Should be called when any kind of opening paren occurs
     */
    protected function push_options_stack_item($dup_subexpr_number = -1) {
        $newitem = clone end($this->opt_stack);
        $newitem->dup_subexpr_name = null;   // Reset it anyway.
        $newitem->parennum = count($this->opt_stack);
        $newitem->dup_subexpr_number = $dup_subexpr_number;
        $this->opt_stack[] = $newitem;
    }

    /**
     * Should be called when a closing paren occurs
     */
    protected function pop_options_stack_item() {
        if (count($this->opt_stack) < 2) {
            // Stack should always contain at least 1 item.
            // This is a syntax error, will be reported by parser.
            return;
        }
        array_pop($this->opt_stack);
        // Check if we got out of a (?|...) block. If so, reset subpattern numeration to the max occurred number.
        $topitem = end($this->opt_stack);
        if ($topitem->dup_subexpr_number == -1) {
            $this->lastsubexpr = $this->maxsubexpr;
        }
    }

    /**
     * Sets modifiers for the given node using the top stack item.
     */
    protected function set_node_modifiers(&$node) {
        $topitem = end($this->opt_stack);
        if (is_a($node, 'qtype_preg_leaf')) {
            $node->caseless = $topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_CASELESS);
        }
    }

    protected function current_position_for_node() {
        $position = new qtype_preg_position($this->yychar, $this->yychar + $this->yylength() - 1,
                                       $this->yyline, $this->yyline,
                                       $this->yycol, $this->yycol + $this->yylength() - 1);
        return $position;
    }

    protected function form_error($subtype, $addinfo, $addtonode = null) {
        // Create the error node itself.
        $error = new qtype_preg_node_error($subtype, htmlspecialchars($addinfo));
        $error->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($addinfo)));

        // Add the node to the lexer's errors array.
        // Also add it to $addtonode if specified.
        $this->errors[] = $error;
        if ($addtonode !== null) {
            $addtonode->errors[] = $error;
        }
        return $error;
    }

    /**
     * Returns a quantifier token.
     */
    protected function form_quant($text, $infinite, $leftborder, $rightborder, $lazy, $greedy, $possessive) {
        $node = $infinite
              ? new qtype_preg_node_infinite_quant($leftborder, $lazy, $greedy, $possessive)
              : new qtype_preg_node_finite_quant($leftborder, $rightborder, $lazy, $greedy, $possessive);

        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));

        if (!$infinite && $leftborder > $rightborder) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_INCORRECT_QUANT_RANGE, $leftborder . ',' . $rightborder, $node);
        }
        return new JLexToken(qtype_preg_parser::QUANT, $node);
    }

    /**
     * Returns a control sequence token.
     */
    protected function form_control($text) {
        // Error: missing ) at end.
        if (qtype_preg_unicode::substr($text, $this->yylength() - 1, 1) !== ')') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_CONTROL_ENDING, $text);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }

        $pos = $this->current_position_for_node();
        $ui = array(new qtype_preg_userinscription($text));

        switch ($text) {
        case '(*ACCEPT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ACCEPT);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*FAIL)':
        case '(*F)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_FAIL);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*COMMIT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_COMMIT);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*THEN)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_THEN);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*SKIP)':
        case '(*SKIP:)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_SKIP);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*PRUNE)':
        case '(*PRUNE:)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_PRUNE);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*CR)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_CR);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*LF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_LF);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*CRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_CRLF);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*ANYCRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ANYCRLF);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*ANY)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_ANY);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*BSR_ANYCRLF)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_BSR_ANYCRLF);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*BSR_UNICODE)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_BSR_UNICODE);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*NO_START_OPT)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_NO_START_OPT);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*UTF8)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UTF8);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*UTF16)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UTF16);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        case '(*UCP)':
            $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_UCP);
            $node->set_user_info($pos, $ui);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        default:
            $delimpos = qtype_preg_unicode::strpos($text, ':');

            // Error: unknown control sequence.
            if ($delimpos === false) {
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE, $text);
                return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
            }

            // There is a parameter separated by ":"
            $subtype = qtype_preg_unicode::substr($text, 2, $delimpos - 2);
            $name = qtype_preg_unicode::substr($text, $delimpos + 1, $this->yylength() - $delimpos - 2);

            // Error: empty name.
            if ($name === '') {
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED, $text);
                return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
            }

            if ($subtype === 'MARK' || $delimpos === 2) {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $ui);
                return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
            } else if ($subtype === 'PRUNE') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $ui);
                $node2 = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_PRUNE);
                $node2->set_user_info($pos, $ui);
                return array(new JLexToken(qtype_preg_parser::PARSELEAF, $node),
                             new JLexToken(qtype_preg_parser::PARSELEAF, $node2));
            } else if ($subtype === 'SKIP') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_SKIP_NAME, $name);
                $node->set_user_info($pos, $ui);
                return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
            } else if ($subtype === 'THEN') {
                $node = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_MARK_NAME, $name);
                $node->set_user_info($pos, $ui);
                $node2 = new qtype_preg_leaf_control(qtype_preg_leaf_control::SUBTYPE_THEN);
                $node2->set_user_info($pos, $ui);
                return array(new JLexToken(qtype_preg_parser::PARSELEAF, $node),
                             new JLexToken(qtype_preg_parser::PARSELEAF, $node2));
            }

            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_CONTROL_SEQUENCE, $text);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }
    }

    /**
     * Returns a named subexpression token.
     */
    protected function form_named_subexpr($text, $name) {
        // Error: empty name.
        if ($name === '') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED, $text);
            return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
        }

        // Is it a duplicate (by name) of already existing subexpression?
        $maxsubexprbefore = $this->maxsubexpr;
        $nameexists = array_key_exists($name, $this->subexpr_name_to_number_map);

        // Are we inside a (?| group?
        $topitem = end($this->opt_stack);
        $insidedup = ($topitem->dup_subexpr_number !== -1);

        $this->push_options_stack_item();

        // Map name to number.
        $number = $this->map_subexpression($name);
        if (is_object($number)) {
            // Error: subexpressions with same names should have same numbers.
            return new JLexToken(qtype_preg_parser::OPENBRACK, $number);  // $number contains the error object.
        }

        if ($insidedup && $topitem->dup_subexpr_name === null) {
            // First occurence of a named subexpression inside a (?| group.
            $topitem->dup_subexpr_name = $name;
        }

        // If all is fine, fill the another, inverse, map.
        $this->subexpr_number_to_name_map[$number] = $name;

        $isduplicate = $nameexists || $number <= $maxsubexprbefore;
        return $this->form_subexpr($text, qtype_preg_node_subexpr::SUBTYPE_SUBEXPR, $number, $name, $isduplicate);
    }

    protected function form_subexpr($text, $subtype, $number = null, $name = null, $isduplicate = false) {
        $node = new qtype_preg_node_subexpr($subtype, $number, $name, $isduplicate);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        return new JLexToken(qtype_preg_parser::OPENBRACK, $node);
    }

    /**
     * Returns a conditional subexpression (number of name condition) token.
     */
    protected function form_numeric_or_named_cond_subexpr($text, $number, $name, $ending = '') {
        $this->push_options_stack_item();

        // Error: unclosed condition.
        if (qtype_preg_unicode::substr($text, $this->yylength() - strlen($ending)) != $ending) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING, $text);
            return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
        }

        $node = new qtype_preg_node_cond_subexpr(qtype_preg_node_cond_subexpr::SUBTYPE_SUBEXPR, $number, $name);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));

        if (is_integer($number) && $number === 0) {
            // Error: reference to the whole expression.
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CONSUBEXPR_ZERO_CONDITION, '', $node);
        } else if (is_string($name) && $name === '') {
            // Error: assertion expected.
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED, '', $node);
        }

        $this->nodes_with_subexpr_refs[] = $node;

        $closebr = new qtype_preg_lexem();
        $closebr->set_user_info($this->current_position_for_node());

        return array(new JLexToken(qtype_preg_parser::CONDSUBEXPR, $node),
                     new JLexToken(qtype_preg_parser::PARSELEAF, null),        // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
                     new JLexToken(qtype_preg_parser::CLOSEBRACK, $closebr));  // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
    }

    /**
     * Returns a conditional subexpression (recursion condition) token.
     */
    protected function form_recursive_cond_subexpr($text, $number, $name) {
        $this->push_options_stack_item();

        // Error: unclosed condition.
        if (qtype_preg_unicode::substr($text, $this->yylength() - 1) != ')') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING, $text);
            return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
        }

        $node = new qtype_preg_node_cond_subexpr(qtype_preg_node_cond_subexpr::SUBTYPE_RECURSION, $number, $name);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));

        if ($name === '') {
            // Error: assertion expected.
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CONDSUBEXPR_ASSERT_EXPECTED, '', $node);
        }

        $this->nodes_with_subexpr_refs[] = $node;

        $closebr = new qtype_preg_lexem();
        $closebr->set_user_info($this->current_position_for_node());

        return array(new JLexToken(qtype_preg_parser::CONDSUBEXPR, $node),
                     new JLexToken(qtype_preg_parser::PARSELEAF, null),        // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
                     new JLexToken(qtype_preg_parser::CLOSEBRACK, $closebr));  // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
    }

    /**
     * Returns a conditional subexpression (assertion condition) token.
     */
    protected function form_assert_cond_subexpr($text, $subtype) {
        $this->push_options_stack_item();
        $this->push_options_stack_item();

        $node = new qtype_preg_node_cond_subexpr($subtype);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        return new JLexToken(qtype_preg_parser::CONDSUBEXPR, $node);
    }

    /**
     * Returns a conditional subexpression (define condition) token.
     */
    protected function form_define_cond_subexpr($text) {
        $this->push_options_stack_item();

        // Error: unclosed condition.
        if (qtype_preg_unicode::substr($text, $this->yylength() - 1) != ')') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_CONDSUBEXPR_ENDING, $text);
            return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
        }

        $node = new qtype_preg_node_cond_subexpr(qtype_preg_node_cond_subexpr::SUBTYPE_DEFINE);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));

        $closebr = new qtype_preg_lexem();
        $closebr->set_user_info($this->current_position_for_node());

        return array(new JLexToken(qtype_preg_parser::CONDSUBEXPR, $node),
                     new JLexToken(qtype_preg_parser::PARSELEAF, null),        // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
                     new JLexToken(qtype_preg_parser::CLOSEBRACK, $closebr));  // Fictive lexem, used to satisfy grammar for both simple and assertion conditions
    }

    /**
     * Returns a named backreference token.
     */
    protected function form_named_backref($text, $namestartpos, $opentype, $closetype) {
        // Error: missing opening characters.
        if (qtype_preg_unicode::substr($text, $namestartpos - 1, 1) !== $opentype) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_BEGINNING, $opentype);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }

        // Error: missing closing characters.
        if (qtype_preg_unicode::substr($text, $this->yylength() - 1, 1) !== $closetype) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_BACKREF_ENDING, $closetype);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }

        $name = qtype_preg_unicode::substr($text, $namestartpos, $this->yylength() - $namestartpos - 1);

        // Error: empty name.
        if ($name === '') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED, $text);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }

        return $this->form_backref($text, null, $name);
    }

    /**
     * Returns a backreference token.
     */
    protected function form_backref($text, $number, $name) {
        $node = new qtype_preg_leaf_backref($number, $name);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        $this->set_node_modifiers($node);
        $this->nodes_with_subexpr_refs[] = $node;
        return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
    }

    /**
     * Returns a simple assertion token.
     */
    protected function form_simple_assertion($text, $classname, $negative = false) {
        $node = new $classname($negative);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text, $node->subtype)));
        $this->set_node_modifiers($node);
        return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
    }

    /**
     * Returns a character set token.
     */
    protected function form_charset($text, $type, $data, $negative = false) {
        $node = new qtype_preg_leaf_charset();
        $uitype = $type === qtype_preg_charset_flag::TYPE_SET ? null : $data;
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text, $uitype)));
        $node->subtype = $type;

        $this->set_node_modifiers($node);

        if ($data !== null) {
            $flag = new qtype_preg_charset_flag;
            $flag->negative = $negative;
            if ($type == qtype_preg_charset_flag::TYPE_SET) {
                $data = new qtype_poasquestion\string($data);
            }
            $flag->set_data($type, $data);
            $node->flags = array(array($flag));
        }
        return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
    }

    /**
     * Returns a named subexpression call token.
     */
    protected function form_named_subexpr_call($text, $name) {
        // Error: empty name.
        if ($name === '') {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SUBEXPR_NAME_EXPECTED, $text);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        }
        return $this->form_subexpr_call($text, null, $name);
    }

    /**
     * Returns a subexpression call token.
     */
    protected function form_subexpr_call($text, $number, $name) {
        $node = new qtype_preg_leaf_subexpr_call($number, $name);
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        $this->set_node_modifiers($node);
        $this->nodes_with_subexpr_refs[] = $node;
        return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
    }

    /**
     * Forms an interval from sequences like a-z, 0-9, etc. If a string contains
     * something like "x-z" in the end, it will be converted to "xyz".
     */
    protected function expand_charset_range() {
        // Don't expand anything if inside a \Q...\E sequence.
        if ($this->qe_sequence_length > 1) {
            return;
        }
        // Check if there are enough characters in before.
        if ($this->charset_count < 3 || qtype_preg_unicode::substr($this->charset_set, $this->charset_count - 2, 1) != '-') {
            return;
        }
        $startchar = qtype_preg_unicode::substr($this->charset_set, $this->charset_count - 3, 1);
        $endchar = qtype_preg_unicode::substr($this->charset_set, $this->charset_count - 1, 1);

        // Modify userinscription;
        $userinscriptionend = array_pop($this->charset->userinscription);
        array_pop($this->charset->userinscription);
        $userinscriptionstart = array_pop($this->charset->userinscription);
        $this->charset->userinscription[] = new qtype_preg_userinscription($userinscriptionstart->data . '-' . $userinscriptionend->data);

        if (core_text::utf8ord($startchar) <= core_text::utf8ord($endchar)) {
            // Replace last 3 characters by all the characters between them.
            $this->charset_set = qtype_preg_unicode::substr($this->charset_set, 0, $this->charset_count - 3);
            $this->charset_count -= 3;
            $curord = core_text::utf8ord($startchar);
            $endord = core_text::utf8ord($endchar);
            while ($curord <= $endord) {
                $tmp = qtype_preg_unicode::code2utf8($curord++);
                if ($tmp == '-') {
                    $this->charset_set = $tmp . $this->charset_set;
                } else {
                    $this->charset_set .= $tmp;
                }
                $this->charset_count++;
            }
        } else {
            // Delete last 3 characters.
            $this->charset_count -= 3;
            $this->charset_set = qtype_preg_unicode::substr($this->charset_set, 0, $this->charset_count);
            // Form the error node.
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_INCORRECT_CHARSET_RANGE, $startchar . '-' . $endchar, $this->charset);
            $error->set_user_info($this->current_position_for_node());
        }
    }

    /**
     * Adds a named subexpression to the map.
     */
    protected function map_subexpression($name) {
        // Does the given name exist?
        $exists = isset($this->subexpr_name_to_number_map[$name]);
        if (!$exists) {
            // This subexpression does not exists, all is OK. Almost.
            $number = $this->lastsubexpr + 1;

            if (isset($this->subexpr_number_to_name_map[$number])) {
                // There can be situations like (?|(?<name1>)|(?<name2>)). By this moment name2 doesn't exist, but this is an error.
                $assumed_name = $this->subexpr_number_to_name_map[$number];
                if ($name != $assumed_name) {
                    // Subexpression has wrong name.
                    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_DIFFERENT_SUBEXPR_NAMES, $name);
                    return $error;
                }
            }

            $this->subexpr_name_to_number_map[$name] = $number;
            $this->lastsubexpr++;
            $this->maxsubexpr = max($this->maxsubexpr, $this->lastsubexpr);
            return $number;
        }

        // This subexpression does exist.
        $number = $this->subexpr_name_to_number_map[$name];
        $topitem = end($this->opt_stack);
        $modJ = $topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_DUPNAMES);

        if ($modJ) {
            // Just keep on increasing numbers.
            $this->lastsubexpr++;
            $this->maxsubexpr = max($this->maxsubexpr, $this->lastsubexpr);
            return $this->lastsubexpr;
        } else {
            // Check if the number is correct.
            if ($number !== $this->lastsubexpr + 1) {
                // Two subexpressions with same number in a row is error.
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_DUPLICATE_SUBEXPR_NAMES, $name, '');
                return $error;
            }
            $this->lastsubexpr = $number;
            return $number;
        }
    }

    /**
     * Calculates the character for a \cx sequence.
     * @param cx the sequence itself.
     * @return character corresponding to the given sequence.
     */
    protected function calculate_cx($cx) {
        $code = self::code_of_char_escape_sequence($cx);
        if ($code === null) {
            return null;
        }
        return qtype_preg_unicode::code2utf8($code);
    }

    /**
     * Adds a flag to the lexer's charset when lexer is in the YYCHARSET state.
     * @param userinscription a string typed by user and consumed by lexer.
     * @param type type of the flag, should be a constant of qtype_preg_leaf_charset.
     * @param data can contain either subtype of a flag or characters for a charset.
     * @param negative is this flag negative.
     * @param appendtoend if true, new characters are concatenated from right, from left otherwise.
     */
    protected function add_flag_to_charset($text, $type, $data, $negative = false, $appendtoend = true) {
        switch ($type) {
        case qtype_preg_charset_flag::TYPE_SET:
            $this->charset->userinscription[] = new qtype_preg_userinscription($text);
            $this->charset_count++;
            if ($appendtoend) {
                $this->charset_set .= $data;
            } else {
                $this->charset_set = $data . $this->charset_set;
            }
            $this->expand_charset_range();
            break;
        case qtype_preg_charset_flag::TYPE_FLAG:
            $this->charset->userinscription[] = new qtype_preg_userinscription($text, $data);
            $flag = new qtype_preg_charset_flag;
            $flag->set_data($type, $data);
            $flag->negative = $negative;
            $this->charset->flags[] = array($flag);
            break;
        }
    }

    protected function string_to_tokens($str) {
        $res = array();
        for ($i = 0; $i < qtype_preg_unicode::strlen($str); $i++) {
            $char = qtype_preg_unicode::substr($str, $i, 1);
            $res[] = $this->form_charset($char, qtype_preg_charset_flag::TYPE_SET, $char);
        }
        return $res;
    }

    /**
     * Returns a unicode property flag type corresponding to the consumed string.
     * @param str string consumed by the lexer, defines the property itself.
     * @return a constant of qtype_preg_leaf_charset if this property is known, null otherwise.
     */
    protected function get_uprop_flag($str) {
        if ($str == 'L&') {
            // This is an exception
            return qtype_preg_charset_flag::UPROP_Llut;
        }
        $constname = "qtype_preg_charset_flag::UPROP_$str";
        if (defined($constname)) {
            return $str;
        }
        return null;
    }

    protected function assertion_for_circumflex() {
        $topitem = end($this->opt_stack);
        if ($this->options->preserveallnodes || $topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_MULTILINE)) {
            // The ^ assertion is used "as is" only in multiline mode. Or if preserveallnodes is true.
            return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_circumflex');
        } else {
            // Default case: the same as \A.
            return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_esc_a');
        }
    }

    protected function assertion_for_dollar() {
        $topitem = end($this->opt_stack);
        if ($this->options->preserveallnodes || $topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_MULTILINE)) {
            // The $ assertion is used "as is" only in multiline mode. Or if preserveallnodes is true.
            return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_dollar');
        } else if ($topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_DOLLAR_ENDONLY)) {
            // Not multiline, but dollar endonly; the same as \z.
            return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_small_esc_z');
        } else {
            // Default case: the same as \Z.
            return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_capital_esc_z');
        }
    }
%}

%%

<YYINITIAL> {WHITESPACE} {                       /* Whitespace */
    $topitem = end($this->opt_stack);
    if (!$topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_EXTENDED)) {
        // If the "x" modifier is not set, return all the whitespaces.
        $text = $this->yytext();
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, $text);
    }
}
<YYINITIAL> "#" {                                /* Comment beginning when modifier x is set */
    $topitem = end($this->opt_stack);
    if ($topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_EXTENDED)) {
        $this->state_begin_position = $this->current_position_for_node();
        $this->yybegin(self::YYCOMMENTEXT);
    } else {
        return $this->form_charset('#', qtype_preg_charset_flag::TYPE_SET, '#');
    }
}
<YYCOMMENTEXT> [^\n]* {
    // Do nothing.
}
<YYCOMMENTEXT> \n {
    $this->state_begin_position = null;
    $this->yybegin(self::YYINITIAL);
}




<YYINITIAL> "?"{QUANTTYPE} {                     // ?     Quantifier 0 or 1
    $text = $this->yytext();
    $greedy = $this->yylength() === 1;
    $lazy = qtype_preg_unicode::substr($text, 1, 1) === '?';
    $possessive = !$greedy && !$lazy;
    return $this->form_quant($text, false, 0, 1, $lazy, $greedy, $possessive);
}
<YYINITIAL> "*"{QUANTTYPE} {                     // *     Quantifier 0 or more
    $text = $this->yytext();
    $greedy = $this->yylength() === 1;
    $lazy = qtype_preg_unicode::substr($text, 1, 1) === '?';
    $possessive = !$greedy && !$lazy;
    return $this->form_quant($text, true, 0, null, $lazy, $greedy, $possessive);
}
<YYINITIAL> "+"{QUANTTYPE} {                     // +     Quantifier 1 or more
    $text = $this->yytext();
    $greedy = $this->yylength() === 1;
    $lazy = qtype_preg_unicode::substr($text, 1, 1) === '?';
    $possessive = !$greedy && !$lazy;
    return $this->form_quant($text, true, 1, null, $lazy, $greedy, $possessive);
}
<YYINITIAL> "{"[0-9]+","[0-9]+"}"{QUANTTYPE} {   // {n,m} Quantifier at least n, no more than m
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_preg_unicode::substr($text, -1);
    $greedy = $lastchar === '}';
    $lazy = $lastchar === '?';
    $possessive = !$greedy && !$lazy;
    $greedy || $textlen--;
    $delimpos = qtype_preg_unicode::strpos($text, ',');
    $leftborder = (int)qtype_preg_unicode::substr($text, 1, $delimpos - 1);
    $rightborder = (int)qtype_preg_unicode::substr($text, $delimpos + 1, $textlen - 2 - $delimpos);
    return $this->form_quant($text, false, $leftborder, $rightborder, $lazy, $greedy, $possessive);
}
<YYINITIAL> "{"[0-9]+",}"{QUANTTYPE} {           // {n,}  Quantifier n or more
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_preg_unicode::substr($text, -1);
    $greedy = $lastchar === '}';
    $lazy = $lastchar === '?';
    $possessive = !$greedy&& !$lazy;
    $greedy || $textlen--;
    $leftborder = (int)qtype_preg_unicode::substr($text, 1, $textlen - 1);
    return $this->form_quant($text, true, $leftborder, null, $lazy, $greedy, $possessive);
}
<YYINITIAL> "{"[0-9]+"}"{QUANTTYPE} {            // {n}    Quantifier exactly n
    $text = $this->yytext();
    $textlen = $this->yylength();
    $lastchar = qtype_preg_unicode::substr($text, -1);
    $greedy = $lastchar === '}';
    $lazy = $lastchar === '?';
    $possessive = !$greedy&& !$lazy;
    $greedy || $textlen--;
    $count = (int)qtype_preg_unicode::substr($text, 1, $textlen - 1);
    return $this->form_quant($text, false, $count, $count, $lazy, $greedy, $possessive);
}




<YYINITIAL> \\[1-9][0-9]?[0-9]? {      /* \n              Backreference by number (can be ambiguous) */
    $text = $this->yytext();
    $str = qtype_preg_unicode::substr($text, 1);
    if ((int)$str < 8 || ((int)$str <= $this->maxsubexpr && (int)$str < 100)) {
        // Return a backreference.
        return $this->form_backref($text, (int)$str, null);
    }
    // Return a character.
    $octal = '';
    $failed = false;
    for ($i = 0; !$failed && $i < qtype_preg_unicode::strlen($str); $i++) {
        $tmp = qtype_preg_unicode::substr($str, $i, 1);
        if ((int)$tmp < 8) {
            $octal = $octal . $tmp;
        } else {
            $failed = true;
        }
    }
    $tail = qtype_preg_unicode::substr($str, qtype_preg_unicode::strlen($octal));
    $result = array();
    if ($octal != '') {
        // Character by octal code.
        $charset = $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(octdec($octal)));
        $charset->value->position->indlast -= core_text::strlen($tail);
        $charset->value->position->collast -= core_text::strlen($tail);
        $charset->value->userinscription = array(new qtype_preg_userinscription($tail == $str ? '\\' : '\\' . $octal));
        $result[] = $charset;
    }
    if (qtype_preg_unicode::strlen($tail) > 0) {
        // Plain digits
        $tokens = $this->string_to_tokens($tail);
        $offset = core_text::strlen($text) - core_text::strlen($tail);
        foreach ($tokens as $token) {
            $token->value->position = new qtype_preg_position($this->yychar + $offset, $this->yychar + $offset,
                                            $this->yyline, $this->yyline,
                                            $this->yycol + $offset, $this->yycol + $offset);
            $offset++;
        }
        $result = array_merge($result, $tokens);
    }
    return count($result) > 1
           ? $result
           : $result[0];
}
<YYINITIAL> "\g"-?[0-9][0-9]? {        /* \gn \g-n        Backreference by number */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 2);
    // Convert relative backreferences to absolute.
    if ($number < 0) {
        $number = $this->lastsubexpr + $number + 1;
    }
    return $this->form_backref($text, $number, null);
}
<YYINITIAL> "\g{"-?[0-9][0-9]?"}" {    /* \g{n} \g{-n}    Backreference by number */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    // Convert relative backreferences to absolute.
    if ($number < 0) {
        $number = $this->lastsubexpr + $number + 1;
    }
    return $this->form_backref($text, $number, null);
}
<YYINITIAL> "\k<"{ALNUM}*">" {         /* \k<name>        Backreference by name (Perl) */
    return $this->form_named_backref($this->yytext(), 3, '<', '>');
}
<YYINITIAL> "\k'"{ALNUM}*"'" {         /* \k'name'        Backreference by name (Perl) */
    return $this->form_named_backref($this->yytext(), 3, '\'', '\'');
}
<YYINITIAL> "\g{"{ALNUM}*"}" {         /* \g{name}        Backreference by name (Perl) */
    return $this->form_named_backref($this->yytext(), 3, '{', '}');
}
<YYINITIAL> "\k{"{ALNUM}*"}" {         /* \k{name}        Backreference by name (.NET) */
    return $this->form_named_backref($this->yytext(), 3, '{', '}');
}
<YYINITIAL> "(?P="{ALNUM}*")" {        /* (?P=name)       Backreference by name (Python) */
    return $this->form_named_backref($this->yytext(), 4, '=', ')');
}




<YYINITIAL> "(" {                      /* (...)           Subexpression */
    $this->push_options_stack_item();
    $this->lastsubexpr++;
    $isduplicate = $this->lastsubexpr <= $this->maxsubexpr;
    $this->maxsubexpr = max($this->maxsubexpr, $this->lastsubexpr);
    return $this->form_subexpr('(', qtype_preg_node_subexpr::SUBTYPE_SUBEXPR, $this->lastsubexpr, null, $isduplicate);
}
<YYINITIAL> "(?<"{ALNUM}*">"? {         /* (?<name>...)     Named subexpression (Perl) */
    $text = $this->yytext();
    $last = qtype_preg_unicode::substr($text, $this->yylength() - 1, 1);
    if ($last != '>') {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING, $text);
        return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
    }
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_named_subexpr($text, $name);
}
<YYINITIAL> "(?'"{ALNUM}*"'"? {         /* (?'name'...)     Named subexpression (Perl) */
    $text = $this->yytext();
    $last = qtype_preg_unicode::substr($text, $this->yylength() - 1, 1);
    if ($last != '\'') {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING, $text);
        return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
    }
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_named_subexpr($text, $name);
}
<YYINITIAL> "(?P<"{ALNUM}*">"? {        /* (?P<name>...)    Named subexpression (Python) */
    $text = $this->yytext();
    $last = qtype_preg_unicode::substr($text, $this->yylength() - 1, 1);
    if ($last != '>') {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING, $text);
        return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
    }
    $name = qtype_preg_unicode::substr($text, 4, $this->yylength() - 5);
    return $this->form_named_subexpr($text, $name);
}
<YYINITIAL> "(?:" {                    /* (?:...)         Non-capturing group */
    $this->push_options_stack_item();
    return $this->form_subexpr('(?:', qtype_preg_node_subexpr::SUBTYPE_GROUPING);
}
<YYINITIAL> "(?|" {                    /* (?|...)         Non-capturing group, duplicate subexpression numbers */
    // Save the top-level subexpression number.
    $this->push_options_stack_item($this->lastsubexpr);
    return $this->form_subexpr('(?|', qtype_preg_node_subexpr::SUBTYPE_GROUPING);
}
<YYINITIAL> "(?>" {                    /* (?>...)         Atomic, non-capturing group */
    $this->push_options_stack_item();
    return $this->form_subexpr('(?>', qtype_preg_node_subexpr::SUBTYPE_ONCEONLY);
}
<YYINITIAL> ")" {
    $this->pop_options_stack_item();
    $closebr = new qtype_preg_lexem();
    $closebr->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription(')')));
    return new JLexToken(qtype_preg_parser::CLOSEBRACK, $closebr);
}




<YYINITIAL> "(?#" {                                        /* (?#....) Comment beginning */
    $this->comment = '';
    $this->comment_length = 0;
    $this->state_begin_position = $this->current_position_for_node();
    $this->yybegin(self::YYCOMMENT);
}
<YYCOMMENT> [^)\\]* {                                      /* Comment body: all characters until ')' or '\' found */
    $this->comment .= $this->yytext();
    $this->comment_length += $this->yylength();
}
<YYCOMMENT> "\)"|\\\\ {                                    /* Comment body: \) or \\ */
    $this->comment .= $this->yytext();
    $this->comment_length += $this->yylength();
}
<YYCOMMENT> [^)] {                                         /* Comment body: not ')' */
    $this->comment .= $this->yytext();
    $this->comment_length += $this->yylength();
}
<YYCOMMENT> ")" {                                          /* Comment ending */
    $result = null;
    if ($this->options->parsetemplates && $this->comment_length > 0) {
        $position = new qtype_preg_position($this->state_begin_position->indfirst, $this->yychar + $this->yylength() - 1,
                                            $this->state_begin_position->linefirst, $this->yyline,
                                            $this->state_begin_position->colfirst, $this->yycol + $this->yylength() - 1);
        if ($this->comment === '##,') {
            // Template params separator
            $sep = new qtype_preg_lexem();
            $sep->set_user_info($position, array(new qtype_preg_userinscription('(?##,)')));
            $result = new JLexToken(qtype_preg_parser::TEMPLATESEP, $sep);

        } else if ($this->comment === '##>') {
            // Template close bracket
            $closebr = new qtype_preg_lexem();
            $closebr->set_user_info($position, array(new qtype_preg_userinscription('(?###>)')));
            $result = new JLexToken(qtype_preg_parser::TEMPLATECLOSEBRACK, $closebr);
        } else if (core_text::substr($this->comment, 0, 2) === '##' && core_text::substr($this->comment, -1) === '<') {
            // Template open bracket
            $name = core_text::substr($this->comment, 2, $this->comment_length - 3);
            $available = qtype_preg\template::available_templates();
            $node = new qtype_preg_node_template($name);
            $node->set_user_info($position, array(new qtype_preg_userinscription('(?#' . $this->comment . ')')));
            if (!array_key_exists($name, $available)) {
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE, $name, $node);
                $error->position = $position;
                $node->errors = array($error);
            }
            $result = new JLexToken(qtype_preg_parser::TEMPLATEOPENBRACK, $node);
        } else if (core_text::substr($this->comment, 0, 2) === '##') {
            // Template leaf
            $name = core_text::substr($this->comment, 2);
            $available = qtype_preg\template::available_templates();
            $node = new qtype_preg_leaf_template($name);
            $node->set_user_info($position, array(new qtype_preg_userinscription('(?#' . $this->comment . ')')));
            if (!array_key_exists($name, $available)) {
                $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_TEMPLATE, $name, $node);
                $error->position = $position;
                $node->errors = array($error);
            }
            $result = new JLexToken(qtype_preg_parser::TEMPLATEPARSELEAF, $node);
        }
    }
    $this->comment = '';
    $this->comment_length = 0;
    $this->state_begin_position = null;
    $this->yybegin(self::YYINITIAL);
    if ($result !== null) {
        return $result;
    }
}




<YYINITIAL> "(?"{MODIFIER}*-?{MODIFIER}*")" {              /* (?imsxuADSUXJ-imsxuADSUXJ) Option setting */
    $text = $this->yytext();
    $delimpos = qtype_preg_unicode::strpos($text, '-');
    if ($delimpos !== false) {
        $set = qtype_preg_unicode::substr($text, 2, $delimpos - 2);
        $unset = qtype_preg_unicode::substr($text, $delimpos + 1, $this->yylength() - $delimpos - 2);
    } else {
        $set = qtype_preg_unicode::substr($text, 2, $this->yylength() - 3);
        $unset = '';
    }
    $setflags = qtype_preg_handling_options::string_to_modifiers($set);
    $unsetflags = qtype_preg_handling_options::string_to_modifiers($unset);
    $errors = $this->modify_top_options_stack_item($setflags, $unsetflags);
    if ($this->options->preserveallnodes) {
        $node = new qtype_preg_leaf_options(new qtype_poasquestion\string($set), new qtype_poasquestion\string($unset));
        $node->errors = $errors;
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $node);
    } else {
        $this->skipped_positions[] = $this->current_position_for_node();
    }
}
<YYINITIAL> "(?"{MODIFIER}*-?{MODIFIER}*":" {              /* (?imsxuADSUXJ-imsxuADSUXJ: Subexpression with option setting */
    $text = $this->yytext();
    $delimpos = qtype_preg_unicode::strpos($text, '-');
    if ($delimpos !== false) {
        $set = qtype_preg_unicode::substr($text, 2, $delimpos - 2);
        $unset = qtype_preg_unicode::substr($text, $delimpos + 1, $this->yylength() - $delimpos - 2);
    } else {
        $set = qtype_preg_unicode::substr($text, 2, $this->yylength() - 3);
        $unset = '';
    }
    $setflags = qtype_preg_handling_options::string_to_modifiers($set);
    $unsetflags = qtype_preg_handling_options::string_to_modifiers($unset);
    $this->push_options_stack_item();
    $errors = $this->modify_top_options_stack_item($setflags, $unsetflags);
    if ($this->options->preserveallnodes) {
        $res = array();
        $res[] = $this->form_subexpr($text, qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $node = new qtype_preg_leaf_options(new qtype_poasquestion\string($set), new qtype_poasquestion\string($unset));
        $node->errors = $errors;
        $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
        $res[] = new JLexToken(qtype_preg_parser::PARSELEAF, $node);
        return $res;
    } else {
        return $this->form_subexpr($text, qtype_preg_node_subexpr::SUBTYPE_GROUPING);
    }
}




<YYINITIAL> "(?=" {                    /* (?=...)         Positive look ahead assertion */
    $this->push_options_stack_item();
    $node = new qtype_preg_node_assert(qtype_preg_node_assert::SUBTYPE_PLA);
    $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($this->yytext())));
    return new JLexToken(qtype_preg_parser::OPENBRACK, $node);
}
<YYINITIAL> "(?!" {                    /* (?!...)         Negative look ahead assertion */
    $this->push_options_stack_item();
    $node = new qtype_preg_node_assert(qtype_preg_node_assert::SUBTYPE_NLA);
    $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($this->yytext())));
    return new JLexToken(qtype_preg_parser::OPENBRACK, $node);
}
<YYINITIAL> "(?<=" {                   /* (?<=...)        Positive look behind assertion */
    $this->push_options_stack_item();
    $node = new qtype_preg_node_assert(qtype_preg_node_assert::SUBTYPE_PLB);
    $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($this->yytext())));
    return new JLexToken(qtype_preg_parser::OPENBRACK, $node);
}
<YYINITIAL> "(?<!" {                   /* (?<!...)        Negative look behind assertion */
    $this->push_options_stack_item();
    $node = new qtype_preg_node_assert(qtype_preg_node_assert::SUBTYPE_NLB);
    $node->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($this->yytext())));
    return new JLexToken(qtype_preg_parser::OPENBRACK, $node);
}




<YYINITIAL> "(?R)" {                   /* (?R)            Recurse whole pattern */
    $text = $this->yytext();
    return $this->form_subexpr_call($text, 0, null);
}
<YYINITIAL> "(?"[0-9]+")" {            /* (?n)            Call subexpression by absolute number */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 2, $this->yylength() - 3);
    return $this->form_subexpr_call($text, $number, null);
}
<YYINITIAL> "(?"{SIGN}[0-9]+")" {      /* (?+n) (?-n)     Call subexpression by relative number */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    if ($text[2] == '-') {
        $number = $this->lastsubexpr - $number + 1;
    } else {
        $number = $this->lastsubexpr + $number;
    }
    return $this->form_subexpr_call($text, $number, null);
}
<YYINITIAL> "(?&"{ALNUM}*")" {         /* (?&name)        Call subexpression by name (Perl) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_named_subexpr_call($text, $name);
}
<YYINITIAL> "(?P>"{ALNUM}*")" {        /* (?P>name)       Call subexpression by name (Python) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 4, $this->yylength() - 5);
    return $this->form_named_subexpr_call($text, $name);
}
<YYINITIAL> "\g<"{ALNUM}*">" {         /* \g<name>        Call subexpression by name (Oniguruma) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_named_subexpr_call($text, $name);
}
<YYINITIAL> "\g'"{ALNUM}*"'" {         /* \g'name'        Call subexpression by name (Oniguruma) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_named_subexpr_call($text, $name);
// TODO:
//         \g<n>           call subpattern by absolute number (Oniguruma)
//         \g'n'           call subpattern by absolute number (Oniguruma)
//         \g<+n>          call subpattern by relative number (PCRE extension)
//         \g'+n'          call subpattern by relative number (PCRE extension)
//         \g<-n>          call subpattern by relative number (PCRE extension)
//         \g'-n'          call subpattern by relative number (PCRE extension)
}




<YYINITIAL> "(?(DEFINE"")"? {          /* (?(DEFINE)...             Conditional subexpression - define subpattern for reference */
    $text = $this->yytext();
    // Special case: (?(DEFINE with existing subpattern named "DEFINE"
    if ($text == '(?(DEFINE)' && array_key_exists('DEFINE', $this->subexpr_name_to_number_map)) {
        return $this->form_numeric_or_named_cond_subexpr($text, null, 'DEFINE', ')');
    }
    return $this->form_define_cond_subexpr($this->yytext());
}
<YYINITIAL> "(?(?=" {                  /* (?(assert)...             Conditional subexpression - positive look ahead assertion */
    return $this->form_assert_cond_subexpr($this->yytext(), qtype_preg_node_cond_subexpr::SUBTYPE_PLA);
}
<YYINITIAL> "(?(?!" {                  /* (?(assert)...             Conditional subexpression - negative look ahead assertion */
    return $this->form_assert_cond_subexpr($this->yytext(), qtype_preg_node_cond_subexpr::SUBTYPE_NLA);
}
<YYINITIAL> "(?(?<=" {                 /* (?(assert)...             Conditional subexpression - positive look behind assertion */
    return $this->form_assert_cond_subexpr($this->yytext(), qtype_preg_node_cond_subexpr::SUBTYPE_PLB);
}
<YYINITIAL> "(?(?<!" {                 /* (?(assert)...             Conditional subexpression - negative look behind assertion */
    return $this->form_assert_cond_subexpr($this->yytext(), qtype_preg_node_cond_subexpr::SUBTYPE_NLB);
}
<YYINITIAL> "(?(R"[0-9]*")"? {         /* (?(R)... or (?(Rn)...     Conditional subexpression - overall or specific group recursion condition */
    $text = $this->yytext();
    // Special case: (?(R with existing subpattern named "R"
    if ($text == '(?(R)' && array_key_exists('R', $this->subexpr_name_to_number_map)) {
        return $this->form_numeric_or_named_cond_subexpr($text, null, 'R', ')');
    }
    $number = (int)qtype_preg_unicode::substr($text, 4, $this->yylength() - 5);
    return $this->form_recursive_cond_subexpr($text, $number, null);
}
<YYINITIAL> "(?(R&"{ALNUM}*")"? {      /* (?(name)...               Conditional subexpression - specific recursion condition */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 5, $this->yylength() - 6);
    return $this->form_recursive_cond_subexpr($text, null, $name);
}
<YYINITIAL> "(?("[0-9]+")"? {          /* (?(n)...                  Conditional subexpression - absolute reference condition */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_numeric_or_named_cond_subexpr($text, $number, null, ')');
}
<YYINITIAL> "(?("{SIGN}[0-9]+")"? {    /* (?(+n)... or (?(-n)...    Conditional subexpression - relative reference condition */
    $text = $this->yytext();
    $number = (int)qtype_preg_unicode::substr($text, 4, $this->yylength() - 5);
    if ($text[3] == '-') {
        $number = $this->lastsubexpr - $number + 1;
    } else {
        $number = $this->lastsubexpr + $number;
    }
    return $this->form_numeric_or_named_cond_subexpr($text, $number, null, ')');
}
<YYINITIAL> "(?(<"{ALNUM}*(">)")? {    /* (?(<name>)...             Conditional subexpression - named reference condition (Perl) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 4, $this->yylength() - 6);
    return $this->form_numeric_or_named_cond_subexpr($text, null, $name, '>)');
}
<YYINITIAL> "(?('"{ALNUM}*("')")? {    /* (?('name')...             Conditional subexpression - named reference condition (Perl) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 4, $this->yylength() - 6);
    return $this->form_numeric_or_named_cond_subexpr($text, null, $name, "')");
}
<YYINITIAL> "(?("{ALNUM}*")"? {        /* (?(name)...               Conditional subexpression - named reference condition (PCRE) */
    $text = $this->yytext();
    $name = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    return $this->form_numeric_or_named_cond_subexpr($text, null, $name, ")");
}




<YYINITIAL> "(*"[^)]*")"? {            /* (*...) Backtracking control sequence */
    return $this->form_control($this->yytext());
}
<YYINITIAL> "(?C"[0-9]*")"? {          /* (?Cxxx) Callout */
    $text = $this->yytext();
    if (qtype_preg_unicode::substr($text, $this->yylength() - 1, 1) !== ')') {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_CALLOUT_ENDING, $text);
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    }
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, 'Callouts are not implemented yet');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    /*$number = (int)qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    if ($number > 255) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CALLOUT_BIG_NUMBER, $text);
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    }*/
}




<YYINITIAL> "\E" {                     /* \Q...\E quotation ending */
    // Do nothing in YYINITIAL state.
}
<YYINITIAL> "\Q" {                     /* \Q...\E quotation beginning */
    $this->qe_sequence = '';
    $this->qe_sequence_length = 0;
    $this->state_begin_position = $this->current_position_for_node();
    $this->skipped_positions[] = $this->current_position_for_node();
    $this->yybegin(self::YYQEOUT);
}
<YYQEOUT> {ANY} {                      /* \Q...\E quotation body */
    $text = $this->yytext();
    $this->qe_sequence .= $text;
    $this->qe_sequence_length++;
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, $text);
}
<YYQEOUT> "\E" {                       /* \Q...\E quotation ending */
    $this->qe_sequence = '';
    $this->qe_sequence_length = 0;
    $this->state_begin_position = null;
    $this->skipped_positions[] = $this->current_position_for_node();
    $this->yybegin(self::YYINITIAL);
}




<YYINITIAL> "\g" {                     /* ERROR: missing brackets for \g */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_G, $this->yytext());
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "\k" {                     /* ERROR: missing brackets for \k */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_BRACKETS_FOR_K, $this->yytext());
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "(?P=" {                   /* ERROR: missing closing paren for (?P= */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_MISSING_SUBEXPR_NAME_ENDING, $this->yytext());
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "(?""-"? {                 /* ERROR: Unrecognized character after (? or (?- */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQH, $this->yytext());
    return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
}
<YYINITIAL> "(?<" {                    /* ERROR: Unrecognized character after (?< */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQLT, $this->yytext());
    return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
}
<YYINITIAL> "(?P" {                    /* ERROR: Unrecognized character after (?P */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNRECOGNIZED_PQP, $this->yytext());
    return new JLexToken(qtype_preg_parser::OPENBRACK, $error);
}




<YYINITIAL> "[^"|"["|"[^]"|"[]" {               // Beginning of a charset: [^ or [ or [^] or []
    $text = $this->yytext();
    $this->charset = new qtype_preg_leaf_charset();
    $this->charset->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription($text)));
    $this->charset->negative = ($text === '[^' || $text === '[^]');
    $this->charset_count = 0;
    $this->charset_set = '';
    if ($text === '[^]' || $text === '[]') {
        $this->add_flag_to_charset(']', qtype_preg_charset_flag::TYPE_SET, ']');
    }
    $this->state_begin_position = $this->current_position_for_node();
    $this->yybegin(self::YYCHARSET);
}
<YYINITIAL> "." {
    $topitem = end($this->opt_stack);
    if ($this->options->preserveallnodes || $topitem->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_DOTALL)) {
        // The true dot matches everything.
        return $this->form_charset($this->yytext(), qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::META_DOT);
    } else {
        // Convert . to [^\n]
        return $this->form_charset('.', qtype_preg_charset_flag::TYPE_SET, "\n", true);
    }
}
<YYINITIAL> "|" {
    // Reset subexpressions numeration inside a (?|...) group.
    $topitem = end($this->opt_stack);
    if ($topitem->dup_subexpr_number != -1) {
        $this->lastsubexpr = $topitem->dup_subexpr_number;
    }
    $alt = new qtype_preg_lexem();
    $alt->set_user_info($this->current_position_for_node(), array(new qtype_preg_userinscription('|')));
    return new JLexToken(qtype_preg_parser::ALT, $alt);
}
<YYINITIAL> "\a"|"\e"|"\f"|"\n"|"\r"|"\t" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(self::code_of_char_escape_sequence($text)));
}
<YYINITIAL> "\c"{ANY} {
    $text = $this->yytext();
    $char = $this->calculate_cx($text);
    if ($char === null) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII, $text);
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, $char);
    }
}
<YYINITIAL> ("\p"|"\P"){ANY} {
    $text = $this->yytext();
    $str = qtype_preg_unicode::substr($text, 2);
    $negative = (qtype_preg_unicode::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype === null) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $str);
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, $subtype, $negative);
    }
}
<YYINITIAL> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $text = $this->yytext();
    $str = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    $negative = (qtype_preg_unicode::substr($text, 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str === 'Any') {
        $res = $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::META_DOT, $negative);
    } else {
        $subtype = $this->get_uprop_flag($str);
        if ($subtype === null) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $str);
            return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
        } else {
            return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, $subtype, $negative);
        }
    }
    return $res;
}
<YYINITIAL> \\0[0-9]?[0-9]? {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0' . decoct($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0' . decoct($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYINITIAL> "\o{"[0-9]+"}" {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0' . decoct($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0' . decoct($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYINITIAL> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0x' . dechex($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0x' . dechex($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYINITIAL> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0x' . dechex($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0x' . dechex($code));
        return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYINITIAL> "\d"|"\D" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_D, $text === '\D');
}
<YYINITIAL> "\h"|"\H" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_H, $text === '\H');
}
<YYINITIAL> "\s"|"\S" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_S, $text === '\S');
}
<YYINITIAL> "\v"|"\V" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_V, $text === '\V');
}
<YYINITIAL> "\w"|"\W" {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_W, $text === '\W');
}
<YYINITIAL> "\C" {
    // TODO: matches any one data unit. For now implemented the same way as dot.
    return $this->form_charset($this->yytext(), qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::META_DOT);
}
<YYINITIAL> "\N" {
    return $this->form_charset($this->yytext(), qtype_preg_charset_flag::TYPE_SET, "\n", true);
}
<YYINITIAL> "\K" {
    // \K resets start of match.
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, '\K is not implemented yet');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "\R" {
    // \R matches new line unicode sequences.
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, '\R is not implemented yet');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "\X" {
    // \X matches  any number of Unicode characters that form an extended Unicode sequence.
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_ERROR, '\X is not implemented yet');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "\b"|"\B" {
    $text = $this->yytext();
    return $this->form_simple_assertion($text, 'qtype_preg_leaf_assert_esc_b', $text === '\B');
}
<YYINITIAL> "\A" {
    return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_esc_a');
}
<YYINITIAL> "\z" {
    return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_small_esc_z');
}
<YYINITIAL> "\Z" {
    return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_capital_esc_z');
}
<YYINITIAL> "\G" {
    return $this->form_simple_assertion($this->yytext(), 'qtype_preg_leaf_assert_esc_g');
}
<YYINITIAL> "^" {
    return $this->assertion_for_circumflex();
}
<YYINITIAL> "$" {
    return $this->assertion_for_dollar();
}
<YYINITIAL> "\c" {
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_C_AT_END_OF_PATTERN, '\c');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> "\u"|"\U"|"\l"|"\L"|"\N{"{ALNUM}*"}" {
    $text = $this->yytext();
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, $text);
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> \\0[0-7]?[0-7]? {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($text, 1))));
}
<YYINITIAL> \\{ANY} {
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::substr($text, 1, 1));
}
<YYINITIAL> \\ {                       /* ERROR: \ at the end of the pattern */
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_SLASH_AT_END_OF_PATTERN, '\\');
    return new JLexToken(qtype_preg_parser::PARSELEAF, $error);
}
<YYINITIAL> {ANY} {                 // Just to avoid exceptions.
    $text = $this->yytext();
    return $this->form_charset($text, qtype_preg_charset_flag::TYPE_SET, $text);
}
<YYCHARSET> "\d"|"\D" {
    $text = $this->yytext();
    $negative = ($text === '\D');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_D, $negative);
}
<YYCHARSET> "\h"|"\H" {
    $text = $this->yytext();
    $negative = ($text === '\H');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_H, $negative);
}
<YYCHARSET> "\s"|"\S" {
    $text = $this->yytext();
    $negative = ($text === '\S');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_S, $negative);
}
<YYCHARSET> "\v"|"\V" {
    $text = $this->yytext();
    $negative = ($text === '\V');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_V, $negative);
}
<YYCHARSET> "\w"|"\W" {
    $text = $this->yytext();
    $negative = ($text === '\W');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::SLASH_W, $negative);
}
<YYCHARSET> "[:alnum:]"|"[:^alnum:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^alnum:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALNUM, $negative);
}
<YYCHARSET> "[:alpha:]"|"[:^alpha:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^alpha:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ALPHA, $negative);
}
<YYCHARSET> "[:ascii:]"|"[:^ascii:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^ascii:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_ASCII, $negative);
}
<YYCHARSET> "[:blank:]"|"[:^blank:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^blank:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_BLANK, $negative);
}
<YYCHARSET> "[:cntrl:]"|"[:^cntrl:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^cntrl:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_CNTRL, $negative);
}
<YYCHARSET> "[:digit:]"|"[:^digit:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^digit:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_DIGIT, $negative);
}
<YYCHARSET> "[:graph:]"|"[:^graph:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^graph:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_GRAPH, $negative);
}
<YYCHARSET> "[:lower:]"|"[:^lower:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^lower:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_LOWER, $negative);
}
<YYCHARSET> "[:print:]"|"[:^print:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^print:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PRINT, $negative);
}
<YYCHARSET> "[:punct:]"|"[:^punct:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^punct:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_PUNCT, $negative);
}
<YYCHARSET> "[:space:]"|"[:^space:]"  {
    $text = $this->yytext();
    $negative = ($text === '[:^space:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_SPACE, $negative);
}
<YYCHARSET> "[:upper:]"|"[:^upper:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^upper:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_UPPER, $negative);
}
<YYCHARSET> "[:word:]"|"[:^word:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^word:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_WORD, $negative);
}
<YYCHARSET> "[:xdigit:]"|"[:^xdigit:]" {
    $text = $this->yytext();
    $negative = ($text === '[:^xdigit:]');
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::POSIX_XDIGIT, $negative);
}
<YYCHARSET> "[:"[^\]]*":]"|"[:^"[^\]]*":]"|"[."[^\]]*".]"|"[="[^\]]*"=]" {
    $text = $this->yytext();
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_POSIX_CLASS, $text, $this->charset);
    $this->charset->userinscription[] = new qtype_preg_userinscription($text);
}
<YYCHARSET> ("\p"|"\P"){ANY} {
    $text = $this->yytext();
    $str = qtype_preg_unicode::substr($text, 2);
    $negative = (qtype_preg_unicode::substr($text, 1, 1) === 'P');
    $subtype = $this->get_uprop_flag($str);
    if ($subtype === null) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $str, $this->charset);
        $this->charset->userinscription[] = new qtype_preg_userinscription($text, $subtype);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, $subtype, $negative);
    }
}
<YYCHARSET> ("\p"|"\P")("{^"|"{")[^}]*"}" {
    $text = $this->yytext();
    $str = qtype_preg_unicode::substr($text, 3, $this->yylength() - 4);
    $negative = (qtype_preg_unicode::substr($text, 1, 1) === 'P');
    $circumflex = (qtype_preg_unicode::substr($str, 0, 1) === '^');
    $negative = ($negative xor $circumflex);
    if ($circumflex) {
        $str = qtype_preg_unicode::substr($str, 1);
    }
    if ($str === 'Any') {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, qtype_preg_charset_flag::META_DOT, $negative);
    } else {
        $subtype = $this->get_uprop_flag($str);
        if ($subtype === null) {
            $error = $this->form_error(qtype_preg_node_error::SUBTYPE_UNKNOWN_UNICODE_PROPERTY, $str, $this->charset);
            $this->charset->userinscription[] = new qtype_preg_userinscription($text, $subtype);
        } else {
            $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_FLAG, $subtype, $negative);
        }
    }
}
<YYCHARSET> \\[0-7][0-7]?[0-7]? {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(octdec(qtype_preg_unicode::substr($text, 1))));
}
<YYCHARSET> "\o{"[0-9]+"}" {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0' . decoct($code), $this->charset);
        $this->charset->userinscription[] = new qtype_preg_userinscription($text);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0' . decoct($code), $this->charset);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYCHARSET> "\x"[0-9a-fA-F]?[0-9a-fA-F]? {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0x' . dechex($code), $this->charset);
        $this->charset->userinscription[] = new qtype_preg_userinscription($text);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0x' . dechex($code), $this->charset);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYCHARSET> "\x{"[0-9a-fA-F]+"}" {
    $text = $this->yytext();
    $code = self::code_of_char_escape_sequence($text);
    if ($code > qtype_preg_unicode::max_possible_code()) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_TOO_BIG, '0x' . dechex($code), $this->charset);
        $this->charset->userinscription[] = new qtype_preg_userinscription($text);
    } else if (0xd800 <= $code && $code <= 0xdfff) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CHAR_CODE_DISALLOWED, '0x' . dechex($code), $this->charset);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8($code));
    }
}
<YYCHARSET> "\a"|"\b"|"\e"|"\f"|"\n"|"\r"|"\t" {
    $text = $this->yytext();
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(self::code_of_char_escape_sequence($text)));
}
<YYCHARSET> "\c"{ANY} {
    $text = $this->yytext();
    $char = $this->calculate_cx($text);
    if ($char === null) {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_CX_SHOULD_BE_ASCII, $text, $this->charset);
        $this->charset->userinscription[] = new qtype_preg_userinscription($text);
    } else {
        $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, $char);
    }
}
<YYCHARSET> "\N" {
    // TODO: matches any character except new line characters. For now, the same as dot.
    $this->add_flag_to_charset($this->yytext(), qtype_preg_charset_flag::TYPE_SET, qtype_preg_unicode::code2utf8(0x0A), true);
}
<YYCHARSET> "\u"|"\U"|"\l"|"\L"|"\N{"{ALNUM}*"}" {
    $text = $this->yytext();
    $error = $this->form_error(qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED, $text, $this->charset);
    $this->charset->userinscription[] = new qtype_preg_userinscription($text);
}
<YYCHARSET> "\E" {
    // Do nothing in YYCHARSET state.
}
<YYCHARSET> "\Q" {                   // \Q...\E beginning
    $this->qe_sequence = '';
    $this->qe_sequence_length = 0;
    //$this->state_begin_position = $this->current_position_for_node();
    $this->skipped_positions[] = $this->current_position_for_node();
    $this->yybegin(self::YYQEIN);
}
<YYQEIN> {ANY} {                     // \Q...\E body
    $text = $this->yytext();
    $this->qe_sequence .= $text;
    $this->qe_sequence_length++;
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, $text);
}
<YYQEIN> "\E" {                      // \Q...\E ending
    $this->qe_sequence = '';
    $this->qe_sequence_length = 0;
    //$this->state_begin_position = $this->current_position_for_node();
    $this->skipped_positions[] = $this->current_position_for_node();
    $this->yybegin(self::YYCHARSET);
}
<YYCHARSET> \\{ANY} {
    $text = $this->yytext();
    $char = qtype_preg_unicode::substr($text, 1, 1);
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, $char, false, $char !== '-');
}
<YYCHARSET> [^\]] {
    $text = $this->yytext();
    $this->add_flag_to_charset($text, qtype_preg_charset_flag::TYPE_SET, $text);
}
<YYCHARSET> "]" {
    // Form the charset.
    $position = new qtype_preg_position($this->state_begin_position->indfirst, $this->yychar + $this->yylength() - 1,
                                        $this->state_begin_position->linefirst, $this->yyline,
                                        $this->state_begin_position->colfirst, $this->yycol + $this->yylength() - 1);
    $this->charset->userinscription[] = new qtype_preg_userinscription(']');

    $this->charset->set_user_info($position, $this->charset->userinscription);
    if ($this->charset_set !== '') {
        $flag = new qtype_preg_charset_flag;
        $flag->set_data(qtype_preg_charset_flag::TYPE_SET, new qtype_poasquestion\string($this->charset_set));
        $this->charset->flags[] = array($flag);
    }

    $this->set_node_modifiers($this->charset);

    // Look for possible errors.
    $ui1 = $this->charset->userinscription[1];
    $ui2 = $this->charset->userinscription[count($this->charset->userinscription) - 2];
    if (count($this->charset->userinscription) > 3 && $ui1->data == ':' && $ui2->data == ':') {
        $error = $this->form_error(qtype_preg_node_error::SUBTYPE_POSIX_CLASS_OUTSIDE_CHARSET, '', $this->charset);
        $error->set_user_info($position);
        $res = new JLexToken(qtype_preg_parser::PARSELEAF, $error);
    } else {
        $res = new JLexToken(qtype_preg_parser::PARSELEAF, $this->charset);
    }

    $this->charset = null;
    $this->charset_count = 0;
    $this->charset_set = '';
    $this->state_begin_position = null;
    $this->yybegin(self::YYINITIAL);

    return $res;
}
