<?php
// This file is part of Preg question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Preg question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines abstract class of regular expression handler, which is basically anything that works with regexes.
 * By inheriting the handler you can benefit automatic regex parsing, error handling etc.
 *
 * @package    qtype_preg
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/poasquestion/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/poasquestion/questiontype.php');
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');

/**
 * Options, generic to all handlers - mainly affects scanning and parsing.
 */
class qtype_preg_handling_options {
    const MODE_PCRE = 0;
    const MODE_POSIX = 1;

    const MODIFIER_CASELESS           = 0x00000001;  // i // case insensitive match.
    const MODIFIER_MULTILINE          = 0x00000002;  // m // multiple lines match.
    const MODIFIER_DOTALL             = 0x00000004;  // s // dot matches newlines.
    const MODIFIER_EXTENDED           = 0x00000008;  // x // ignore white spaces.
    const MODIFIER_ANCHORED           = 0x00000010;  // A // the pattern is forced to be anchored.
    const MODIFIER_DOLLAR_ENDONLY     = 0x00000020;  // D // dollar metacharacter matches only at the end of the subject string.
    const MODIFIER_EXTRA              = 0x00000040;  // X //
    const MODIFIER_NOTBOL             = 0x00000080;
    const MODIFIER_NOTEOL             = 0x00000100;
    const MODIFIER_UNGREEDY           = 0x00000200;  // U // inverts the greediness of the quantifiers.
    //const MODIFIER_NOTEMPTY           = 0x00000400;
    const MODIFIER_UTF8               = 0x00000800;  // u // regard both the pattern and the subject as UTF-8 strings.
    //const MODIFIER_UTF16              = 0x00000800;
    //const MODIFIER_UTF32              = 0x00000800;
    //const MODIFIER_NO_AUTO_CAPTURE    = 0x00001000;
    //const MODIFIER_NO_UTF8_CHECK      = 0x00002000;
    //const MODIFIER_NO_UTF16_CHECK     = 0x00002000;
    //const MODIFIER_NO_UTF32_CHECK     = 0x00002000;
    //const MODIFIER_AUTO_CALLOUT       = 0x00004000;
    //const MODIFIER_PARTIAL_SOFT       = 0x00008000;
    //const MODIFIER_PARTIAL            = 0x00008000;

    //const MODIFIER_NEVER_UTF          = 0x00010000;
    //const MODIFIER_DFA_SHORTEST       = 0x00010000;

    //const MODIFIER_NO_AUTO_POSSESS    = 0x00020000;
    //const MODIFIER_DFA_RESTART        = 0x00020000;

    //const MODIFIER_FIRSTLINE          = 0x00040000;
    const MODIFIER_DUPNAMES           = 0x00080000;  // J // names used to identify capturing subpatterns need not be unique.
    const MODIFIER_NEWLINE_CR         = 0x00100000;  //   // newline is indicated by CR.
    const MODIFIER_NEWLINE_LF         = 0x00200000;  //   // newline is indicated by LF.
    const MODIFIER_NEWLINE_CRLF       = 0x00300000;  //   // newline is indicated by CRLF.
    const MODIFIER_NEWLINE_ANY        = 0x00400000;  //   // newline is indicated by any Unicode newline sequence.
    const MODIFIER_NEWLINE_ANYCRLF    = 0x00500000;  //   // newline is indicated by CR, LF or CRLF.
    const MODIFIER_BSR_ANYCRLF        = 0x00800000;  //   // \R matches CR, LF, or CRLF.
    const MODIFIER_BSR_UNICODE        = 0x01000000;  //   // \R matches any Unicode newline sequence.
    //const MODIFIER_JAVASCRIPT_COMPAT  = 0x02000000;
    //const MODIFIER_NO_START_OPTIMIZE  = 0x04000000;
    //const MODIFIER_NO_START_OPTIMISE  = 0x04000000;
    //const MODIFIER_PARTIAL_HARD       = 0x08000000;
    //const MODIFIER_NOTEMPTY_ATSTART   = 0x10000000;
    //const MODIFIER_UCP                = 0x20000000;

