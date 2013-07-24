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

abstract class qtype_preg_authoring_tool extends qtype_preg_regex_handler {

    protected $linefirst = null; 
    protected $linelast = null; 
    protected $indfirst = null; 
    protected $indlast = null;
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

    public function build_dst() {
        if ($this->linefirst && $this->linelast && $this->indfirst && $this->indlast) {
            // TODO
        }
        parent::build_dst();
    }

    /**
     * Escaping a character by its code.
     * @param int $code - character's code.
     * @param array|string $codesormode - array of codes which should be escaped
     * or string containing escape mode: 'html' or 'dot'.
     * @return string escaped character.
     */
    public static function escape_char_by_code($code, $codesormode = NULL) {
        if ($codesormode === NULL) {
            $codesormode = self::$codes;
        }
        if (is_string($codesormode)) {
            if ($codesormode==='html') {
                $codesormode = self::$htmlescapecodes;
            } else if ($codesormode==='dot') {
                $codesormode = self::$dotescapecodes;
            }
        }
        if (in_array($code, $codesormode)) {
            return '&#'.$code.';';
        } else {
            return textlib::code2utf8($code);
        }
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
     * @param stringToEscape - string to escape.
     * @param extraCodes - extra codes which should be escaped.
     * @return escaped string.
     */
    public static function escape_string($stringToEscape, $extraCodes = NULL) {

        if (is_array($extraCodes) && sizeof($extraCodes) != 0) {
            $codes = array_intersect(self::$dotescapecodes, $extraCodes);
        } else {
            $codes = self::$dotescapecodes;
        }

        $result = '';
        for ($i = 0; $i < strlen($stringToEscape); ++$i) {
            $result .= self::escape_char($stringToEscape[$i], $codes);
        }

        return $result;
    }

    protected static $dotescapecodes = array(34 /*=> '"'*/,
                                    38 /*=> '&'*/,
                                    44 /*=> ','*/,
                                    60 /*=> '<'*/,
                                    62 /*=> '>'*/,
                                    91 /*=> '['*/,
                                    93 /*=> ']'*/,
                                    123 /*=> '{'*/,
                                    124 /*=> '|'*/,
                                    125 /*=> '}'*/,
                                    92 /*=> '\\'*/
                                   );

    protected static $htmlescapecodes = array(34, 38, 39, 60, 62);

    /**
     * Generates a json-array corresponding to $regex and core of tool.
     * @param json_array - output array with json
     * @param regex - our regular expression
     * @param id - identifier of node which will be picked out in image.
     */
    public function generate_json(&$json_array, $regex, $id) {
        $json_array['regex'] = $regex;
        $json_array['id'] = $id;
        if ($regex == '') {
            $this->generate_json_for_empty_regex($json_array, $id);
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            $this->generate_json_for_unaccepted_regex($json_array, $id);
        } else {
            $this->generate_json_for_accepted_regex($json_array, $id);
        }
    }

    protected abstract function json_key();

    protected abstract function generate_json_for_empty_regex(&$json_array, $id);

    protected abstract function generate_json_for_unaccepted_regex(&$json_array, $id);

    protected abstract function generate_json_for_accepted_regex(&$json_array, $id);

}

abstract class qtype_preg_dotbased_authoring_tool extends qtype_preg_authoring_tool {

    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
        // TODO
    }

}

?>
