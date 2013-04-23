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
require_once($CFG->dirroot . '/question/type/preg/authoring_tools/preg_authoring_tool.php');

abstract class qtype_preg_dotbased_authoring_tool extends qtype_preg_authoring_tool {

    protected function add_image_dimensions_to_json(&$json_array, $raw_image_data) {
    	// getimagesizefromstring is only available from PHP 5.4 and later, so use the old good StringStream.
    	StringStreamController::createRef('image', $raw_image_data);

    	$dimensions = getimagesize('string://image');
    	$json_array[$this->json_key() . '_width'] = $dimensions[0];
    	$json_array[$this->json_key() . '_height'] = $dimensions[1];
    }

    protected function generate_json_for_unaccepted_regex(&$json_array, $id) {
    	// TODO
    }

}

?>