    /** @var boolean Regex compatibility mode. */
    public $mode = self::MODE_PCRE;
    /** @var integer bitwise union of constants MODIFIER_XXX. */
    public $modifiers = 0;
    /** @var boolean Strict PCRE compatible regex syntax. */
    public $pcrestrict = false;
    /** @var boolean Should regex match the entire string (true) or any part of it (false). */
    public $exactmatch = false;
    /** @var boolean Should lexer and parser try hard to preserve all nodes, including grouping and option nodes. */
    public $preserveallnodes = false;
    /** @var boolean Should lexer and parser recognize templates in comments like (?###word). */
    public $parsetemplates = true;
    /** @var boolean Should parser expand nodes x{m,n} to sequences like xxxx?x?x?x?. */
    public $expandquantifiers = false;
    /** @var boolean Should parser replace non-recursive subexpr calls (?1) with the subexpr node clone. */
    public $replacesubexprcalls = false;
    /** @var boolean Are we running in debug mode? If so, engines can print debug information during matching. */
    public $debugmode = false;
    /** @var string Notation, in which regex was passed*/
    public $notation = 'native';
    /** @var object Regex text selection borders, an instance of qtype_preg_position. (-2, -2) means no selection. */
    public $selection = null;

    public static function get_all_modifiers() {
        return array(self::MODIFIER_CASELESS,
                     self::MODIFIER_MULTILINE,
                     self::MODIFIER_DOTALL,
                     self::MODIFIER_EXTENDED,
                     self::MODIFIER_ANCHORED,
                     self::MODIFIER_DOLLAR_ENDONLY,
                     self::MODIFIER_EXTRA,
                     self::MODIFIER_NOTBOL,
                     self::MODIFIER_NOTEOL,
                     self::MODIFIER_UNGREEDY,
                     //self::MODIFIER_NOTEMPTY,
                     self::MODIFIER_UTF8,
                     //self::MODIFIER_UTF16,
                     //self::MODIFIER_UTF32,
                     //self::MODIFIER_NO_AUTO_CAPTURE,
                     //self::MODIFIER_NO_UTF8_CHECK,
                     //self::MODIFIER_NO_UTF16_CHECK,
                     //self::MODIFIER_NO_UTF32_CHECK,
                     //self::MODIFIER_AUTO_CALLOUT,
                     //self::MODIFIER_PARTIAL_SOFT,
                     //self::MODIFIER_PARTIAL,

                     //self::MODIFIER_NEVER_UTF,
                     //self::MODIFIER_DFA_SHORTEST,

                     //self::MODIFIER_NO_AUTO_POSSESS,
                     //self::MODIFIER_DFA_RESTART,

                     //self::MODIFIER_FIRSTLINE,
                     self::MODIFIER_DUPNAMES,
                     self::MODIFIER_NEWLINE_CR,
                     self::MODIFIER_NEWLINE_LF,
                     self::MODIFIER_NEWLINE_CRLF,
                     self::MODIFIER_NEWLINE_ANY,
                     self::MODIFIER_NEWLINE_ANYCRLF,
                     self::MODIFIER_BSR_ANYCRLF,
                     self::MODIFIER_BSR_UNICODE,
                     //self::MODIFIER_JAVASCRIPT_COMPAT,
                     //self::MODIFIER_NO_START_OPTIMIZE,
                     //self::MODIFIER_NO_START_OPTIMISE,
                     //self::MODIFIER_PARTIAL_HARD,
                     //self::MODIFIER_NOTEMPTY_ATSTART,
                     //self::MODIFIER_UCP
                     );
    }

    public static function char_to_modifier($char) {
        switch ($char) {
        case 'i':
            return self::MODIFIER_CASELESS;
        case 'm':
            return self::MODIFIER_MULTILINE;
        case 's':
            return self::MODIFIER_DOTALL;
        case 'x':
            return self::MODIFIER_EXTENDED;
        case 'A':
            return self::MODIFIER_ANCHORED;
        case 'D':
            return self::MODIFIER_DOLLAR_ENDONLY;
        case 'X':
            return self::MODIFIER_EXTRA;
        case 'U':
            return self::MODIFIER_UNGREEDY;
        case 'u':
            return self::MODIFIER_UTF8;
        case 'J':
            return self::MODIFIER_DUPNAMES;
        default:
            return 0;
        }
    }

