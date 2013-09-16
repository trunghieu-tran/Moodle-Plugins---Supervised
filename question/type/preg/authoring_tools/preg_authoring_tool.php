<?php
/**
 * Defines abstract class which is common for all authoring tools.
 *
 * @copyright &copy; 2012 Oleg Sychev, Volgograd State Technical University
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/nfa_matcher/nfa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/dfa_matcher/dfa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/php_preg_matcher/php_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');

interface qtype_preg_i_authoring_tool {

    /**
     * Returns the key for accessing this tool's data in JSON array.
     */
    public function json_key();

    /**
     * Generates a json array with this tool's data. Should call the methods below.
     * @param json - output JSON array.
     */
    public function generate_json(&$json);

    public function generate_json_for_accepted_regex(&$json);

    public function generate_json_for_unaccepted_regex(&$json);

    public function generate_json_for_empty_regex(&$json);
}

abstract class qtype_preg_authoring_tool extends qtype_preg_regex_handler implements qtype_preg_i_authoring_tool {

    protected static $dotescapecodes = array(34, 38, 44, 60, 62, 91, 92, 93, 123, 124, 125);

    protected static $htmlescapecodes = array(34, 38, 39, 60, 62);

    // Regex text selection borders, an instance of qtype_preg_position.
    protected $selection = null;
    // Updated value of the selection first index.
    protected $newindfirst = -1;
    // Updated value of the selection last index.
    protected $newindlast = -1;

    protected $selectednode = null;

    public function __construct($regex = null, $options = null, $engine = null, $notation = null, $selection = null) {
        //TODO: move to qtype_preg_regex_handler
        if ($regex === null) {
            return;
        }
        if ($options === null) {
            $options = new qtype_preg_handling_options();
        }
        $options->preserveallnodes = true;
        // Convert to actually used notation if necessary.
        if ($engine !== null && $notation !== null) {
            $engineclass = 'qtype_preg_'.$engine;
            $queryengine = new $engineclass;
            $usednotation = $queryengine->used_notation();
            if ($notation != $usednotation) {
                $notationclass = 'qtype_preg_notation_'.$notation;
                $notationobj = new $notationclass($regex);
                $regex = $notationobj->convert_regex($usednotation);
            }
        }

        parent::__construct($regex, $options);

        $this->selection = $selection !== null
                         ? $selection
                         : new qtype_preg_position();

        $idcounter = $this->parser->get_max_id() + 1;
        $this->newindfirst = $this->selection->indfirst;
        $this->newindlast = $this->selection->indlast;
        $this->selectednode = $this->ast_root->node_by_regex_fragment($this->newindfirst, $this->newindlast, $idcounter);
        $this->build_dst();
    }

    /**
     * Overloaded since parsing errors are normal for authoring tools.
     */
    public function get_errors() {
        $result = array();
        foreach (parent::get_errors() as $error) {
            if (is_a($error, 'qtype_preg_accepting_error') || is_a($error, 'qtype_preg_modifier_error')) {
                $result[] = $error;
            }
        }
        return $result;
    }

    /**
     * Escaping a character by its code.
     * @param int $code - character's code.
     * @param array|string $codesormode - array of codes which should be escaped
     * or string containing escape mode: 'html' or 'dot'.
     * @return string escaped character.
     */
    public static function escape_char_by_code($code, $codesormode = null) {
        if ($codesormode === 'html') {
            $codesormode = self::$htmlescapecodes;
        } else if ($codesormode === 'dot' || $codesormode === null) {
            $codesormode = self::$dotescapecodes;
        }

        if (in_array($code, $codesormode)) {
            return '&#' . $code . ';';
        }
        return textlib::code2utf8($code);
    }

    /**
     * Escaping a character.
     * @param code - character to escape.
     * @param codes - codes which should be escaped.
     * @return escaped character.
     */
    public static function escape_char($character, $codes) {
        return self::escape_char_by_code(textlib::utf8ord($character), $codes);
    }

    /**
     * Escaping a string
     * @param stringtoescape - string to escape.
     * @param extracodes - extra codes which should be escaped.
     * @return escaped string.
     */
    public static function escape_string($stringtoescape, $extracodes = null) {
        if (is_array($extracodes) && sizeof($extracodes) != 0) {
            $codes = array_intersect(self::$dotescapecodes, $extracodes);
        } else {
            $codes = self::$dotescapecodes;
        }

        $result = '';
        for ($i = 0; $i < textlib::strlen($stringtoescape); ++$i) {
            $result .= self::escape_char($stringtoescape[$i], $codes);
        }

        return $result;
    }

    public function generate_json(&$json) {
        $json['regex'] = $this->regex->string();
        $json['id'] = $this->selectednode !== null ? $this->selectednode->id : -1;  // TODO: remove
        $json['newindfirst'] = $this->newindfirst;
        $json['newindlast'] = $this->newindlast;

        if ($this->regex == '') {
            $this->generate_json_for_empty_regex($json);
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            $this->generate_json_for_unaccepted_regex($json);
        } else {
            $this->generate_json_for_accepted_regex($json);
        }
    }

    public function generate_json_for_empty_regex(&$json) {
        $json[$this->json_key()] = '';
    }

    public function generate_json_for_unaccepted_regex(&$json) {
        $a =  textlib::strtolower(get_string($this->name(), 'qtype_preg'));
        $result = get_string('error_duringauthoringtool', 'qtype_preg', $a);
        foreach ($this->get_error_messages(true) as $error) {
            $result .= '<br />' . $error;
        }
        $json[$this->json_key()] = $result;
    }
}

abstract class qtype_preg_dotbased_authoring_tool extends qtype_preg_authoring_tool {

    // Overloaded for some exceptions handling.
    public function generate_json(&$json) {
        try {
            parent::generate_json($json);
        } catch (Exception $e) {
            // Something is wrong with graphviz.
            $a = new stdClass;
            $a->name = textlib::strtolower(get_string($this->name(), 'qtype_preg'));
            if (is_a($e, 'qtype_preg_pathtodot_empty')) {
                $json[$this->json_key()] = get_string('pathtodotempty', 'qtype_preg', $a);
            } else {
                $a->pathtodot = $e->a;
                $json[$this->json_key()] = get_string('pathtodotincorrect', 'qtype_preg', $a);
            }
        }
    }
}

?>
