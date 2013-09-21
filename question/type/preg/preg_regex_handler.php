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
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');
require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');

/**
 * Options, generic to all handlers - mainly affects scanning and parsing.
 */
class qtype_preg_handling_options {
    const MODE_PCRE = 0;
    const MODE_POSIX = 1;

    const MODIFIER_ANCHORED            = 0x000001;  // A // the pattern is forced to be anchored.
    // const MODIFIER_AUTO_CALLOUT      = 0x000002;
    const MODIFIER_BSR_ANYCRLF         = 0x000004;  //   // \R matches CR, LF, or CRLF.
    const MODIFIER_BSR_UNICODE         = 0x000008;  //   // \R matches any Unicode newline sequence.
    const MODIFIER_CASELESS            = 0x000010;  // i // case insensitive match.
    const MODIFIER_DOLLAR_ENDONLY      = 0x000020;  // D // dollar metacharacter matches only at the end of the subject string.
    const MODIFIER_DOTALL              = 0x000040;  // s // dot matches newlines.
    const MODIFIER_DUPNAMES            = 0x000080;  // J // names used to identify capturing subpatterns need not be unique.
    const MODIFIER_EXTENDED            = 0x000100;  // x // ignore white spaces.
    // const MODIFIER_EXTRA             = 0x000200;  // X //
    // const MODIFIER_FIRSTLINE         = 0x000400;
    // const MODIFIER_JAVASCRIPT_COMPAT = 0x000800;
    const MODIFIER_MULTILINE           = 0x001000;  // m // multiple lines match.
    const MODIFIER_NEWLINE_CR          = 0x002000;  //   // newline is indicated by CR.
    const MODIFIER_NEWLINE_LF          = 0x004000;  //   // newline is indicated by LF.
    const MODIFIER_NEWLINE_CRLF        = 0x008000;  //   // newline is indicated by CRLF.
    const MODIFIER_NEWLINE_ANYCRLF     = 0x010000;  //   // newline is indicated by CR, LF or CRLF.
    const MODIFIER_NEWLINE_ANY         = 0x020000;  //   // newline is indicated by any Unicode newline sequence.
    // const MODIFIER_NO_AUTO_CAPTURE   = 0x040000;
    // const MODIFIER_NO_START_OPTIMIZE = 0x080000;
    // const MODIFIER_UCP               = 0x100000;
    const MODIFIER_UNGREEDY            = 0x200000;  // U // inverts the greediness of the quantifiers.
    const MODIFIER_UTF8                = 0x400000;  // u // regard both the pattern and the subject as UTF-8 strings.
    // const MODIFIER_NO_UTF8_CHECK     = 0x800000;

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
    /** @var boolean Should parser expand nodes x{m,n} to sequences like xxxx?x?x?x?. */
    public $expandquantifiers = false;
    /** @var boolean Are we running in debug mode? If so, engines can print debug information during matching. */
    public $debugmode = false;
    /** @var string Notation, in which regex was passed*/
    public $notation = 'native';
    /** @var object Regex text selection borders, an instance of qtype_preg_position. (-2, -2) means no selection. */
    public $selection = null;

    public static function get_all_modifiers() {
        return array(self::MODIFIER_ANCHORED,
                     // self::MODIFIER_AUTO_CALLOUT,
                     self::MODIFIER_BSR_ANYCRLF,
                     self::MODIFIER_BSR_UNICODE,
                     self::MODIFIER_CASELESS,
                     self::MODIFIER_DOLLAR_ENDONLY,
                     self::MODIFIER_DOTALL,
                     self::MODIFIER_DUPNAMES,
                     self::MODIFIER_EXTENDED,
                     // self::MODIFIER_EXTRA,
                     // self::MODIFIER_FIRSTLINE,
                     // self::MODIFIER_JAVASCRIPT_COMPAT,
                     self::MODIFIER_MULTILINE,
                     self::MODIFIER_NEWLINE_CR,
                     self::MODIFIER_NEWLINE_LF,
                     self::MODIFIER_NEWLINE_CRLF,
                     self::MODIFIER_NEWLINE_ANYCRLF,
                     self::MODIFIER_NEWLINE_ANY,
                     // self::MODIFIER_NO_AUTO_CAPTURE,
                     // self::MODIFIER_NO_START_OPTIMIZE,
                     // self::MODIFIER_UCP,
                     self::MODIFIER_UNGREEDY,
                     self::MODIFIER_UTF8,
                     // self::MODIFIER_NO_UTF8_CHECK
                     );
    }