    public static function modifier_to_char($mod) {
        switch ($mod) {
        case self::MODIFIER_CASELESS:
            return 'i';
        case self::MODIFIER_MULTILINE:
            return 'm';
        case self::MODIFIER_DOTALL:
            return 's';
        case self::MODIFIER_EXTENDED:
            return 'x';
        case self::MODIFIER_ANCHORED:
            return 'A';
        case self::MODIFIER_DOLLAR_ENDONLY:
            return 'D';
        case self::MODIFIER_EXTRA:
            return 'X';
        case self::MODIFIER_UNGREEDY:
            return 'U';
        case self::MODIFIER_UTF8:
            return 'u';
        case self::MODIFIER_DUPNAMES:
            return 'J';
        default:
            return '';
        }
    }

    public static function string_to_modifiers($str) {
        $result = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            $result = $result | self::char_to_modifier($str[$i]);
        }
        return $result;
    }

    public function modifiers_to_string() {
        $result = '';
        foreach (self::get_all_modifiers() as $mod) {
            if ($this->is_modifier_set($mod)) {
                $result .= self::modifier_to_char($mod);
            }
        }
        return $result;
    }

    public function set_modifier($modifier) {
        $this->modifiers = ($this->modifiers | $modifier);
    }

    public function unset_modifier($modifier) {
        $modifier = ~$modifier;
        $this->modifiers = ($this->modifiers & $modifier);
    }

    public function is_modifier_set($modifier) {
        return ($this->modifiers & $modifier) != 0;
    }
}

class qtype_preg_regex_handler {

    /** Regular expression as an object of qtype_poasquestion\string. */
    protected $regex;
    /** Regular expression handling options, may be different for different handlers. */
    protected $options;
    /** Node selected by the user. */
    protected $selectednode = null;
    /** Regex lexer. */
    protected $lexer = null;
    /** Regex parser. */
    protected $parser = null;
    /** The root of the regex abstract syntax tree, consists of qtype_preg_node childs. */
    protected $astroot = null;
    /** The root of the regex definite syntax tree, consists of xxx_preg_node childs where xxx is engine name. */
    protected $dstroot = null;
    /** Array of error nodes generated by lexer or parser. */
    protected $errornodes = array();
    /** The error objects array. */
    protected $errors = array();

    /**
     * Parses the regex and does all necessary preprocessing.
     * @param string regex - regular expression to handle.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $options = null) {
        // Options should exist at least as a default object.
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }
        if ($options->selection === null) {
            $options->selection = new qtype_preg_position(-2, -2);
        }

        if ($regex == '' || $regex === null) {
            $this->regex = new qtype_poasquestion\string('');
            $this->options = $options;
            return;
        }

        // Convert to actually used notation if necessary.
        $usednotation = $this->used_notation();
        if ($options->notation != $usednotation) {
            $notationclass = 'qtype_preg_notation_' . $options->notation;
            $notationobj = new $notationclass($regex, $options);
            $regex = $notationobj->convert_regex($usednotation);
            $options = $notationobj->convert_options($usednotation);
        }

        $this->regex = new qtype_poasquestion\string($regex);
        $this->options = $options;

        // Do parsing.
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
        }

        // Sometimes engine that use accept_regex still need parsing to count subexpressions.
        // In case with no parsing we should stick to accepting whole regex, not nodes.
        $this->accept_regex();
    }

    /**
     * Returns class name without 'qtype_preg_' prefix.
     * Should be overloaded in child classes.
     */
    public function name() {
        return 'regex_handler';
    }

    /**
     * Returns notation, actually used by handler.
     */
    public function used_notation() {
        return 'native';
    }

