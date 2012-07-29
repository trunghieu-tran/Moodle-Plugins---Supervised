<?php
/**
 * Defines abstract class of regular expression handler, which is basically anything that want to work with regex
 * Beeing handler you could benefit from automatic regex parsing, error handling etc
 *
 * @copyright &copy; 2011  Oleg Sychev
 * @author Oleg Sychev, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/poasquestion/poasquestion_string.php');
require_once($CFG->dirroot . '/question/type/preg/preg_lexer.lex.php');
require_once($CFG->dirroot . '/question/type/preg/stringstream/stringstream.php');
require_once($CFG->dirroot . '/question/type/preg/preg_exception.php');
require_once($CFG->dirroot . '/question/type/preg/preg_errors.php');

class qtype_preg_regex_handler {

    /** Regular expression as an object of qtype_poasquestion_string. */
    protected $regex;
    /** Modifiers for regular expression as an object of qtype_poasquestion_string. */
    protected $modifiers;
    /** Regular expression handling options, may be different for different handlers. */
    protected $options;
    /** Regex lexer. */
    protected $lexer;
    /** Regex parser. */
    protected $parser;

    /** The root of the regex abstract syntax tree, consists of qtype_preg_node childs. */
    protected $ast_root;
    /** The root of the regex definite syntax tree, consists of xxx_preg_node childs where xxx is engine name. */
    protected $dst_root;
    /** The error objects array. */
    protected $errors;
    /** Anchoring - object, with 'start' and 'end' logical fields, which are true if all regex is anchored. */
    protected $anchor;

    public function name() {
        return 'preg_regex_handler';
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
    protected function get_engine_node_name($pregname) {
        return 'qtype_preg_' . $this->node_infix() . '_' . $pregname;
    }

    /**
     * Returns notation, actually used by matcher.
     */
    public function used_notation() {
        return 'native'; // TODO - php_preg_matcher should really used PCRE strict notation when conversion will be supported.
    }

    /**
     * Returns string of regular expression modifiers supported by this engine
     */
    public function get_supported_modifiers() {
        return new qtype_poasquestion_string('i'); // Any qtype_preg_matcher who intends to work with this question should support case insensitivity.
    }

    /**
     * Sets regex options.
     * @param options an object containing options to handle the regex.
     */
    public function set_options($options) {
        $this->options = $options;
    }

    /**
     * Parses the regex and does all necessary preprocessing.
     * @param string regex - regular expression to handle.
     * @param string modifiers - modifiers of the regular expression.
     * @param object options - options to handle regex, i.e. any necessary additional parameters.
     */
    public function __construct($regex = null, $modifiers = null, $options = null) {
        $this->errors = array();
        $this->lexer = null;
        $this->parser = null;

        if ($regex === null) {
            return;
        }

        //Are passed modifiers supported?
        if (is_string($modifiers)) {
            $modifiers = new qtype_poasquestion_string($modifiers);
            $supportedmodifiers = $this->get_supported_modifiers();
            for ($i = 0; $i < $modifiers->length(); $i++) {
                $mod = $modifiers[$i];
                if ($supportedmodifiers->contains($mod) === false) {
                    $this->errors[] = new qtype_preg_error_unsupported_modifier($this->name(), $mod->string());
                }
            }
        } else {
            $modifiers = new qtype_poasquestion_string('');
        }

        $this->regex = new qtype_poasquestion_string($regex);
        $this->modifiers = $modifiers;
        $this->set_options($options);
        //do parsing
        if ($this->is_parsing_needed()) {
            $this->build_tree($regex);
        } else {
            $this->ast_root = null;
            //In case with no parsing we should stick to accepting whole regex, not nodes
            $this->accept_regex();
        }
    }

    protected function accept_regex() {
        return true; // Accept anything by default.
    }

    /**
     * Returns subpatterns map.
     */
    public function get_subpattern_map() {
        if ($this->lexer !== null) {
            return $this->lexer->get_subpattern_map();
        } else {
            return array();
        }
    }

    /**
     * Returns max subpattern number.
     */
    public function get_max_subpattern() {
        if ($this->lexer !== null) {
            return $this->lexer->get_max_subpattern();
        } else {
            return 0;
        }
    }

    /**
     * is this engine need a parsing of regular expression?
     * @return bool if parsing needed
     */
    protected function is_parsing_needed() {
        return true;    // Most engines will need parsing.
    }

    /**
     * Was there an errors in regex?
     * @return bool  errors exists
     */
    public function is_error_exists() {
        return (!empty($this->errors));
    }

    /**
     * Returns error messages for regex
     * @return array of error messages
     */
    public function get_errors() {
        $res = array();
        foreach($this->errors as $error) {
            $res[] = $error->errormsg;
        }
        return $res;
    }

    /**
     * Returns errors as objects
     * @return array of errors
     */
    public function get_error_objects() {
        return $this->errors;
    }

    /**
     * Access function to AST root.
     * Used mainly for unit-testing and avoiding re-parsing
     */
    public function get_ast_root() {
        return $this->ast_root;
    }

    public function is_regex_anchored($start = true) {
        if ($start) {
            return $this->anchor->start;
        } else {
            return $this->anchor->end;
        }
    }

    /**
     * Is a preg_node_... or a preg_leaf_... supported by the engine?
     * Returns true if node is supported or user interface string describing
     *   what properties of node isn't supported.
     */
    protected function is_preg_node_acceptable($pregnode) {
        return false;    // Should be overloaded by child classes
    }

    protected function look_for_circumflex($root) {
        if (is_a($root, 'qtype_preg_leaf')) {
            return ($root->subtype === qtype_preg_leaf_assert::SUBTYPE_CIRCUMFLEX);
        } else if (is_a($root, 'qtype_preg_node_infinite_quant')) {
            $operand = $root->operands[0];
            return ($root->leftborder === 0 && is_a($operand, 'qtype_preg_leaf_charset') &&
                    count($operand->flags) > 0 && $operand->flags[0][0]->data === qtype_preg_charset_flag::PRIN);
        } else if (is_a($root, 'qtype_preg_node_concat') || is_a($root, 'qtype_preg_node_subpatt')) {
            return $this->look_for_circumflex($root->operands[0]);
        } else if (is_a($root, 'qtype_preg_node_alt')) {
            $result = true;
            foreach ($root->operands as $operand) {
                $result = $result && $this->look_for_circumflex($operand);
            }
            return $result;
        }
        return false;
    }

    /**
     * Fill anchor field to show if regex is anchored using ast_root
     *
     * If all top-level alternatives starts from ^ or .* then expression is anchored from start (i.e. if matching from start failed, no other matches possible)
     * If all top-level alternatives ends on $ or .* then expression is anchored from end (i.e. if matching from start failed, no other matches possible)
     */
    public function look_for_anchors() {
        //TODO(performance) - write real code, for now think no anchoring is in expressions
        $this->anchor = new stdClass;
        $this->anchor->start = $this->look_for_circumflex($this->ast_root);
        $this->anchor->end = false;
    }

    /**
     * Does lexical and syntaxical analysis of the regex and builds an abstract syntax tree, saving root node in $this->ast_root.
     * @param string regex - regular expression for building tree.
     */
    protected function build_tree($regex) {
        StringStreamController::createRef('regex', $regex);
        $pseudofile = fopen('string://regex', 'r');
        $this->lexer = new qtype_preg_lexer($pseudofile);
        $this->lexer->matcher = $this;        // Set matcher field, to allow creating qtype_preg_leaf nodes that require interaction with matcher
        $this->lexer->mod_top_opt($this->modifiers, new qtype_poasquestion_string(''));
        $this->parser = new preg_parser_yyParser;
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
        // Lexer returns errors for an unclosed character set or wrong modifiers: they don't create AST nodes.
        $lexerrors = $this->lexer->get_errors();
        foreach ($lexerrors as $node) {
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }
        // Parser contains other errors inside AST nodes.
        $parseerrors = $this->parser->get_error_nodes();
        foreach($parseerrors as $node) {
            $this->errors[] = new qtype_preg_parsing_error($regex, $node);
        }
        // Set the AST root anyway, but the DST root only of there are no errors.
        $this->ast_root = $this->parser->get_root();
        if (count($this->errors) === 0) {
            $this->dst_root = $this->from_preg_node($this->ast_root);
            $this->look_for_anchors();
        }
        fclose($pseudofile);
    }

    /**
     * Copy Abstract Syntax Tree from another qtype_preg_regex_handler class and build DST on it
     *
     * Create handler with no parameters, than call this function to avoid re-parsing if you have
     *   two handlers working on one regex.
     */
    public function get_tree_from_another_handler($handler) {
        $this->errors = $handler->get_error_objects();
        if (!$this->is_error_exists()) {
            $srcroot = $handler->get_ast_root();
            $this->ast_root = clone $srcroot;
            $this->dst_root = $this->from_preg_node($this->ast_root);
            $this->look_for_anchors();
        }
    }

    /**
     * Definite syntax tree (DST) node factory creates node objects for given engine from abstract syntax tree
     * @param pregnode qtype_preg_node child class instance
     * @return corresponding xxx_preg_node child class instance
     */
    public function &from_preg_node($pregnode) {
        if (is_a($pregnode,'qtype_preg_node')) {//checking that the node isn't already converted
            $enginenodename = $this->get_engine_node_name($pregnode->name());
            if (class_exists($enginenodename)) {
                $enginenode = new $enginenodename($pregnode, $this);
                $acceptresult = $enginenode->accept();
                if ($acceptresult !== true && !array_key_exists($enginenodename,  $this->errors)) {//highlighting first occurence of unaccepted node
                    $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast));
                }
            } else {
                $enginenode = $pregnode;
                $acceptresult = $this->is_preg_node_acceptable($pregnode);
                if ($acceptresult !== true && !array_key_exists($enginenodename,  $this->errors)) {//highlighting first occurence of unaccepted node
                    $this->errors[$enginenodename] = new qtype_preg_accepting_error($this->regex, $this->name(), $acceptresult, array('start' => $pregnode->indfirst, 'end' => $pregnode->indlast));
                }
            }
            return $enginenode;
        } else {
            return $pregnode;
        }
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
     * Runs dot of graphviz on the given .dot file.
     * @param dotscript a string containing the dot script.
     * @param filename the absolute path to the resulting image file.
     */
    public static function execute_dot($dotscript, $filename) {
        $type = pathinfo($filename, PATHINFO_EXTENSION);
        $cmd = escapeshellarg(!empty($CFG->pathtodot) ?  $CFG->pathtodot : 'dot') . " -T$type -o\"$filename\"";
        if (strpos(strtolower(php_uname()), 'windows') !== false) {
            // There is a weird bug on Windows when calling proc_open with filename containing spaces, so use exec().
            $dotfilename = $filename . '.dot';
            $dotfile = fopen($dotfilename, 'w');
            fwrite($dotfile, $dotscript);
            fclose($dotfile);
            $cmd .= " -Kdot $dotfilename";
            exec($cmd);
            unlink($dotfilename);
        } else {
            global $CFG;
            $descriptorspec = array(0 => array('pipe', 'r'), // Stdin is a pipe that the child will read from.
                                    1 => array('pipe', 'w'), // Stdout is a pipe that the child will write to.
                                    2 => array('pipe', 'w')  // Stderr is a pipe that the child will write to.
                                    );
            $process = proc_open($cmd, $descriptorspec, $pipes, dirname($filename), array());
            if (is_resource($process)) {
                fwrite($pipes[0], $dotscript);
                fclose($pipes[0]);
                $err = stream_get_contents($pipes[2]);
                if (!empty($err)) {
                    //echo "failed to execute cmd: \"$cmd\". stderr: `$err'\n";
                }
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
            } else {
                //echo "failed to execute cmd \"$cmd\"";
            }
        }
    }
}
