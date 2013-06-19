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
     * @param code - character's code.
     * @param codes - codes which should be escaped.
     * @return escaped character.
     */
    public static function escape_char_by_code($code, $codes) {

        if (in_array($code, array_keys($codes))) {
            return '&#'.$code.';';
        } else {
            return chr($code);
        }
    }

    /**
     * Escaping a character.
     * @param code - character to escape.
     * @param codes - codes which should be escaped.
     * @return escaped character.
     */
    public static function escape_char($character, $codes) {
        return self::escape_char_by_code(ord($character), $codes);
    }

    /**
     * Escaping a string
     * @param stringToEscape - string to escape.
     * @param extraCodes - extra codes which should be escaped.
     * @return escaped string.
     */
    public static function escape_string($stringToEscape, $extraCodes = NULL) {

        if (is_array($extraCodes) && sizeof($extraCodes) != 0) {
            $codes = array_intersect(self::$codes, $extraCodes);
        } else {
            $codes = self::$codes;
        }

        $result = '';
        for ($i = 0; $i < strlen($stringToEscape); ++$i) {
            $result .= self::escape_char($stringToEscape[$i], $codes);
        }

        return $result;
    }

    protected static $codes = array(34 /*=> '"'*/,
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