    /**
     * Returns supported modifiers as bitwise union of constants MODIFIER_XXX.
     */
    public function get_supported_modifiers() {
        return qtype_preg_handling_options::MODIFIER_ANCHORED |
               qtype_preg_handling_options::MODIFIER_CASELESS |         // Any qtype_preg_matcher should support case insensitivity.
               qtype_preg_handling_options::MODIFIER_DOLLAR_ENDONLY |
               qtype_preg_handling_options::MODIFIER_DOTALL |
               //qtype_preg_handling_options::MODIFIER_DUPNAMES |
               qtype_preg_handling_options::MODIFIER_EXTENDED |
               qtype_preg_handling_options::MODIFIER_MULTILINE |
               qtype_preg_handling_options::MODIFIER_UTF8;
    }

    /**
     * Sets regex options.
     * @param options an object containing options to handle the regex.
     */
    public function set_options($options) {
        $this->options = $options;
    }

    public function get_options() {
        return $this->options;
    }

    public function get_selected_node() {
        return $this->selectednode;
    }

    /**
     * Was there an error in regex?
     * @return bool errors exists.
     */
    public function errors_exist() {
        return count($this->get_errors()) > 0;
    }

    public function get_error_nodes() {
       return $this->errornodes;
    }

    /**
     * Returns errors as objects.
     * @return array of errors.
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Returns error messages for regex.
     * @param string $limit configuration field name for the maximum number of shown messages.
     * @return array of error messages.
     */
    public function get_error_messages($limit = 'qtype_preg_maxerrorsshown') {
        global $CFG;
        $res = array();
        $maxerrors = 5;
        if ($limit) {
            // Determine maximum number of errors to show.
            if (isset($CFG->$limit)) {
                $maxerrors = $CFG->$limit;
            }
        }
        $i = 0;
        foreach ($this->get_errors() as $error) {
            if (!$limit || $i < $maxerrors) {
                $res[] = $error->errormsg;
            }
            $i++;
        }
        if ($limit && $i > $maxerrors) {
            $res[] = get_string('toomanyerrors', 'qtype_preg' , $i - $maxerrors);
        }
        return $res;
    }

    /**
     * Access function to the AST root.
     * Used mainly for unit-testing and avoiding re-parsing.
     */
    public function get_ast_root() {
        return $this->astroot;
    }

    /**
     * Access function to the DST root.
     */
    public function get_dst_root() {
        return $this->dstroot;
    }

    /**
     * Returns max subexpression number.
     */
    public function get_max_subexpr() {
        if ($this->lexer !== null) {
            return $this->lexer->get_max_subexpr();
        }
        return 0;
    }

    /**
     * Returns max subpattern number.
     */
    public function get_max_subpatt() {
        if ($this->parser !== null) {
            return $this->parser->get_max_subpatt();
        }
        return 0;
    }

    /**
     * Returns subexpressions name => number map.
     */
    public function get_subexpr_name_to_number_map() {
        if ($this->lexer !== null) {
            return $this->lexer->get_subexpr_name_to_number_map();
        }
        return array();
    }

    public function get_subexpr_number_to_nodes_map() {
        if ($this->parser !== null) {
            return $this->parser->get_subexpr_number_to_nodes_map();
        }
        return array();
    }

    /**
     * Returns subpatterns number => node map.
     */
    public function get_subpatt_number_to_node_map() {
        $result = array();
        if ($this->parser !== null) {
            $result = $this->parser->get_subpatt_number_to_node_map();
        }
        if ($this->selectednode !== null) {
            $result[-2] = $this->selectednode;
        }
        return $result;
    }

    /**
     * Returns all nodes with references to subexpressions (backreferences, conditional subexpressions, subexpression calls).
     */
    public function get_nodes_with_subexpr_refs() {
        if ($this->lexer !== null) {
            return $this->lexer->get_nodes_with_subexpr_refs();
        }
        return array();
    }

    public function get_subexpr_refs_map() {
        if ($this->lexer !== null) {
            return $this->lexer->get_subexpr_refs_map();
        }
        return array();
    }

