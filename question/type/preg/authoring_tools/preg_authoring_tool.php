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
