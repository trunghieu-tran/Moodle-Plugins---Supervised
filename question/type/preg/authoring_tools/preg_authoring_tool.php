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

require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');
require_once($CFG->dirroot . '/question/type/preg/fa_matcher/fa_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/php_preg_matcher/php_preg_matcher.php');
require_once($CFG->dirroot . '/question/type/preg/preg_notations.php');

interface qtype_preg_i_authoring_tool {

    /**
     * Returns the key for accessing this tool's data in JSON array.
     */
    public function json_key();

    /**
     * Generates a json array with this tool's data.
     */
    public function generate_json();

    /**
     * Generates html with this tool's data.
     */
    public function generate_html();

    public function data_for_accepted_regex();

    public function data_for_unaccepted_regex();

    public function data_for_empty_regex();
}

class qtype_preg_authoring_tools_options extends qtype_preg_handling_options {
    public $engine = null;
    public $treeorientation = null;
    public $displayas = null;
    public $foldcoords = null;
    public $treeisfold = null;
}

abstract class qtype_preg_authoring_tool extends qtype_preg_regex_handler implements qtype_preg_i_authoring_tool {

    protected static $htmlspecialcodes = array(34, 38, 44, 60, 62, 91, 92, 93, 123, 124, 125);

    protected static $specialcodes = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16,
                                           17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32,
                                           0x7F, 0xA0, 0xAD, 0x2002, 0x2003, 0x2009, 0x200C, 0x200D);

    public function __construct($regex = null, $options = null) {
        if ($options === null) {
            $options = new qtype_preg_authoring_tools_options();
        }
        $options->preserveallnodes = true;
        parent::__construct($regex, $options);
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
     * Converts a character so it's representable in HTML.
     */
    public static function char_to_html($char) {
        $code = core_text::utf8ord($char);
        if (in_array($code, self::$htmlspecialcodes)) {
            return '&#' . $code . ';';
        }
        return $char;
    }

    /**
     * Converts a string so it's representable in HTML.
     */
    public static function string_to_html($string) {
        $result = '';
        $string = new qtype_poasquestion\string($string);
        for ($i = 0; $i < $string->length(); ++$i) {
            $result .= self::char_to_html($string[$i]);
        }
        return $result;
    }

    public static function escape_characters($string, $chars) {
        $result = '';
        $string = new qtype_poasquestion\string($string);
        for ($i = 0; $i < $string->length(); ++$i) {
            $char = $string[$i];
            $result .= in_array($char, $chars) ? "\\$char" : $char;
        }
        return $result;
    }

    public static function replace_special_characters($string) {
        $result = '';
        $string = new qtype_poasquestion\string($string);
        for ($i = 0; $i < $string->length(); ++$i) {
            $char = $string[$i];
            $code = core_text::utf8ord($char);
            if (in_array($code, self::$specialcodes)) {
                $hex = core_text::strtoupper(dechex($code));
                $result .= get_string('description_char' . $hex, 'qtype_preg');
            } else {
                $result .= $char;
            }
        }
        return $result;
    }

    public static function userinscription_to_string($ui, $explaincodes = true) {
        $data = new qtype_poasquestion\string($ui->data);

        // Is it a range?
        if ($ui->is_character_range()) {
            $mpos = core_text::strpos($data, '-');
            $left = $data->substring(0, $mpos)->string();
            $right = $data->substring($mpos + 1)->string();

            $uileft = new qtype_preg_userinscription($left);
            $uiright = new qtype_preg_userinscription($right);

            // Make a recursive call; won't get here next time.
            $a = new stdClass;
            $a->start = self::userinscription_to_string($uileft, $explaincodes);
            $a->end = self::userinscription_to_string($uiright, $explaincodes);
            return get_string('description_range', 'qtype_preg', $a);
        }

        // Is it an escape-sequence for a character?
        if ($ui->is_single_escape_sequence_character()) {
            $code = qtype_preg_lexer::code_of_char_escape_sequence($data->string());
            $hex = core_text::strtoupper(dechex($code));
            return get_string('description_char' . $hex, 'qtype_preg');
        }

        // Is it \cx or \ddd or \ddd or \x{hh} escape sequence?
        if ($ui->is_single_escape_sequence_character_c() || $ui->is_single_escape_sequence_character_oct() || $ui->is_single_escape_sequence_character_hex()) {
            $code = qtype_preg_lexer::code_of_char_escape_sequence($data->string());
            $tmp = new qtype_preg_userinscription(core_text::code2utf8($code));
            $a = new stdClass;
            $a->code = core_text::strtoupper(dechex($code));
            $a->char = self::userinscription_to_string($tmp);
            return $explaincodes ? get_string('description_char_16value', 'qtype_preg', $a) : $a->char;
        }

        // Is it another flag or POSIX class?
        if ($ui->isflag !== null) {
            return get_string($ui->lang_key(true), 'qtype_preg');
        }

        if ($data != '\\' && $data[0] == '\\') {
            $data = $data->substring(1);
        }

        $code = core_text::utf8ord($data[0]);
        if ($data->length() == 1 && $code <= 32) {
            $hex = core_text::strtoupper(dechex($code));
            return get_string('description_char' . $hex, 'qtype_preg');
        }

        return $data->string();
    }

    public function generate_json() {
        $json = array();
        $json['regex'] = $this->regex->string();
        $json['engine'] = $this->options->engine;
        $json['notation'] = $this->options->notation;
        $json['exactmatch'] = (int)$this->options->exactmatch;
        $json['usecase'] = (int)!$this->options->is_modifier_set(qtype_preg_handling_options::MODIFIER_CASELESS);
        $json['indfirst'] = $this->selectednode !== null ? $this->selectednode->position->indfirst : -2;
        $json['indlast'] = $this->selectednode !== null ? $this->selectednode->position->indlast : -2;
        $json['treeorientation'] = $this->options->treeorientation;
        $json['displayas'] = $this->options->displayas;

        if ($this->regex->string() == '') {
            $json[$this->json_key()] = $this->data_for_empty_regex();
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            $json[$this->json_key()] = $this->data_for_unaccepted_regex();
        } else {
            $json[$this->json_key()] = $this->data_for_accepted_regex();
        }

        return $json;
    }

    public function data_for_empty_regex() {
        return '';
    }

    public function data_for_unaccepted_regex() {
        $a =  core_text::strtolower(get_string($this->name(), 'qtype_preg'));
        $result = get_string('error_duringauthoringtool', 'qtype_preg', $a);
        $result .= implode('<br />', $this->get_error_messages());
        return $result;
    }

    public function get_regex() {
        return $this->regex->string();
    }
}

abstract class qtype_preg_dotbased_authoring_tool extends qtype_preg_authoring_tool {

    public function __construct($regex = null, $options = null) {
        parent::__construct($regex, $options);
    }

    // Overloaded for some exceptions handling.
    public function generate_json() {
        try {
            return parent::generate_json();
        } catch (Exception $e) {
            // Something is wrong with graphviz.
            $json = array();
            $a = new stdClass;
            $a->name = core_text::strtolower(get_string($this->name(), 'qtype_preg'));
            if (is_a($e, 'qtype_preg_pathtodot_empty')) {
                $json[$this->json_key()] = get_string('pathtodotempty', 'qtype_preg', $a);
            } else if (is_a($e, 'qtype_preg_pathtodot_incorrect')) {
                $a->pathtodot = $e->a;
                $json[$this->json_key()] = get_string('pathtodotincorrect', 'qtype_preg', $a);
            } else if (is_a($e, 'qtype_preg_dot_error')) {
                $json[$this->json_key()] = get_string('doterror', 'qtype_preg', $a);
            } else {
                // It's not our exception, let it go.
                throw $e;
            }
            return $json;
        }
    }
}