    public static function char_to_modifier($char) {
        switch ($char) {
        case 'A':
            return self::MODIFIER_ANCHORED;
        case 'i':
            return self::MODIFIER_CASELESS;
        case 'D':
            return self::MODIFIER_DOLLAR_ENDONLY;
        case 's':
            return self::MODIFIER_DOTALL;
        case 'J':
            return self::MODIFIER_DUPNAMES;
        case 'x':
            return self::MODIFIER_EXTENDED;
        //case 'X':
        //    return self::MODIFIER_EXTRA;
        case 'm':
            return self::MODIFIER_MULTILINE;
        case 'U':
            return self::MODIFIER_UNGREEDY;
        case 'u':
            return self::MODIFIER_UTF8;
        default:
            return 0;
        }
    }

    public static function modifier_to_char($mod) {
        switch ($mod) {
        case self::MODIFIER_ANCHORED:
            return 'A';
        // case self::MODIFIER_AUTO_CALLOUT:
        case self::MODIFIER_BSR_ANYCRLF:
        case self::MODIFIER_BSR_UNICODE:
            return '';
        case self::MODIFIER_CASELESS:
            return 'i';
        case self::MODIFIER_DOLLAR_ENDONLY:
            return 'D';
        case self::MODIFIER_DOTALL:
            return 's';
        case self::MODIFIER_DUPNAMES:
            return 'J';
        case self::MODIFIER_EXTENDED:
            return 'x';
        // case self::MODIFIER_EXTRA:
        //    return 'X';
        // case self::MODIFIER_FIRSTLINE:
        // case self::MODIFIER_JAVASCRIPT_COMPAT:
        case self::MODIFIER_MULTILINE:
            return 'm';
        case self::MODIFIER_NEWLINE_CR:
        case self::MODIFIER_NEWLINE_LF:
        case self::MODIFIER_NEWLINE_CRLF:
        case self::MODIFIER_NEWLINE_ANYCRLF:
        case self::MODIFIER_NEWLINE_ANY:
        // case self::MODIFIER_NO_AUTO_CAPTURE:
        // case self::MODIFIER_NO_START_OPTIMIZE:
        // case self::MODIFIER_UCP:
        case self::MODIFIER_UNGREEDY:
            return 'U';
        case self::MODIFIER_UTF8:
            return 'u';
        // case self::MODIFIER_NO_UTF8_CHECK:
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
            if ($this->modifiers & $mod) {
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
        return ($this->modifiers & $modifier) == 0 ? false : true;
    }
}

class qtype_preg_regex_handler {

    /** Regular expression as an object of qtype_poasquestion_string. */
    protected $regex;
    /** Regular expression handling options, may be different for different handlers. */
    protected $options;
    /** Node selected by the user. */
    protected $selectednode = null;
    /** Regex lexer. */
    protected $lexer = null;
    /** Regex parser. */
    protected $parser = null;

    /** @var int Number of characters, added at the start of regular expression by internal code.*/
    protected $addedatstart = 0;
    /** @var int Number of characters, added at the end of regular expression by internal code.*/
    protected $addedatend = 0;

    /** The root of the regex abstract syntax tree, consists of qtype_preg_node childs. */
    protected $ast_root = null;
    /** The root of the regex definite syntax tree, consists of xxx_preg_node childs where xxx is engine name. */
    protected $dst_root = null;
    /** The error objects array. */
    protected $errors = array();

    /**
     * Parses the regex and does all necessary preprocessing.
     * @param string regex - regular expression to handle.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $options = null) {
        if ($regex == '' || $regex === null) {
            return;
        }

        // Options should exist at least as a default object.
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }

        // Convert to actually used notation if necessary.
        $usednotation = $this->used_notation();
        if ($options->notation != $usednotation) {
            $notationclass = 'qtype_preg_notation_' . $options->notation;
            $notationobj = new $notationclass($regex, $options);
            $regex = $notationobj->convert_regex($usednotation);
            $options = $notationobj->convert_options($usednotation);
        }

        // Deal with the selection.
        if ($options->selection === null) {
            $options->selection = new qtype_preg_position(-2, -2);
        }

        if ($options->exactmatch) {
            // Grouping is needed in case regexp contains top-level alternations.
            // Use non-capturing grouping to not mess-up with user subexpression capturing.
            // Add line break before last bracket since regex may end in the comment in extended notation.
            // Line breaks will be ignored in other notations, so it's ok to add it anyway.
            $regex = '^(?:'.$regex."\n)$";
            $this->addedatstart = 4;
            $this->addedatend = 3;
        }

        // Look for unsupported modifiers.
        $allmodifiers = qtype_preg_handling_options::get_all_modifiers();
        $supportedmodifiers = $this->get_supported_modifiers();
        foreach ($allmodifiers as $mod) {
            $passed = $options->is_modifier_set($mod);
            $supported = $supportedmodifiers & $mod;
            if ($passed && !$supported) {
                $this->errors[] = new qtype_preg_modifier_error($this->name(), $mod);
            }
        }

        $this->regex = new qtype_poasquestion_string($regex);
        $this->options = $options;

        // Do parsing.
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
        }

        // Find and expand a node by selection.
        if ($this->options->selection->indfirst != -2) {
            $indfirst = $this->options->selection->indfirst;
            $indlast = $this->options->selection->indlast;
            $idcounter = $this->parser->get_max_id() + 1;
            $this->selectednode = $this->ast_root->node_by_regex_fragment($indfirst, $indlast, $idcounter);
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
               qtype_preg_handling_options::MODIFIER_DUPNAMES |
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
     * @return bool  errors exists.
     */
    public function errors_exist() {
        return count($this->get_errors()) > 0;
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
     * @param limit bool limit error messages to the admin option
     * @return array of error messages.
     */
    public function get_error_messages($limit = false) {
        global $CFG;
        $res = array();
        $maxerrors = 5;
        if ($limit) {
            // Determine maximum number of errors to show.
            if (isset($CFG->qtype_preg_maxerrorsshown)) {
                $maxerrors = $CFG->qtype_preg_maxerrorsshown;
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
        return $this->ast_root;
    }

    /**
     * Access function to the DST root.
     */
    public function get_dst_root() {
        return $this->dst_root;
    }

    /**
     * Returns max subexpression number.
     */
    public function get_max_subexpr() {
        if ($this->lexer !== null) {
            return $this->lexer->get_max_subexpr();
        } else {
            return 0;
        }
    }

    /**
     * Returns subexpressions map.
     */
    public function get_subexpr_map() {
        if ($this->lexer !== null) {
            return $this->lexer->get_subexpr_map();
        } else {
            return array();
        }
    }

    /**
     * Returns all backreference nodes in the regex.
     */
    public function get_backrefs() {
        if ($this->lexer !== null) {
            return $this->lexer->get_backrefs();
        } else {
            return array();
        }
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
            $acceptresult = $enginenode->accept();
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
                throw new qtype_preg_pathtodot_incorrect('', $CFG->pathtodot);
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

    /**
     * Does lexical and syntaxical analysis of the regex and builds an abstract syntax tree, saving root node in $this->ast_root.
     * @param string regex - regular expression for building tree.
     */
    protected function build_tree($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $this->lexer = new qtype_preg_lexer($pseudofile);
        $this->lexer->set_options($this->options);
        $this->lexer->set_added_at_start($this->addedatstart);

        $this->parser = new qtype_preg_yyParser($this->options);

        while (($token = $this->lexer->nextToken()) !== null) {
            if (!is_array($token)) {
                $this->parser->doParse($token->type, $token->value);
            } else {
                foreach ($token as $curtoken) {
                    $this->parser->doParse($curtoken->type, $curtoken->value);
                }
            }
        }
        $this->parser->doParse(0, 0);

        // Lexer returns errors for an unclosed character set or wrong modifiers.
        $lexerrors = $this->lexer->get_error_nodes();
        foreach ($lexerrors as $node) {
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }

        // Parser contains other errors inside AST nodes.
        $parseerrors = $this->parser->get_error_nodes();
        foreach ($parseerrors as $node) {
            // There can be a specific accepting error.
            if ($node->subtype == qtype_preg_node_error::SUBTYPE_LNU_UNSUPPORTED) {
                $inscription = $node->addinfo;
                $this->errors[] = new qtype_preg_accepting_error($regex, $this->name(), $inscription, $node);
            } else {
                $this->errors[] = new qtype_preg_parsing_error($regex, $node);
            }
        }

        // Set AST and DST roots.
        $this->ast_root = $this->parser->get_root();
        if ($this->ast_root != null) {
            $this->dst_root = $this->from_preg_node(clone $this->ast_root);
        }

        fclose($pseudofile);
    }
}