    public function is_node_generated($pregnode) {
        if (!$this->options->exactmatch) {
            return false;
        }
        if ($pregnode->id == $this->astroot->id) {
            return true;
        }
        if (is_a($this->astroot, 'qtype_preg_operator')) {
            foreach ($this->astroot->operands as $operand) {
                if ($pregnode->id == $operand->id) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Definite syntax tree (DST) node factory creates node objects for given engine from abstract syntax tree.
     * @param pregnode qtype_preg_node child class instance.
     * @return corresponding xxx_preg_node child class instance.
     */
    public function from_preg_node($pregnode) {
        if (!is_a($pregnode, 'qtype_preg_node')) {
            return $pregnode;   // The node is already converted.
        }

        $enginenodename = $this->get_engine_node_name($pregnode->type, $pregnode->subtype);
        if (class_exists($enginenodename)) {
            $enginenode = new $enginenodename($pregnode, $this);
            try {
                $acceptresult = $enginenode->accept($this->options);

            } catch (qtype_preg_mergedassertion_option_exception $e) {
                $this->errors[$enginenodename] = new qtype_preg_mergedassertion_option_error($this->regex);
                return;
            }
            if ($acceptresult !== true && !isset($this->errors[$enginenodename])) {
                // Highlight first occurence of the unaccepted node.
                $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, $pregnode);
            }
        } else {
            $enginenode = $pregnode;
            $acceptresult = $this->is_preg_node_acceptable($pregnode);
            if ($acceptresult !== true && !isset($this->errors[$enginenodename])) {
                // Highlight first occurence of the unaccepted node.
                $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, $pregnode);
            }
        }
        return $enginenode;
    }

    /**
     * Returns path to the temporary directory for the given component.
     * @param componentname name of the component calling this function.
     * @return absolute path to the temporary directory for the given component.
     */
    public static function get_temp_dir($componentname) {
        global $CFG;
        $dir = $CFG->dataroot . '/temp/preg/' . $componentname . '/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * Runs dot of graphviz on the given dot script.
     * @param dotscript a string containing the dot script.
     * @param type type of the resulting image, should be 'svg', png' or something.
     * @param filename the absolute path to the resulting image file.
     * @return binary representation of the image if filename is null.
     */
    public static function execute_dot($dotscript, $type, $filename = null) {
        global $CFG;

        if (empty($CFG->pathtodot)) {
            throw new qtype_preg_pathtodot_empty('');
        }

        $cmd = escapeshellarg($CFG->pathtodot) . ' -T' . $type;
        if ($filename !== null) {
            $cmd .= ' -o' . escapeshellarg($filename);
        }
        $descriptorspec = array(0 => array('pipe', 'r'),  // Stdin is a pipe that the child will read from.
                                1 => array('pipe', 'w'),  // Stdout is a pipe that the child will write to.
                                2 => array('pipe', 'w')); // Stderr is a pipe that the child will write to.

        $process = proc_open($cmd, $descriptorspec, $pipes);
        $output = '';
        if (is_resource($process)) {
            fwrite($pipes[0], $dotscript);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);

            if (!empty($err)) {
                if (!qtype_poasquestion::is_dot_available()) {
                    throw new qtype_preg_pathtodot_incorrect('', $CFG->pathtodot);
                } else {
                    throw new qtype_preg_dot_error('');
                }
            }
        }
        return $output;
    }

    /**
     * Returns the infix for DST node names which are named like 'qtype_preg_' . $infix . '_' . $pregnodename.
     * Should be overloaded in child classes.
     */
    protected function node_infix() {
        return '';
    }

    /**
     * Returns the engine-specific node name for the given preg_node name.
     * Overload in case of sophisticated node name schemes.
     */
    protected function get_engine_node_name($nodetype, $nodesubtype) {
        return 'qtype_preg_' . $this->node_infix() . '_' . $nodetype;
    }

    protected function accept_regex() {
        return true; // Accept anything by default.
    }

    /**
     * Is this engine need a parsing of regular expression?
     * @return bool if parsing needed.
     */
    protected function is_parsing_needed() {
        return true;    // Most engines will need parsing.
    }

    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing.
     * what properties of node isn't supported.
     * Should be overloaded in child classes.
     */
    protected function is_preg_node_acceptable($pregnode) {
        // Do not show accepting errors for error nodes.
        if ($pregnode->type === qtype_preg_node::TYPE_NODE_ERROR) {
            return true;
        }
        return false;
    }

    protected function find_errors($regex, &$lexer, &$parser) {
        // All errors are found with preserveallnodes == true.
        $options = clone $this->options;
        $options->preserveallnodes = true;

        // Tokenize regex.
        $hastemplates = false;
        $tokens = qtype_preg_lexer::tokenize_regex($regex, $options, $lexer, $hastemplates);

        // Parse tokens.
        $parser = new qtype_preg_parser($options);
        foreach ($tokens as $token) {
            $parser->doParse($token->type, $token->value);
        }

        // Parse specific lexer errors.
        $lexerrors = $lexer->get_error_nodes();
        foreach ($lexerrors as $node) {
            if ($node->subtype == qtype_preg_node_error::SUBTYPE_UNCLOSED_CHARSET || $node->subtype == qtype_preg_node_error::SUBTYPE_MISSING_COMMENT_ENDING) {
                $parser->doParse(qtype_preg_parser::PARSELEAF, $node);
            }
            $this->errornodes[] = $node;
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }

        // Parsing is finished.
        $parser->doParse(0, 0);

        // Parser contains other errors inside AST nodes.
        $parseerrors = $parser->get_error_nodes();
        foreach ($parseerrors as $node) {
            $this->errornodes[] = $node;
            // There can be a specific accepting error.
            if ($node->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED) {
                $inscription = $node->addinfo;
                $this->errors[] = new qtype_preg_accepting_error($regex, $this->name(), $inscription, $node);
            } else {
                $this->errors[] = new qtype_preg_parsing_error($regex, $node);
            }
        }
    }

    /**
     * Does lexical and syntaxical analysis of the regex and builds an abstract syntax tree, saving root node in $this->astroot.
     * @param string regex - regular expression for building tree.
     */
    protected function build_tree($regex) {
        // Find errors first. If there is one, save the AST as is and exit.
        $this->find_errors($regex, $this->lexer, $this->parser);
        $this->astroot = $this->parser->get_root();

        if (!$this->errors_exist()) {
            // No errors in the regex. Parse again if needed (preserveallnodes == false)
            if (!$this->options->preserveallnodes) {
                // Use templates preprocessor.
                $resultregex = '';
                $tokens = qtype_preg\template::process_regex($regex, $this->options, $this->lexer, $resultregex);

                // Pass tokens to the parser.
                $this->parser = new qtype_preg_parser($this->options);
                foreach ($tokens as $token) {
                    $this->parser->doParse($token->type, $token->value);
                }

                // Parsing is finished.
                $this->parser->doParse(0, 0);

                $this->regex = new qtype_poasquestion\string($resultregex);
                $this->astroot = $this->parser->get_root();

                // Look for unsupported modifiers.
                $supportedmodifiers = $this->get_supported_modifiers();
                foreach ($this->lexer->get_all_modifiers() as $mod) {
                    $supported = $supportedmodifiers & $mod;
                    if (!$supported) {
                        $this->errors[] = new qtype_preg_modifier_error($this->name(), qtype_preg_handling_options::modifier_to_char($mod));
                    }
                }
            }

            // Add necessary nodes.
            if ($this->astroot !== null && $this->options->exactmatch) {
                $newroot = $this->add_exact_match_nodes($this->astroot);
                $this->astroot->subpattern = -1;
                $this->astroot = $newroot;
                $this->astroot->subpattern = 0;
            }
            if ($this->astroot !== null && $this->options->selection->indfirst != -2) {
                $newroot = $this->add_selection_nodes($this->astroot);
                $this->astroot->subpattern = -1;
                $this->astroot = $newroot;
                $this->astroot->subpattern = 0;
            }
        }

        if ($this->astroot !== null) {
            $this->dstroot = $this->from_preg_node(clone $this->astroot);
        }
    }

    /**
     * Adds necessary preg nodes for selection.
     */
    protected function add_selection_nodes($oldroot) {
        // Find and expand a node by selection.
        $indfirst = $this->options->selection->indfirst;
        $indlast = $this->options->selection->indlast;
        foreach ($this->lexer->get_skipped_positions() as $skipped) {
            if ($indfirst - $indlast == 1) {
                break;  // Fictive leaf.
            }
            if ($indfirst >= $skipped->indfirst && $indfirst <= $skipped->indlast) {
                $indfirst = $skipped->indlast + 1;
            }
            if ($indlast >= $skipped->indfirst && $indlast <= $skipped->indlast) {
                $indlast = $skipped->indfirst - 1;
            }
            if ($indlast < $indfirst) {
                $indfirst = -2;
                $indlast = -2;
                break;
            }
        }
        if ($indfirst != -2) {
            $idcounter = $this->parser->get_max_id();
            $this->selectednode = $oldroot->node_by_regex_fragment($indfirst, $indlast, $idcounter);
            $this->parser->set_max_id($idcounter);
        }
        return $oldroot;    // Handler doesn't add any nodes for selection by default.
    }

    /**
     * Adds necessary preg nodes for exact matching.
     */
    protected function add_exact_match_nodes($oldroot) {
        $idcounter = $this->parser->get_max_id();
        $position = new qtype_preg_position(min($oldroot->position->indfirst, 0),
                                            max($oldroot->position->indlast, $this->regex->length() - 1));
        $newroot = new qtype_preg_node_concat();
        $newroot->id = ++$idcounter;
        $newroot->set_user_info($position->add_chars_left(-4)->add_chars_right(2), new qtype_preg_userinscription(''));
        $newroot->operands[0] = new qtype_preg_leaf_assert_circumflex();
        $newroot->operands[0]->id = ++$idcounter;
        $newroot->operands[0]->set_user_info(new qtype_preg_position($newroot->position->indfirst, $newroot->position->indfirst), array(new qtype_preg_userinscription('^')));
        $newroot->operands[1] = new qtype_preg_node_subexpr(qtype_preg_node_subexpr::SUBTYPE_GROUPING);
        $newroot->operands[1]->id = ++$idcounter;
        $newroot->operands[1]->set_user_info($position->add_chars_left(-3)->add_chars_right(1), array(new qtype_preg_userinscription('(?:...)')));
        $newroot->operands[1]->operands[0] = $oldroot;
        $newroot->operands[2] = new qtype_preg_leaf_assert_dollar();
        $newroot->operands[2]->id = ++$idcounter;
        $newroot->operands[2]->set_user_info(new qtype_preg_position($newroot->position->indlast, $newroot->position->indlast), array(new qtype_preg_userinscription('$')));
        $this->parser->set_max_id($idcounter);
        return $newroot;
    }

    protected function find_parent_node($root, $node) {
        if ($root === $node) {
            return null;
        }
        $cur = array($root);
        while (count($cur) > 0) {
            $tmp = array_pop($cur);
            if (is_a($tmp, 'qtype_preg_leaf')) {
                continue;
            }
            foreach ($tmp->operands as $operand) {
                if ($operand === $node) {
                    return $tmp;
                }
                $cur[] = $operand;
            }
        }
        return null;
    }

    /**
     * Checks that the two nodes are equivalent
     * @param $handler qtype_preg_regex_handler other node
     * @return bool is $handler fully equals to this handler
     */
    public function is_equal($handler) {
        $thisroot = $this->get_ast_root();
        $otherroot = $handler->get_ast_root();
        return $thisroot->is_equal($otherroot, 0);
    }

    /**
     * Finds all occurrences of $node subtree in the current tree
     * @param $handler qtype_preg_regex_handler subtree to find
     * @return array array of roots of founded subtrees
     */
    public function find_all_subtrees($handler) {
        $thisroot = $this->get_ast_root();
        $otherroot = $handler->get_ast_root();
        return $thisroot->find_all_subtrees($otherroot, 0);
    }
}
