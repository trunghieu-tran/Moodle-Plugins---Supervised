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

class qtype_preg_authoring_tools_options extends qtype_preg_handling_options {
    public $engine = null;
    public $treeorientation = null;
    public $displayas = null;
}

abstract class qtype_preg_authoring_tool extends qtype_preg_regex_handler implements qtype_preg_i_authoring_tool {

    protected static $dotescapecodes = array(34, 38, 44, 60, 62, 91, 92, 93, 123, 124, 125);

    protected static $htmlescapecodes = array(34, 38, 39, 60, 62);

    protected $originalregex = null;

    public function __construct($regex = null, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_authoring_tools_options();
        }
        $options->preserveallnodes = true;

        parent::__construct($regex, $options);

        $this->originalregex = $regex;
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
        if (is_array($extracodes) && count($extracodes) != 0) {
            $codes = array_intersect(self::$dotescapecodes, $extracodes);
        } else {
            $codes = self::$dotescapecodes;
        }

        $result = '';
        $stringtoescape = new qtype_poasquestion_string($stringtoescape);
        for ($i = 0; $i < $stringtoescape->length(); ++$i) {
            $result .= self::escape_char($stringtoescape[$i], $codes);
        }

        return $result;
    }

    public function generate_json(&$json) {
        $json['regex'] = $this->originalregex;
        $json['engine'] = $this->options->engine;
        $json['notation'] = $this->options->notation;
        $json['exactmatch'] = (int)$this->options->exactmatch;
        $json['usecase'] = (int)!$this->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_CASELESS);
        $json['indfirst'] = $this->selectednode !== null ? $this->selectednode->position->indfirst : -2;
        $json['indlast'] = $this->selectednode !== null ? $this->selectednode->position->indlast : -2;
        $json['treeorientation'] = $this->options->treeorientation;
        $json['displayas'] = $this->options->displayas;
        $json['id'] = $this->selectednode !== null ? $this->selectednode->id : -1;  // TODO: remove

        if ($this->originalregex == '') {
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
