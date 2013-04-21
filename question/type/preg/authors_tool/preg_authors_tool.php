<?php
/**
 * Defines abstract class which is common for all authoring tools.
 *
 * @copyright &copy; 2012  Vladimir Ivanov
 * @author Vladimir Ivanov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/type/preg/preg_regex_handler.php');

abstract class qtype_preg_author_tool extends qtype_preg_regex_handler {

    public function generate_json(&$json_array, $regex, $id) {
        if ($regex == '') {
            $this->generate_json_for_empty_regex($json_array, $id);
        } else if ($this->errors_exist() || $this->get_ast_root() == null) {
            $this->generate_json_for_incorrect_regex($json_array, $id);
        } else {
            $this->generate_json_for_correct_regex($json_array, $id);
        }
    }

    protected abstract function generate_json_for_empty_regex(&$json_array, $id);

    protected abstract function generate_json_for_incorrect_regex(&$json_array, $id);

    protected abstract function generate_json_for_correct_regex(&$json_array, $id);
}

?>
